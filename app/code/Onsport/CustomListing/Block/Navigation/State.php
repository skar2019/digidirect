<?php

namespace Onsport\CustomListing\Block\Navigation;

class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
	public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Onsport\CustomListing\Model\Layer\Resolver $layerResolver,
            array $data = []
	) {
            parent::__construct(
                $context,
                $layerResolver,
                $data
            );
	}
}
