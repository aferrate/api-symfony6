<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Elasticsearch\ClientBuilder;

#[AsCommand(
    name: 'app:create-elasticsearch-users-index',
    description: 'Creates elasticsearch users index.',
    hidden: false,
    aliases: ['app:create-users-index']
)]
class CreateUsersIndexElastic extends Command
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
        $params = [
            'index' => 'users',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'id' => [
                            'type' => 'text',
                            'index' => 'true'
                        ],
                        'email' => [
                            'type' => 'text',
                            'index' => 'true',
                        ],
                        'password' => [
                            'type' => 'text',
                            'index' => 'true',
                        ]
                    ]
                ]
            ]
        ];

        $this->elasticClient->indices()->create($params);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This Command creates elasticsearch users index');
    }
}
