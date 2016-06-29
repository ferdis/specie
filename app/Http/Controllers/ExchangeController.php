<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Exchanges;
use Illuminate\Support\Facades\Mail;

class ExchangeController extends Controller
{
	/**
	 * Exchange from ZAR to another currency
	 * 
	 * Small shortcut here: Amount's base is assumed to be ZAR. ¯\_(ツ)_/¯
	 *
	 * @return void
	 */
        public function postExchange(Request $request)
        {
                $validator = Validator::make(
                        [ 
				'currency' => strtolower($request->input('currency')), 
				'amount' => $request->input('amount') 
			],
                        [ 
				'currency' => 'required|in:usd,gbp,eur,kes', 
				'amount' => 'required|integer|min:0'
			]
                );

                if ($validator->fails()) 
                {
                        return response()->json([
                                'status' => 'error',
                                'errors' => $validator->errors()
                        ])->setStatusCode(400);
                }

		$currency = strtoupper($request->input('currency'));
		$amount = (float) $request->input('amount');

		// Values not yet determined are either falsey
		$order = new Exchanges;

		$order->currency_type = $currency;
		$order->currency_rate = $this->exchangeRates[$currency];
		$order->amount_paid = $amount;
		$order->surcharge_amount = null;
		$order->discount = 0;

		// Surcharges 
		$order->surcharge_percentage = $this->getSurchargePercentage($currency);
		$order->surcharge_amount = $amount / $order->surcharge_percentage;
		$order->amount_purchased = $this->transformCurrencyAmount($currency, ($amount - $order->surcharge_amount));

		// Some IFTT things need to change this data (Visitor?)
		if ($discount = (int) $this->getDiscountPercentage($currency)) 
		{
			$order->amount_paid = $order->amount_paid * (1 + ($discount / 100));
		}

		$order->save();

		if ($currency === 'GBP')
		{
			// Might as well do it now.
			$this->sendOrderConfirmation($order);
		}


		return response()->json([
			'status' => 'ok',
			'order' => $order
		]);

        }


	/**
	 * Converts from ZAR to another currency
	 *
	 * @param string $code
	 * @param float $amount
	 * @return float
	 */
	protected function transformCurrencyAmount($code, $amount) 
	{
		return ($amount * $this->exchangeRates[$code]);
	}


	/**
	 * Get percentage of sucharge for a given currency
	 *
	 * @param string $currency
	 * @return integer 
	 */
	protected function getSurchargePercentage($currency)
	{
		return env('SURCHARGE_' . $currency);
	}

	/**
	 * Get percentage of discount for a given currency
	 *
	 * @param string $currency
	 * @return integer 
	 */
	protected function getDiscountPercentage($currency)
	{
		return env('DISCOUNT_' . $currency);
	}

	/**
	 * Sends an order confirmation mail
	 *
         * Beware: Double quotes lie ahead!
         *
	 * @param \App\Models\Exchanges $order
	 * @return void 
	 */
	protected function sendOrderConfirmation(Exchanges $order)
	{

		// No bloat, no templating.
		$template = sprintf("Your order confirmation for #%d\r\n", $order->id);

		foreach ($order->toArray() as $item => $value)
		{
			$template .= sprintf("\r\n%s: %s", ucwords(str_replace('_', ' ', $item)), $value);
		}

		Mail::raw($template, function($m) use($order)
		{
			$m->from('orders@specie.dev', 'Specie');
			$m->subject('Order Confirmation #' . $order->id);
			$m->to(env('ORDER_EMAIL'));
		});
	}


}
