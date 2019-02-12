<?php
namespace Ewave\AbstractEntity\Ui\DataProvider\Form\Modifier;

use Ewave\AbstractEntity\Model\Registry\Constants;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

abstract class AbstractModifier implements ModifierInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Ewave\AbstractEntity\Model\AbstractEntity
     */
    protected $currentEntity;

    /**
     * AbstractModifier constructor.
     * @param Registry $registry
     * @param RequestInterface $request
     */
    public function __construct(
        Registry $registry,
        RequestInterface $request
    ) {
        $this->registry = $registry;
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->request->getParam('store');
    }

    /**
     * Get current menu Item
     *
     * @return \Ewave\AbstractEntity\Model\AbstractEntity
     */
    protected function getCurrentEntity()
    {
        if ($this->currentEntity === null) {
            $this->currentEntity = $this->registry->registry(Constants::CURRENT_ABSTRACT_ENTITY);
        }
        return $this->currentEntity;
    }
}
