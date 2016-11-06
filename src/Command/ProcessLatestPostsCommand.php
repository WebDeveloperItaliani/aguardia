<?php

namespace AGuardia\Command;

use AGuardia\Service\FacebookService;
use AGuardia\Validator\HashtagValidator;
use AGuardia\Service\FacebookServiceFactory;
use Symfony\Component\Console\Command\Command;
use AGuardia\Repository\ReportedPostRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessLatestPostsCommand extends Command
{
    /* @var FacebookService */
    private $facebookService;

    /* @var HashtagValidator */
    private $hashtagValidator;

    /* @var ReportedPostRepository */
    private $reportedPostRepository;

    public function configure()
    {
        $this
            ->setName('process-latest')
            ->setDescription('Fetch latest posts and checks their format.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Fetching latest posts...');

        $this->hashtagValidator = new HashtagValidator();
        $this->facebookService = FacebookServiceFactory::make(getenv('FACEBOOK_ACCESS_TOKEN'));
        $this->reportedPostRepository = new ReportedPostRepository('posts.json');

        $latestPosts = $this->facebookService->getLatestPostsByFbGroupId(getenv('FACEBOOK_GROUP_ID'));
        $output->writeln('Found ' . count($latestPosts) . ' posts.');

        $alreadyReportedPostsIds = $this->reportedPostRepository->get();
        $reportedPostsIds = [];
        foreach($latestPosts as $post)
        {
            if(!$this->hashtagValidator->validate($post['message']) && !in_array($post['id'], $alreadyReportedPostsIds)) {
                $reportedPostsIds[] = $post['id'];
                $this->facebookService->commentPost($post['id'], getenv('WARNING_MESSAGE'));
            }
        }

        $this->reportedPostRepository->save($reportedPostsIds);

        $output->writeln(count($reportedPostsIds) . ' marked as invalid posts.');
    }
}
