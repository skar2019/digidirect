<?php
namespace Digidirect\EventbriteEvents\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Digidirect\EventbriteEvents\Model\Eventbrite;

class Index extends Action
{
    private $jsonFactory;
    private $eventbrite;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Eventbrite $eventbrite
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->eventbrite  = $eventbrite;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        return $result->setData($this->eventbrite->getEvents());
    }
}
