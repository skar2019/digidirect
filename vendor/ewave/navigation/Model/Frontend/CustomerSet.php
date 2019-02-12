<?php

namespace Ewave\Navigation\Model\Frontend;

/**
 * As we always in frontend use combination of set and customer session type
 * it is better to move these parameters to a new object
 */
class CustomerSet implements CustomerSetInterface
{

    /**
     * @var bool
     */
    private $isLoggedIn;

    /**
     * @var string
     */
    private $setCode;

    /**
     * @var string
     */
    private $storeCode;

    /**
     * CustomerSet constructor.
     *
     * @param bool $isLoggedIn
     * @param string $setCode
     * @param string $storeCode
     */
    public function __construct(bool $isLoggedIn, string $setCode, string $storeCode)
    {
        $this->setCode = $setCode;
        $this->storeCode = $storeCode;
        $this->isLoggedIn = $isLoggedIn;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    /**
     * @return string
     */
    public function getSetCode(): string
    {
        return $this->setCode;
    }

    /**
     * @return string
     */
    public function getStoreCode(): string
    {
        return $this->storeCode;
    }
}
