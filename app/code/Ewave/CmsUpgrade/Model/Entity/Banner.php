<?php
namespace Ewave\CmsUpgrade\Model\Entity;

use Ewave\CmsUpgrade\Model\GeneratorInterface;
use Ewave\CmsUpgrade\Model\BannerGenerator;

/**
 * Class Banner
 * @package Ewave\CmsUpgrade\Model\Entity
 */
class Banner implements GeneratorInterface
{
    const ENTITY_TYPE = 'banner';

    /**
     * @var array
     */
    protected $_bannerIds = [];

    /** 
     * @var BannerGenerator  
     */
    protected $_generator = null;
    
    /**
     * @var array
     */
    protected $_upgradeFields = [];

    /**
     * @param BannerGenerator $generator
     * @param array $upgradeFields
     */
    public function __construct(BannerGenerator $generator, array $upgradeFields = [])
    {
        $this->_generator = $generator;
        $this->_upgradeFields = $upgradeFields;
        $this->_generator->setGenerateEntity($this);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function generate()
    {
        return $this->_generator->processUpgradeScript($this->_bannerIds);
    }

    /**
     * @return mixed
     */
    public function getUpgradeFields()
    {
        return $this->_upgradeFields;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function setBannerIds(array $ids)
    {
        $this->_bannerIds = $ids;
        return $this;
    }
}
