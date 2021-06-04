<?php
namespace Ewave\ProductOverlay\Model\Rule;

use Ewave\ProductOverlay\Model\Overlays;

/**
 * Interface ProcessorInterface
 *
 * @package Ewave\ProductOverlay\Model\Rule
 */
interface ProcessorInterface
{
    /**
     * @param Overlays $overlay
     * @return mixed
     */
    public function isApplicable(Overlays $overlay);
}
