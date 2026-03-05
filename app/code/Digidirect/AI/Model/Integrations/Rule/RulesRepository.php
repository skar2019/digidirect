<?php
namespace Digidirect\AI\Model\Integrations\Rule;

use Magento\Framework\Api\ObjectFactory;

class RulesRepository
{
    /**
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * @var \Digidirect\AI\Model\Integrations\Config\Data
     */
    protected $_integrationsConfig;

    /**
     * RuleRepository constructor.
     * @param ObjectFactory $objectFactory
     * @param \Digidirect\AI\Model\Integrations\Config\Data $integrationsConfig
     */
    public function __construct(
        ObjectFactory $objectFactory,
        \Digidirect\AI\Model\Integrations\Config\Data $integrationsConfig
    ) {
        $this->objectFactory = $objectFactory;
        $this->_integrationsConfig = $integrationsConfig;
    }

    /**
     * @param string $integrationName
     * @return array
     */
    public function getRulesByIntegrationName($integrationName)
    {
        $rules = [];
        $integration = $this->_integrationsConfig->getIntegrationByName($integrationName);
        foreach ($integration['entity_rules'] as $entityRule) {
            if (isset($entityRule['mapper'])) {
                $mapperInstance = $this->objectFactory->create($entityRule['mapper'], []);
                $entityRule['mapper'] = $mapperInstance;
            }
            $entityRuleObject = $this->objectFactory->create($entityRule['class'], $entityRule);
            if ($entityRuleObject instanceof RuleAbstract) {
                $rules[] = $entityRuleObject;
            }
        }

        return $rules;
    }
}
