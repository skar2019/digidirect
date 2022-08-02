<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Digidirect\Sales\Model\Mail\Container;

class Template extends \Magento\Sales\Model\Order\Email\Container\Template
{
    /**
     * @var array
     */
    protected $templateId;

    /**
     * @var array
     */
    protected $imageAttach;

    public function setTemplateid($tempID)
    {
        $this->templateId = $tempID;
    }


    public function getTemplateid()
    {
        return $this->templateId;
    }


}