<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class CurrecyTest extends TestCase
{
    /**
     * Tests for correct response on /currency
     *
     * @return void
     */
    public function testListing()
    {
	$response = json_decode($this->call('GET', '/currency')->getContent(), true);
	
	$this->assertEquals('ok', $response['status']);

	$this->assertArrayHasKey('USD', $response['rates']);
	$this->assertArrayHasKey('EUR', $response['rates']);
	$this->assertArrayHasKey('GBP', $response['rates']);
	$this->assertArrayHasKey('KES', $response['rates']);
    }

    /**
     * Tests for correct response on /currency/{code}
     *
     * @return void
     */
    public function testAcceptsValid()
    {
	$response = json_decode($this->call('GET', '/currency/usd')->getContent(), true);
	
	$this->assertEquals('ok', $response['status']);
	$this->assertEquals('usd', $response['code']);

	$this->assertArrayHasKey('rate', $response);
    }

    /**
     * Tests for invalid response on /currency/{code}
     *
     * @return void
     */
    public function testFailsInvalid()
    {
	$response = json_decode($this->call('GET', '/currency/yen')->getContent(), true);
	
	$this->assertEquals('error', $response['status']);
	$this->assertArrayHasKey('errors', $response);
    }
}
