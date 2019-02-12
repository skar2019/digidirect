<?php
namespace Ewave\AbstractEntity\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Ewave\AbstractEntity\Model\Indexer\AbstractEntity;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Indexer\StateInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLED = 'ewave_abstractentity/general/enable';
    const XML_PATH_ENTITIES = 'ewave_abstractentity/general/entities';

    /**
     * @var array
     */
    protected $entities;

    /**
     * @var StateInterface
     */
    protected $state;

    /**
     * Config constructor.
     * @param Context $context
     * @param StateInterface $state
     */
    public function __construct(
        Context $context,
        StateInterface $state
    ) {
        $this->state = $state;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        if ($this->entities === null) {
            $this->entities = $this->getMultiSelectConfig(self::XML_PATH_ENTITIES, ScopeInterface::SCOPE_WEBSITE);
        }

        return $this->entities;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isIndexTableEnableForEntity($id)
    {
        if (!$this->isEnable() || !in_array($id, $this->getEntities())) {
            return false;
        }

        $state = $this->state->loadByIndexer(AbstractEntity::INDEXER_ID);
        $indexValid = $state->getStatus() === StateInterface::STATUS_VALID;

        return $indexValid;
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param string $scopeCode
     * @return array
     */
    protected function getMultiSelectConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $value = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
        if ($value) {
            return explode(',', $value);
        }
        return [];
    }
}
