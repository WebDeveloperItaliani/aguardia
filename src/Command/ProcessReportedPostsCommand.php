<?php

namespace AGuardia\Command;


use AGuardia\Repository\ReportedPostRepository;
use AGuardia\Service\FacebookServiceFactory;
use AGuardia\Validator\HashtagValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessReportedPostsCommand extends Command
{
    private $facebookService;
    private $hashtagValidator;
    private $reportedPostRepository;

    public function configure()
    {
        $this
            ->setName('process-reported')
            ->setDescription('Removes non-compliant posts, starting from the previously reported ones.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->hashtagValidator = new HashtagValidator();
        $this->reportedPostRepository = new ReportedPostRepository('posts.json');
        $this->facebookService = FacebookServiceFactory::make(getenv('FACEBOOK_ACCESS_TOKEN'));

        $output->writeln('Processing reported posts...');

        $reportedPostsIds = $this->reportedPostRepository->get();

        $output->writeln('Found ' . count($reportedPostsIds) . ' previously reported posts.');

        foreach($reportedPostsIds as $postId) {
            try {
                $post = $this->facebookService->getPostById($postId);
                if(!$this->hashtagValidator->validate($post['message'])) {
                    $this->facebookService->deletePostById($postId);
                }
            } catch (\Exception $e) {
                $output->writeln('Something went wrong while processing this post: ' . $postId);
            }
        }

        $this->reportedPostRepository->save([]);

        $output->writeln('Operation completed successfully.');
    }
}
