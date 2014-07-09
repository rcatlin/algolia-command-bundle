<?php

namespace AlgoliaCommandBundle\Command;

use Psr\Log\AbstractLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractAlgoliaCommand extends ContainerAwareCommand
{
    CONST API_KEY_OPTION_KEY        = 'api-key';
    CONST APPLICATION_ID_OPTION_KEY = 'application-id';
    private $apiKey;
    private $applicationId;
    protected $logger;

    /**
     * {@inheritDoc}
     */
    public function __construct(AbstractLogger $logger = null)
    {
        $this->logger = $logger;

        // Initializes definition, sets name and calls configure()
        parent::__construct($this->getName());

        if (!$this->apiKey && !$this->applicationId) {
            throw new \LogicException('Both an Algolia API key and Application ID must be provided.');
        }
        if (!$this->apiKey) {
            throw new \LogicException('An Algolia API key must be provided.');
        }

        if (!$this->applicationId) {
            throw new \LogicException('An Algolia Application ID must be provided');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
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
     * @return string
     */
    abstract public function getName();

    /**
     * @return alias
     */
    abstract public function getAlias();
}