<?php

namespace AlgoliaCommandBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AlgoliaSearch\Client;

abstract class AbstractAlgoliaCommand extends ContainerAwareCommand
{
    CONST API_KEY_OPTION_KEY        = 'api-key';
    CONST APPLICATION_ID_OPTION_KEY = 'application-id';
    CONST STATUS_CODE_ERROR = 1;

    private $apiKey;
    private $applicationId;
    protected $logger;
    protected $client;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        // Initializes definition, sets name and calls configure()
        parent::__construct($this->getName());
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption(
                self::API_KEY_OPTION_KEY,       // alias
                null,                           // shortcut (not provided)
                InputOption::VALUE_OPTIONAL,    // mode
                'Algolia API Key',              // description
                null                            // default (not provided)
            )
            ->addOption(
                self::APPLICATION_ID_OPTION_KEY,
                null,
                InputOption::VALUE_OPTIONAL,
                'Algolia Application ID'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::intialize($input, $output);

        if ($input->hasOption(self::API_KEY_OPTION_KEY)) {
            $this->apiKey = $input->getOption(self::API_KEY_OPTION_KEY);
        }

        if ($input->hasOption(self::APPLICATION_ID_OPTION_KEY)) {
            $this->applicationID = $input->getOption(self::APPLICATION_ID_OPTION_KEY);
        }

        if (!$this->apiKey) {
            $this->apiKey = $this->getContainer()->get('algolia_api_key');
        }

        if (!$this->applicationId) {
            $this->applicationId = $this->getContainer()->get('algolia_application_id');
        }

        if (!$this->apiKey) {
            throw new \LogicException('Algolia API Key must be provided.');
        }

        if (!$this->applicationId) {
            throw new \LogicException('Algolia Application ID must be provided.');
        }

        $this->client = $this->createClient();            
    }

    /**
     * @return string
     */
    protected function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    protected function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @return AlgoliaSearch\Client
     */
    protected function createClient()
    {
        return new Client($this->applicationId, $this->apiKey);
    }
}