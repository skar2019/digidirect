<?php
namespace Ewave\ProductAttachment\Api\Data;

/**
 * Interface AttachmentInterface
 * @package Ewave\ProductAttachment\Api
 */
interface AttachmentInterface
{
    const ALL_STORE_ID = 0;

    const ENTITY_ID = 'entity_id';
    const FILE_PATH = 'file_path';
    const STATUS = 'status';
    const ATTACHMENT_ID = 'attachment_id';
    const ATTACHED = 'attached';
    const STORE_ID = 'store_id';

    /**
     * @return bool
     */
    public function saveAttachmentFile();

    /**
     * @return []
     */
    public static function getAttachmentAttributes();

    /**
     * @return string
     */
    public function getFileFormat();

    /**
     * @return string
     */
    public function getFileSize();

    /**
     * @return string
     */
    public function getDownloadableFileName();
}
