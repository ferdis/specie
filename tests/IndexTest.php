<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class IndexTest extends TestCase
{
    /**
     * Tests for correct response on /
     *
     * @return void
     */
    public function testIndexRoute()
    {
        $this->get('/')->seeJsonEquals([
			'status' => 'ok',
			'version' => $this->app->version()
		]);	
    }
}
