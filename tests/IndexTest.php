<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IndexTest extends TestCase
{
    
    public function testAccessedWithoutCredential()
    {
        $this->get('/');
        $this->assertEquals(
            'Unauthorized', $this->response->getContent()
        );
    }
}
