<?php

namespace Oro\Bundle\UserBundle\Tests\Functional\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Acme\Bundle\TestsBundle\Test\ToolsAPI;
use Acme\Bundle\TestsBundle\Test\Client;

/**
 * @outputBuffering enabled
 */
class RestApiGroupsTest extends WebTestCase
{

    protected $client = null;
    protected static $hasLoaded = false;

    public function setUp()
    {
        $this->client = static::createClient(array(), ToolsAPI::generateWsseHeader());
        if (!self::$hasLoaded) {
            $this->client->startTransaction();
        }
        self::$hasLoaded = true;
    }

    public static function tearDownAfterClass()
    {
        Client::rollbackTransaction();
    }

    /**
     * @return array
     */
    public function testApiCreateGroup()
    {
        $request = array(
            "group" => array(
                "name" => 'Group_'.mt_rand(100, 500),
                "roles" => array(),
            )
        );

        $this->client->request('POST', 'http://localhost/api/rest/latest/group', $request);
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 201);

        return $request;
    }

    /**
     * @depends testApiCreateGroup
     * @param array $request
     * @return array $group
     */
    public function testApiGetGroups($request)
    {
        $this->client->request('GET', 'http://localhost/api/rest/latest/groups');
        $result = $this->client->getResponse();
        $result = json_decode($result->getContent(), true);
        $flag = 1;
        foreach ($result as $group) {
            if ($group['name'] == $request['group']['name']) {
                $flag = 0;
                break;
            }
        }
        $this->assertEquals(0, $flag);

        return $group;
    }

    /**
     * @depends testApiCreateGroup
     * @depends testApiGetGroups
     * @param array $request
     * @param array $group
     * @return array $group
     */
    public function testApiUpdateGroup($request, $group)
    {
        $request['group']['name'] .= '_updated';
        $this->client->request('PUT', 'http://localhost/api/rest/latest/groups' . '/' . $group['id'], $request);
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 302);
        $this->client->request('GET', 'http://localhost/api/rest/latest/groups' .'/'. $group['id']);
        $result = $this->client->getResponse();
        $result = json_decode($result->getContent(), true);
        $this->assertEquals($result['name'], $request['group']['name'], 'Group does not updated');

        return $group;
    }

    /**
     * @depends testApiUpdateGroup
     * @param $group
     */
    public function testApiDeleteGroup($group)
    {
        $this->client->request('DELETE', 'http://localhost/api/rest/latest/groups' . '/' . $group['id']);
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 204);
        $this->client->request('GET', 'http://localhost/api/rest/latest/groups' . '/' . $group['id']);
        $result = $this->client->getResponse();
        ToolsAPI::assertJsonResponse($result, 404);
    }
}
