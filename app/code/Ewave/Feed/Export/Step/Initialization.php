<?php

namespace Ewave\Feed\Export\Step;

use Ewave\Feed\Export\Context;
use Ewave\Feed\Helper\Io;
use Ewave\Feed\Model\Config;

class Initialization extends AbstractStep
{
    /**
     * @var \Ewave\Feed\Helper\Io
     */
    protected $io;

    /**
     * @var \Ewave\Feed\Model\Config
     */
    protected $config;

    /**
     * Initialization constructor.
     * @param Context $context
     * @param Config $config
     * @param Io $io
     */
    public function __construct(
        Context $context,
        Config $config,
        Io $io
    ) {
        $this->io = $io;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Remove state and temp feed file
     * {@inheritdoc}
     */
    public function execute()
    {
        $tmpPath = $this->config->getTmpPath();

        $this->io->unlink($this->context->getStateFile());
        $this->io->unlink($tmpPath . DIRECTORY_SEPARATOR . $this->context->getFeed()->getId() . '.dat');

        $this->index = 1;
    }
}
