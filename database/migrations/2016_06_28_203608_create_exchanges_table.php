<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->increments('id');
	    $table->enum('currency_type', ['USD', 'GBP', 'EUR', 'KES']);

            // BIGINTs store x*10 values
	    $table->bigInteger('currency_rate');
	    $table->bigInteger('amount_purchased');
	    $table->bigInteger('amount_paid');
	    $table->bigInteger('surcharge_amount');

	    $table->integer('surcharge_percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('exchanges');
    }
}
