<?php

namespace Digidirect\JirehTestEmail\Block;

class Booking extends \Magento\Framework\View\Element\Template
{
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
       }

    /**
     * Get form action URL for POST booking request
     *
     * @return string
     */
    public function getFormAction()
    {
            // companymodule is given in routes.xml
            // controller_name is folder name inside controller folder
            // action is php file name inside above controller_name folder

        return '/digidirectjirehtestemail/index/booking';
        // return '/digidirectjirehtestemail/controller_name/action';
        // here controller_name is index, action is booking
    }
}