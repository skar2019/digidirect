<?php

namespace Digidirect\Navigation\Model\Menu\Type;

use Digidirect\Navigation\Helper\Data;
use Digidirect\Navigation\Model\Frontend\Acl;
use Digidirect\Navigation\Model\Menu;
use Magento\Framework\App\ObjectManager;
use Digidirect\Navigation\Api\Data\MenuItemInterface;

class AbstractType
{
    /**
     * @var \Digidirect\Navigation\Model\Menu
     */
    protected $item;

    /**
     * @var \Digidirect\Navigation\Helper\Data
     */
    protected $helper;

    /**
     * @var Acl|mixed|null
     */
    protected $frontendAcl;

    /**
     * @var []
     */
    protected $placeholderSecurityMapping = [
        '{{unsecure_base_url}}' => false,
        '{{secure_base_url}}' => true,
        '{{base_url}}' => true,
    ];

    /**
     * AbstractType constructor.
     *
     * @param Data $helper
     * @param Acl|null $acl
     */
    public function __construct(Data $helper, Acl $acl = null)
    {
        if (null === $acl) {
            $acl = ObjectManager::getInstance()->get(Acl::class);
        }

        $this->frontendAcl = $acl;
        $this->helper = $helper;
    }

    /**
     * @param MenuItemInterface|\Digidirect\Navigation\Model\Menu $menuItem
     * @return $this
     */
    public function setItem(MenuItemInterface $menuItem):MenuDataInterface
    {
        $this->item = $menuItem;
        return $this;
    }

    /**
     * Check if specified urls is secure
     *
     * @param string $placeholder
     * @return bool
     */
    protected function _isSecureUrl(string $placeholder)
    {
        return $this->placeholderSecurityMapping[$placeholder] ?? false;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->frontendAcl->isAvailable($this->item);
    }

    /**
     * @return bool
     */
    public function isValidLink()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return '';
    }
}
