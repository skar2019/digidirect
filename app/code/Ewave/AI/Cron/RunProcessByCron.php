<?php

namespace Ewave\AI\Cron;

/**
 * Class LogBackup
 * @package Ewave\AI\Cron
 */
class RunProcessByCron
{
    /**
     * @var \Ewave\AI\Model\Engine\EngineFactory
     */
    protected $integrationEngine;

    /**
     * RunProcessByCron constructor.
     * @param \Ewave\AI\Model\Engine\EngineFactory $integrationEngine
     */
    public function __construct(
        \Ewave\AI\Model\Engine\EngineFactory $integrationEngine
    ) {
        $this->integrationEngine = $integrationEngine;
    }

    /**
     * @param string $processCode
     * @param array $arguments
     * @return $this
     */
    public function __call($processCode, $arguments = [])
    {
        $this->integrationEngine
            ->create(
                [
                    'processCode' => $processCode,
                    'initiator' => 'CRON',
                    'runOptions' => [],
                    'isCronRun' => true,
                ]
            )
            ->run();
        return $this;
    }
}
