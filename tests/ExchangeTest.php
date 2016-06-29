<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ExchangeTest extends TestCase
{
    /**
     * Tests for correct response on /exchange
     *
     * @return void
     */
    public function testValid()
    {
	$response = json_decode($this->call('POST', '/exchange', [
		'currency' => 'eur',
		'amount'   => '500'
	])->getContent(), true);
	
	$this->assertEquals('ok', $response['status']);

	$this->assertArrayHasKey('id', $response['order']);
	$this->assertArrayHasKey('currency_type', $response['order']);
	$this->assertArrayHasKey('currency_rate', $response['order']);
	$this->assertArrayHasKey('amount_purchased', $response['order']);
	$this->assertArrayHasKey('amount_paid', $response['order']);
	$this->assertArrayHasKey('surcharge_amount', $response['order']);
	$this->assertArrayHasKey('surcharge_percentage', $response['order']);
	$this->assertArrayHasKey('discount', $response['order']);
	$this->assertArrayHasKey('created_at', $response['order']);

	$this->seeInDatabase('exchanges', ['id' => $response['order']['id']]);
    }

    /**
     * Tests for invalid response on /exchange
     */
    public function testInvalidCurrency()
    {
	$response = json_decode($this->call('POST', '/exchange', [
		'currency' => 'yen',
		'amount'   => '500'
	])->getContent(), true);
	
	$this->assertEquals('error', $response['status']);
	$this->assertArrayHasKey('errors', $response);
    }

    /**
     * Tests for invalid response on /exchange
     */
    public function testInvalidAmount()
    {
	$response = json_decode($this->call('POST', '/exchange', [
		'currency' => 'yen',
		'amount'   => -5
	])->getContent(), true);
	
	$this->assertEquals('error', $response['status']);
	$this->assertArrayHasKey('errors', $response);
    }
}

