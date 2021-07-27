<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ItemTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();

        // list
        $request = $this->request('/api/item/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/item/new', 'POST', [], [
            'name' => 'name-' . $timestamp,
            'body' => 'body'
        ]);

        $this->assertEquals('3', count((array)$request));
        $this->assertTrue(isset($request->id));
        $this->assertEquals('name-' . $timestamp, $request->name);
        $this->assertEquals('body', $request->body);

        $id = $request->id;

        // edit
        $request = $this->request('/api/item/name-' . $timestamp, 'PUT', [], [
            'name' => '1-' . $timestamp,
            'body' => '2'
        ]);

        $this->assertEquals('3', count((array)$request));
        $this->assertTrue(isset($request->id));
        $this->assertEquals('1-' . $timestamp, $request->name);
        $this->assertEquals('2', $request->body);

        // show
        $request = $this->request('/api/item/1-' . $timestamp, 'GET');

        $this->assertEquals('3', count((array)$request));
        $this->assertTrue(isset($request->id));
        $this->assertEquals('1-' . $timestamp, $request->name);
        $this->assertEquals('2', $request->body);

        // delete
        $request = $this->request('/api/item/1-' . $timestamp, 'DELETE');

        $this->assertEquals('DELETED', $request->msg);
    }
}
