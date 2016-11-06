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

    public function testGetPostById()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/get_post_ok.json')));

        $this->assertEquals([
            "created_time" => "2016-11-05T19:51:24+0000",
            "message" => "Test post contents...",
            "id" => "1070708249665248_1166157310120341"
        ], $this->facebookService->getPostById('existent_post_id'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while fetching the post.
     */
    public function testGetPostByIdThrowsError()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/get_post_error.json')));

        $this->facebookService->getPostById('nonexistent_post_id');
    }

    public function testDeletePostById()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/delete_post.json')));

        $this->facebookService->deletePostById('existent_post_id');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while deleting the post.
     */
    public function testDeletePostByIdThrowsError()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/delete_post_error.json')));

        $this->facebookService->deletePostById('nonexistent_post_id');
    }

    public function testCommentPost()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/comment_post.json')));

        $this->facebookService->commentPost('existent_post_id', 'Hello there!');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while commenting the post.
     */
    public function testCommentPostThrowsException()
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/comment_post_error.json')));

        $this->facebookService->commentPost('nonexistent_post_id', 'World will never seen this :(');
    }
}
