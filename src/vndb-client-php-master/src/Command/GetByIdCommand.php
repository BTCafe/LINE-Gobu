<?php

namespace VndbClient\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Gitonomy\Git\Repository;
use VndbClient\Client;

class GetByIdCommand extends Command
{
    /**
    * {@inheritdoc}
    */
    protected function configure()
    {
        $this->ignoreValidationErrors();
        
        $this
        ->setName('vndb:getbyid')
        ->setDescription('Get data by id')
        ->addArgument(
            'username',
            InputArgument::REQUIRED,
            'username'
        )
        ->addArgument(
            'password',
            InputArgument::REQUIRED,
            'username'
        )
        ->addArgument(
            'type',
            InputArgument::REQUIRED,
            'username'
        )
        ->addArgument(
            'id',
            InputArgument::REQUIRED,
            'username'
        );
    }

    /**
    * {@inheritdoc}
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');
        $client = new Client();
        
        $client->connect();
        if (!$client->isConnected()) {
            echo "Not connected...\n";
        } else {
            $client->login($username, $password);
        }
        
        $response = null;
        
        switch ($type) {
            case 'vn':
                $response = $client->getVisualNovelDataById($id);
                break;
            case 'release':
                $response = $client->getReleaseDataById($id);
                break;
            case 'character':
                $response = $client->getCharacterDataById($id);
                break;
            case 'producer':
                $response = $client->getProducerDataById($id);
                break;
            default:
                echo "unsupported type. use vn, release, producer or character\n";
                break;
        }
        if ($response) {
            $this->dumpResponse($response);
        }
    }

    private function dumpResponse($response)
    {
        echo "TYPE: [{$response->getType()}]\n";
        echo "DATA: [" . json_encode($response->getData()) . "]\n\n";
    }
}
