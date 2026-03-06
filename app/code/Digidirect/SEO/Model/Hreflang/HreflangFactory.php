<?php

namespace Digidirect\SEO\Model\Hreflang;

use Digidirect\SEO\Model\Hreflang\StoreCodeName;

/**
 * Class HreflangFactory
 */
class HreflangFactory
{
    /**
     *
     */
    const MAIN_OBJECT_PATH = '\\Digidirect\\SEO\\Model\\Hreflang\\';

    /**
     * @var StoreCodeName
     */
    private $storeCodeName;

    /**
     * HreflangFactory constructor.
     * @param StoreCodeName $storeCodeName
     */
    public function __construct(
        StoreCodeName $storeCodeName
    ) {
        $this->storeCodeName = $storeCodeName;
    }

    /**
     * @param $objectName
     * @return mixed|null
     */
    public function create($objectName)
    {
        $className = self::MAIN_OBJECT_PATH.$objectName;

        if ($className === StoreCodeName::class) {
            return $this->storeCodeName;
        }

        return null;
    }
}
