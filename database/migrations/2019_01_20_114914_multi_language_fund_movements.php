<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MultiLanguageFundMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_fund_movements', function(Blueprint $table) {
            $table->tinyInteger('movement_type')->unsigned()->default(0)->after('balance');
            $table->string('variables', 255)->after('movement_type');
        });

        // Update 'Ingresos por venta de entradas'
        \App\TeamFundMovement::where('description', 'Ingresos por venta de entradas')->update(['movement_type' => \Config::get('constants.MONEY_MOVEMENTS_INCOME_SELLING_TICKETS'), 'variables' => json_encode([])]);

        // Update 'Venta de Fúlbos'
        \App\TeamFundMovement::where('description', 'Venta de Fúlbos')->update(['movement_type' => \Config::get('constants.MONEY_MOVEMENTS_INCOME_SELLING_CREDITS'), 'variables' => json_encode([])]);

        // Update 'Pago de salarios'
        \App\TeamFundMovement::where('description', 'Pago de salarios')->update(['movement_type' => \Config::get('constants.MONEY_MOVEMENTS_OUTCOME_SALARIES_PAID'), 'variables' => json_encode([])]);

        // Update movements with variables
        $movements = \App\TeamFundMovement::where('movement_type', 0)->get();
        foreach ($movements AS $movement) {
            if (starts_with($movement->description, 'Compra de ')) {
                $movement->movement_type = \Config::get('constants.MONEY_MOVEMENTS_OUTCOME_BUYING_PLAYER');
                $movement->variables = ['player' => str_replace('Compra de ', '', $movement->description)];
            } elseif (starts_with($movement->description, 'Rescisión de contrato de ')) {
                $movement->movement_type = \Config::get('constants.MONEY_MOVEMENTS_OUTCOME_CONTRACT_TERMINATED');
                $movement->variables = ['player' => str_replace('Rescisión de contrato de ', '', $movement->description)];
            } elseif (starts_with($movement->description, 'Venta de ')) {
                $movement->movement_type = \Config::get('constants.MONEY_MOVEMENTS_INCOME_SELLING_PLAYER');
                $movement->variables = ['player' => str_replace('Venta de ', '', $movement->description)];
            }

            $movement->save();
        }

        Schema::table('team_fund_movements', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_fund_movements', function (Blueprint $table) {
            $table->dropColumn(['movement_type', 'variables']);
        });
    }
}
