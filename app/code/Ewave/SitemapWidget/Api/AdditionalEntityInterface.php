<?php
namespace Ewave\SitemapWidget\Api;

/**
 * Interface PersonalizedGiftCardManagementInterface
 * @api
 * @package Magento\GiftCardAccount\Api
 */
interface AdditionalEntityInterface
{
    /**
     * Entity title
     */
    const ENTITY_TITLE = 'title';

    /**
     * Entity url
     */
    const ENTITY_URL = 'url';

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle();

    /**
     * @param \Magento\Framework\Phrase $title
     * @return void
     */
    public function setTitle($title);

    /**
     * @return array
     */
    public function getEntities();

    /**
     * @param $entities
     * @return mixed
     */
    public function setEntities($entities);

    /**
     * @return string
     */
    public function getRenderer();

    /**
     * @param string $renderer
     * @return void
     */
    public function setRenderer($renderer);
}
