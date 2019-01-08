<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDataNoInput()
    {
        $response = $this->json('GET', '/api/results');

        $response->assertStatus(400);
    }

    public function testGetDataInput()
    {
        foreach(['object', 'city', 'street'] as $object)
        {
	    	$response = $this->json('GET', '/api/results', [
	        	'page' => 1,
	        	'order' => 'ASC',
	        	'column' => $object
	    	]);

	        $response->assertStatus(200);

	        $response = $this->json('GET', '/api/results', [
	        	'page' => 1,
	        	'order' => 'DESC',
	        	'column' => $object
	    	]);

	        $response->assertStatus(200);
        }
        
    }

    public function testGetListing()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
