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

        $this->facebookService = new FacebookService($this->client, 'fb_access_token');

        parent::setUp();
    }

    public function testGetLatestPosts()
    {
        $this->prepareResponseWithFixture('get_latest_posts');

        $this->assertCount(2, $this->facebookService->getLatestPostsByFbGroupId('test_facebook_id'));
    }

    public function testGetLatestPostsWithNoResults()
    {
        $this->prepareResponseWithFixture('get_latest_posts_empty');

        $this->assertCount(0, $this->facebookService->getLatestPostsByFbGroupId('test_facebook_id'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while fetching latest posts.
     */
    public function testGetLatestPostsWithError()
    {
        $this->prepareResponseWithFixture('get_latest_posts_error');

        $this->facebookService->getLatestPostsByFbGroupId('test_facebook_id');
    }

    public function testGetPostById()
    {
        $this->prepareResponseWithFixture('get_post');

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
        $this->prepareResponseWithFixture('get_post_error');

        $this->facebookService->getPostById('nonexistent_post_id');
    }

    public function testDeletePostById()
    {
        $this->prepareResponseWithFixture('delete_post');

        $this->facebookService->deletePostById('existent_post_id');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while deleting the post.
     */
    public function testDeletePostByIdThrowsError()
    {
        $this->prepareResponseWithFixture('delete_post_error');

        $this->facebookService->deletePostById('nonexistent_post_id');
    }

    public function testCommentPost()
    {
        $this->prepareResponseWithFixture('comment_post');

        $this->facebookService->commentPost('existent_post_id', 'Hello there!');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error occurred while commenting the post.
     */
    public function testCommentPostThrowsException()
    {
        $this->prepareResponseWithFixture('comment_post_error');

        $this->facebookService->commentPost('nonexistent_post_id', 'World will never seen this :(');
    }

    private function prepareResponseWithFixture($fileName)
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], file_get_contents('tests/Unit/Fixtures/' . $fileName . '.json')));
    }
}
