<?php
return [
    /**
     * Set our Sandbox and Live credentials
     */
    'mode' => env('MERCADOPAGO_MODE', ''),
    'sandbox_access_token' => env('MERCADOPAGO_SANDBOX_ACCESS_TOKEN', ''),
    'live_access_token' => env('MERCADOPAGO_LIVE_ACCESS_TOKEN', ''),
];