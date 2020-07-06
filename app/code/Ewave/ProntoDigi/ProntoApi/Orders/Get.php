<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders;

use Ewave\Pronto\ProntoApi\Process;

class Get extends Process
{
    const PROCESS_CODE = 'pronto_orders_get';
    const ORDER_RUN_OPTION_PARAMETER = 'order';
}
