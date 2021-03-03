<?php

namespace Digidirect\Navigation\Api\Data;

interface MenuItemInterface
{
    const ENTITY_ID = 'entity_id';
    const MENU_ITEM_CODE = 'menu_item_code';
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
    public function setMenuItemCode($code);

    /**
     * @return mixed
     */
    public function getMenuItemCode();

    /**
     * @param int $typeId
     * @return mixed
     */
    public function setTypeId($typeId);

    /**
     * @return mixed
     */
    public function getTypeId();
}
