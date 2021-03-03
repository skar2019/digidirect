<?php

namespace Digidirect\Blog\Api\Data;

/**
 * Interface PostContentInterface
 */
interface PostContentInterface extends StoreViewSpecificInterface
{

    const STORE_ID = 'information_store_id';
    const Digidirect_BLOG_POST_INFORMATION_TABLE = 'Digidirect_blog_post_information';
    const Digidirect_BLOG_POST_INFORMATION_TABLE_ID = 'information_post_id';
    const Digidirect_BLOG_POST_INFORMATION_TABLE_TITLE = 'title';
    const Digidirect_BLOG_POST_INFORMATION_TABLE_CONTENT = 'content';
    const Digidirect_BLOG_POST_INFORMATION_TABLE_SHORT_CONTENT = 'short_content';
    const Digidirect_BLOG_POST_INFORMATION_TABLE_URL_KEY = 'url_key';

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getShortContent();
}
