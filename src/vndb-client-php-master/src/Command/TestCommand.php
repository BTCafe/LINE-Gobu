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

class TestCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('vndb:test')
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
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $client = new Client();
        
        $client->connect();
        if (!$client->isConnected()) {
            echo "Not connected...\n";
        } else {
            $client->login($username, $password);
        }
        
        $res = $client->sendCommand('dbstats');
        $this->dumpResponse($res);

        $res = $client->getVisualNovelDataById(14274);
        $this->dumpResponse($res);

        $res = $client->getReleaseDataById(21446);
        $this->dumpResponse($res);

        $res = $client->getProducerDataById(24);
        $this->dumpResponse($res);

        $res = $client->getCharacterDataById(537);
        $this->dumpResponse($res);

        $res = $client->getCharacterDataById(9999999537);
        $this->dumpResponse($res);
    }
    
    private function dumpResponse($response)
    {
        echo "TYPE: [{$response->getType()}]\n";
        echo "DATA: [" . json_encode($response->getData()) . "]\n\n";
    }
}
