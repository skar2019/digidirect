<?php

namespace Ewave\Navigation\Api\Data;

/**
 * @since 1.3.0
 */
interface TypeInterface
{
    const TYPE_NAME = 'type_name';
    const TYPE_CODE = 'menu_type_code';
    const TYPE_ID = 'type_id';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param int $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param string $code
     * @return mixed
     */
    public function setMenuTypeCode($code);

    /**
     * @return mixed
     */
    public function getMenuTypeCode();

    /**
     * @param string $typeName
     * @return mixed
     */
    public function setTypeName($typeName);

    /**
     * @return mixed
     */
    public function getTypeName();
}
