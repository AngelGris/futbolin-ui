<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\CreditItem;
use MercadoPago\SDK as MercadoPago;
use MercadoPago\Payment as MercadoPago_Payment;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Session;

class PaymentController extends Controller
{
    private $paypal_apiContext;
    private $paypal_mode;
    private $paypal_client_id;
    private $paypal_secret;

    // Create a new instance with our paypal credentials
    public function __construct()
    {
        // Detect if we are running PayPal in live mode or sandbox
        if(config('paypal.settings.mode') == 'live'){
            $this->paypal_client_id = config('paypal.live_client_id');
            $this->paypal_secret = config('paypal.live_secret');
        } else {
            $this->paypal_client_id = config('paypal.sandbox_client_id');
            $this->paypal_secret = config('paypal.sandbox_secret');
        }

        // Set the Paypal API Context/Credentials
        $this->paypal_apiContext = new ApiContext(new OAuthTokenCredential($this->paypal_client_id, $this->paypal_secret));
        $this->paypal_apiContext->setConfig(config('paypal.settings'));
    }

    public function checkout(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'method' => 'required',
        ]);

        $package = CreditItem::find($request->input('id'));

        if ($request->input('method') == 'PP') {
            return $this->checkoutPaypal($package->id, $package->name, \config('constants.CURRENCY'), $package->price);
        } else {
            return $this->checkoutMercadopago($package->id, $package->name, \config('constants.CURRENCY'), $package->price);
        }
    }

    private function checkoutPaypal($item_id, $title, $currency, $total_amount, $quantity = 1)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        session([
            'paypal_currency'      => $currency,
            'paypal_total_amount'  => $total_amount,
        ]);

        // Valid when selling 1 item only
        $item = new Item();
        $item->setName($title)
            ->setCurrency($currency)
            ->setQuantity($quantity)
            ->setSku($item_id)
            ->setPrice($total_amount);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $details = new Details();
        $details->setSubtotal($total_amount);

        $amount = new Amount();
        $amount->setTotal($total_amount)
            ->setCurrency($currency)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($title);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('payment.paypal.process'))
            ->setCancelUrl(route('shopping.credits'));

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->paypal_apiContext);
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // This will print the detailed information on the exception.
            //REALLY HELPFUL FOR DEBUGGING
            echo $ex->getData();
        }

        $approvalUrl = $payment->getApprovalLink();

        return redirect($approvalUrl);
    }

    public function processMercadopago(Request $request)
    {
        // TODO
    }

    public function processPaypal(Request $request)
    {
        if ($request->input('paymentId', false) || $request->input('PayerID', false)) {
            $paymentId = $request->input('paymentId');
            $payment = Payment::get($paymentId, $this->paypal_apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($request->input('PayerID'));

            $transaction = new Transaction();
            $amount = new Amount();

            $amount->setCurrency(session('paypal_currency'));
            $amount->setTotal(session('paypal_total_amount'));
            $transaction->setAmount($amount);

            $request->session()->forget('paypal_currency');
            $request->session()->forget('paypal_total_amount');

            $execution->addTransaction($transaction);

            try {
                $result = $payment->execute($execution, $this->paypal_apiContext);

                try {
                    $payment = Payment::get($paymentId, $this->paypal_apiContext);
                    $credit_item_id = $payment->transactions[0]->item_list->items[0]->sku;
                    $amount_total = $payment->transactions[0]->item_list->items[0]->price;
                    $amount_earnings = number_format($amount_total - (0.044 * $amount_total) - 0.3, 2);
                    $user = Auth::user();

                    if ($payment->state == 'approved') {
                        $payment_status_id = 2;
                    } else {
                        $payment_status_id = 1;
                    }

                    $description = <<< 'EOD'
id: %s
state: %s
email: %s
payer_id: %s
EOD;
                    $description = sprintf(
                        $description,
                        $payment->id,
                        $payment->state,
                        $payment->payer->payer_info->email,
                        $payment->payer->payer_info->payer_id
                    );

                    \App\Payment::create([
                        'user_id'           => $user->id,
                        'credit_item_id'    => $credit_item_id,
                        'method'            => 'PP',
                        'amount_total'      => $amount_total,
                        'amount_earnings'   => $amount_earnings,
                        'payment_status_id' => $payment_status_id,
                        'description'       => $description
                    ]);

                    switch($payment_status_id) {
                        case 2:
                            $credits = $user->addCreditItem($payment->transactions[0]->item_list->items[0]->sku);
                            Session::flash('flash_success', $credits . ' Fúlbos fueron acreditados. Transacción finalizada.');
                            return redirect()->route('shopping');
                            break;
                        case 3:
                            Session::flash('flash_warning', 'Pago rechazado por PayPal.');
                            return redirect()->route('shopping.credits');
                            break;
                        default:
                            break;
                    }
                } catch (Exception $e) {
                    dd($e);
                }
            } catch (Exception $e) {
                dd($ex);
            }
        } else {
            Session::flash('flash_danger', 'Operación cancelada.');
            return redirect()->route('shopping.credits');
        }
    }
}