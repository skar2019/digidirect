<?php

namespace Ewave\Banner\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

use Ewave\Banner\Block\Adminhtml\Banner\Edit\Tab\Properties;
use Magento\Framework\Event\Observer;

class BannerEditTabContentBeforePrepareForm implements ObserverInterface
{
    /**
     * @var Properties
     */
    protected $propertiesTab;

    /**
     * BannerEditTabContentBeforePrepareForm constructor.
     *
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->propertiesTab = $properties;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->propertiesTab->addTargetTypeFields(
            $observer->getForm(),
            $observer->getModel(),
            $observer->getAfterFormBlock()
        );
        return $this;
    }
}
