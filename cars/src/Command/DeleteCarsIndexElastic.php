<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Elasticsearch\ClientBuilder;

#[AsCommand(
    name: 'app:delete-elasticsearch-cars-index',
    description: 'Deletes elasticsearch car index.',
    hidden: false,
    aliases: ['app:delete-cars-index']
)]
class DeleteCarsIndexElastic extends Command
{
    private $elasticClient;

    public function __construct()
    {
        $this->elasticClient = ClientBuilder::create()->setHosts([
            'host' => $_ENV['ELASTICSEARCH_DSN']
        ])->build();;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deleteParams = [
            'index' => 'cars'
        ];

        $this->elasticClient->indices()->delete($deleteParams);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This Command deletes elasticsearch car index');
    }
}
