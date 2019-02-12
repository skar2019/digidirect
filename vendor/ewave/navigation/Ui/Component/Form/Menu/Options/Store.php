<?php

namespace Ewave\Navigation\Ui\Component\Form\Menu\Options;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use Magento\Framework\Escaper;
use Magento\Store\Model\System\Store as SystemStore;
use Ewave\Navigation\Model\Admin\StoreDropdownAcl;

class Store extends StoreOptions
{
    /**
     * @var StoreDropdownAcl
     */
    protected $role;

    /**
     * Store constructor.
     * @param StoreDropdownAcl $roleResolver
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     */
    public function __construct(
        StoreDropdownAcl $roleResolver,
        SystemStore $systemStore,
        Escaper $escaper
    ) {
        $this->role = $roleResolver;
        parent::__construct($systemStore, $escaper);
    }

    /**
     * All Store Views value
     */
    const ALL_STORE_VIEWS = '0';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        if ($this->role->showAllStoreViews()) {
            $this->currentOptions['All Store Views']['label'] = __('All Store Views');
            $this->currentOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;
        }

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);
        return $this->options;
    }

    /**
     * Generate current options
     *
     * @return void
     */
    protected function generateCurrentOptions()
    {
        $websiteCollection = $this->systemStore->getWebsiteCollection();
        $groupCollection = $this->systemStore->getGroupCollection();
        $storeCollection = $this->systemStore->getStoreCollection();
        $strRepeatedEight = str_repeat(' ', 8);
        $strRepeatedFour = str_repeat(' ', 4);
        /** @var \Magento\Store\Model\Website $website */
        foreach ($websiteCollection as $website) {
            $groups = [];
            /** @var \Magento\Store\Model\Group $group */
            foreach ($groupCollection as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $stores = [];
                    /** @var  \Magento\Store\Model\Store $store */
                    foreach ($storeCollection as $store) {
                        if ($store->getGroupId() == $group->getId()) {
                            if ($this->role->showStore($store->getId())) {
                                $name = $this->escaper->escapeHtml($store->getName());
                                $stores[$name]['label'] = $strRepeatedEight . $name;
                                $stores[$name]['value'] = $store->getId();
                            }
                        }
                    }
                    if (!empty($stores)) {
                        $name = $this->escaper->escapeHtml($group->getName());
                        $groups[$name]['label'] = $strRepeatedFour . $name;
                        $groups[$name]['value'] = array_values($stores);
                    }
                }
            }
            if (!empty($groups)) {
                $name = $this->escaper->escapeHtml($website->getName());
                $this->currentOptions[$name]['label'] = $name;
                $this->currentOptions[$name]['value'] = array_values($groups);
            }
        }
    }
}
