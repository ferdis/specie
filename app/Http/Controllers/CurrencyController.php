<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class CurrencyController extends Controller
{
    /**
     * Retrieves a list of available rates
     *
     * @return Response
     */
    public function getAll() {
        return response()->json([
            'status' => 'ok',
            'rates'	 => $this->exchangeRates
        ]);
    }


    /**
     * Retrieves a single exchange rate
     *
     * @return Response
     */
    public function getIndex($code) 
    {
        $validator = Validator::make(
            [ 'code' => strtolower($code) ],
            [ 'code' => 'required|in:usd,gbp,eur,kes' ]
        );

        if ($validator->fails()) 
        {
            return response()->json([ 
                'status' => 'error',
                'errors' => $validator->errors()
            ])->setStatusCode(400);
        }

        return response()->json([
            'status' => 'ok',
            'code'	 => $code,
            'rate'	 => $this->exchangeRates[strtoupper($code)]	
        ]);
    }
}
