<?php

namespace Ewave\Digi\Plugin\Ewave\LayeredNavigation\Helper;

use Ewave\Digi\Model\SeoBrandDescription;

/**
 * Class Meta
 * @package Ewave\Digi\Plugin\Ewave\LayeredNavigation\Helper
 */
class Meta
{
    /**
     * @var SeoBrandDescription
     */
    private $seoBrandDescription;

    /**
     * Meta constructor.
     * @param SeoBrandDescription $seoBrandDescription
     */
    public function __construct(
        SeoBrandDescription $seoBrandDescription
    ) {
        $this->seoBrandDescription = $seoBrandDescription;
    }

    /**
     * @param $subject
     * @param $result
     * @return array
     */
    public function afterSetPageTags($subject, $result)
    {
        $this->seoBrandDescription->setDefaultMetaInformation();
        return [$result];
    }
}
