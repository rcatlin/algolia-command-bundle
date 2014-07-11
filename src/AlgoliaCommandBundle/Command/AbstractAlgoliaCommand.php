<?php

namespace AlgoliaCommandBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AlgoliaSearch\Client;

abstract class AbstractAlgoliaCommand extends ContainerAwareCommand
{
    CONST OPTION_API_KEY           = 'api-key';
    CONST OPTION_APPLICATION_ID    = 'application-id';
    CONST PARAMETER_API_KEY        = 'algolia_api_key';
    CONST PARAMETER_APPLICATION_ID = 'algolia_application_id';
    CONST STATUS_CODE_ERROR        = 1;
    CONST ERROR_BAD_JSON_MESSAGE   = 'Invalid JSON.';

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
                self::OPTION_API_KEY,           // alias
                null,                           // shortcut (not provided)
                InputOption::VALUE_OPTIONAL,    // mode
                'Algolia API Key',              // description
                null                            // default (not provided)
            )
            ->addOption(
                self::OPTION_APPLICATION_ID,
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
        parent::initialize($input, $output);

        if ($input->hasOption(self::OPTION_API_KEY)) {
            $this->apiKey = $input->getOption(self::OPTION_API_KEY);
        }

        if ($input->hasOption(self::OPTION_APPLICATION_ID)) {
            $this->applicationID = $input->getOption(self::OPTION_APPLICATION_ID);
        }

        if (!$this->apiKey) {
            $this->apiKey = $this->getContainer()->getParameter(self::PARAMETER_API_KEY);
        }

        if (!$this->applicationId) {
            $this->applicationId = $this->getContainer()->getParameter(self::PARAMETER_APPLICATION_ID);
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
