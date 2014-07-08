<?php

namespace AlgoliaToolkit\Command;

use Psr\Log\AbstractLogger;
use Symfony\Component\Console\Command\Command;

abstract class AbstractAlgoliaCommand extends Command
{
    protected $client;
    protected $logger;

    public function __construct(AbstractLogger $logger = null)
    {
        parent::__construct($this->getName());

        $this->logger = $logger;
    }

    abstract public function getName();
}