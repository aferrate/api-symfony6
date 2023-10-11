<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Elasticsearch\ClientBuilder;

#[AsCommand(
    name: 'app:create-elasticsearch-cars-index',
    description: 'Creates elasticsearch car index.',
    hidden: false,
    aliases: ['app:create-cars-index']
)]
class CreateCarsIndexElastic extends Command
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
            'index' => 'cars',
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
                        'mark' => [
                            'type' => 'text',
                            'index' => 'true',
                        ],
                        'model' => [
                            'type' => 'text',
                            'index' => 'true',
                        ],
                        'year' => [
                            'type' => 'integer',
                            'index' => 'true',
                        ],
                        'description' => [
                            'type' => 'text',
                            'index' => 'true',
                        ],
                        'enabled' => [
                            'type' => 'boolean',
                            'index' => 'true',
                        ],
                        'createdAt' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                            'index' => 'true',
                        ],
                        'updatedAt' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                            'index' => 'true',
                        ],
                        'country' => [
                            'type' => 'text',
                            'index' => 'true',
                        ],
                        'city' => [
                            'type' => 'text',
                            'index' => 'true',
                        ],
                        'imageFilename' => [
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
        $this->setHelp('This Command creates elasticsearch car index');
    }
}
