<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ItemTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/item/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/item/new', 'POST', [], [
            'name' => 'name',
            'body' => 'body'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertEquals('name', $request->name);
        $this->assertEquals('body', $request->body);

        $id = $request->id;

        // edit
        $request = $this->request('/api/item/' . $id, 'PUT', [], [
            'name' => '1',
            'body' => '2'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertEquals('1', $request->name);
        $this->assertEquals('2', $request->body);

        // show
        $request = $this->request('/api/item/' . $id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertEquals('1', $request->name);
        $this->assertEquals('2', $request->body);

        // delete
        $request = $this->request('/api/item/' . $id, 'DELETE');

        $this->assertEquals('The item was deleted successfully.', $request->msg);
    }
}
