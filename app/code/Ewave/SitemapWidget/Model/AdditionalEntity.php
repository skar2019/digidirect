<?php

namespace Ewave\SitemapWidget\Model;

use Ewave\SitemapWidget\Api\AdditionalEntityInterface;

/**
 * Class Template
 * @package Ewave\SitemapWidget\Model\Widget\Options
 */
class AdditionalEntity implements AdditionalEntityInterface
{
    /**
     * @var \Magento\Framework\Phrase
     */
    protected $title;

    /**
     * @var string
     */
    protected $accessKey;

    /**
     * @var array
     */
    protected $entities;

    /**
     * @var string|null
     */
    protected $renderer = null;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @inheritdoc
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @inheritdoc
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @inheritdoc
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return string
     */
    static function getUrlKey()
    {
        return self::ENTITY_URL;
    }

    /**
     * @return string
     */
    static function getTitleKey()
    {
        return self::ENTITY_TITLE;
    }
}