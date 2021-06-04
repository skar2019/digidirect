<?php
namespace Ewave\NavigationCMSUpgrade\Model\Entity;

use Ewave\CmsUpgrade\Model\GeneratorInterface;
use Ewave\NavigationCMSUpgrade\Model\GeneratorInterface as NavigationGeneratorInterface;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Entity
 */
abstract class AbstractEntity implements GeneratorInterface
{
    /**
     * @var []
     */
    protected $params;

    /**
     * @var NavigationGeneratorInterface
     */
    protected $generator;

    /**
     * @return array
     */
    public function getUpgradeFields()
    {
        return [];
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function generate()
    {
        return $this->generator->processUpgradeScript($this->params);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }
}
