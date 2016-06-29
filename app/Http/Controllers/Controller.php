<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use Cache;

class Controller extends BaseController
{

    /**
     * Stores exchange rates indexed by code
     * @var array
     */
    protected $exchangeRates = [];

    /**
     * Fetches and caches exchange rate data
     *
     * @return void
     */
    public function __construct()
    {
        // Fetch exchange rates from Yahoo Finance API and cache for 5m
        $this->exchangeRates = Cache::remember('rates', 300, function() 
        {
            $yahooRates = 'http://query.yahooapis.com/v1/public/yql?q=select * from yahoo.finance.xchange where pair in ("ZARUSD", "ZARGBP", "ZAREUR", "ZARKES")&format=json&env=store://datatables.org/alltableswithkeys';

            $client = new Client;

            $response = json_decode($client->get($yahooRates)->getBody(), true);

            return array_reduce(
                current($response['query']['results']), 
                function(&$carry, $currency) 
                {
                    $carry[substr($currency['Name'], -3)] = (float) $currency['Rate'];
                    return $carry;
                },
                []
            );
	});

    }
}

