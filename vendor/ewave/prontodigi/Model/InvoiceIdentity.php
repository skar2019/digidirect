<?php

namespace Ewave\ProntoDigi\Model;

class InvoiceIdentity extends \Magento\Sales\Model\Order\Email\Container\InvoiceIdentity
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }
}
