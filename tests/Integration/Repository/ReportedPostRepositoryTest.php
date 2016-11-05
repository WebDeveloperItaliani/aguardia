<?php

namespace AGuardia\Integration\Repository;

use PHPUnit\Framework\TestCase;
use AGuardia\Repository\ReportedPostRepository;

class ReportedPostRepositoryTest extends TestCase
{
    /* @var ReportedPostRepository */
    private $reportedPostRepository;

    public function setUp()
    {
        $this->reportedPostRepository = new ReportedPostRepository('tests/Integration/Fixtures/get_test.json');

        parent::setUp();
    }

    public function testCanGetReportedPosts()
    {
        $expected = [
            'post_id_1',
            'post_id_2',
            'post_id_3'
        ];

        $this->assertEquals(
            $expected,
            $this->reportedPostRepository->get()
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Posts file not found.
     */
    public function testReportedPostsFileDoesNotExist()
    {
        $this->reportedPostRepository = new ReportedPostRepository('tests/Integration/Fixtures/not_existent.json');
        $this->reportedPostRepository->get();
    }

    public function testCanSaveReportedPosts()
    {
        touch('tests/Integration/Fixtures/save_test.json');

        $this->reportedPostRepository = new ReportedPostRepository('tests/Integration/Fixtures/save_test.json');

        $ids = [
            "post_id_1",
            "post_id_2",
            "post_id_3",
            "post_id_4"
        ];

        $this->reportedPostRepository->save($ids);

        $this->assertEquals(
            json_encode($ids),
            file_get_contents('tests/Integration/Fixtures/save_test.json')
        );

        unlink('tests/Integration/Fixtures/save_test.json');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Error while writing on posts file.
     */
    public function testSaveReportedPostsError()
    {
        $this->reportedPostRepository = new ReportedPostRepository('tests/Integration/Fixtures/ThisDoesNotExist/file.json');
        $this->reportedPostRepository->save(['post_id_1']);
    }
}
