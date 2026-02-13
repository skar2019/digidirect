<?php

namespace Digidirect\EventbriteEvents\Block;

use Magento\Framework\View\Element\Template;
use Digidirect\EventbriteEvents\Helper\Data as EventHelper;

class Events extends Template
{
    protected $eventHelper;

    public function __construct(
        Template\Context $context,
        EventHelper $eventHelper,
        array $data = []
    ) {
        $this->eventHelper = $eventHelper;
        parent::__construct($context, $data);
    }

    public function getEvents()
    {
        return $this->eventHelper->getEvents();
        // return ["block working"];
        //redeploy
    }
}
