<?php

namespace AGuardia\Unit\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use AGuardia\Service\FacebookService;

class FacebookServiceTest extends TestCase
{
    /* @var Client | \PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /* @var FacebookService */
    private $facebookService;

    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->facebookService = new FacebookService($this->client);

        parent::setUp();
    }

    public function testGetLatestPosts()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/get_latest_posts_ok.json')));

        $this->assertCount(2, $this->facebookService->getLatestPostsByFbGroupId('test_facebook_id'));
    }

    public function testGetLatestPostsWithNoResults()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/get_latest_posts_empty.json')));

        $this->assertCount(0, $this->facebookService->getLatestPostsByFbGroupId('test_facebook_id'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while fetching latest posts.
     */
    public function testGetLatestPostsWithError()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/get_latest_posts_error.json')));

        $this->facebookService->getLatestPostsByFbGroupId('test_facebook_id');
    }
}
