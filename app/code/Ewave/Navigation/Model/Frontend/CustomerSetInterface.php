<?php

namespace Ewave\Navigation\Model\Frontend;

/**
 * As we always in frontend use combination of set and customer session type
 * it is better to move these parameters to a new object
 */
interface CustomerSetInterface
{
    const IS_LOGGED_IN = 'is_logged_in';
    const SET_CODE = 'set_code';
    const STORE_CODE = 'store_code';

    /**
     * @return bool
     */
    public function isLoggedIn(): bool;

    /**
     * @return string
     */
    public function getSetCode(): string;

    /**
     * @return string
     */
    public function getStoreCode(): string;
}
