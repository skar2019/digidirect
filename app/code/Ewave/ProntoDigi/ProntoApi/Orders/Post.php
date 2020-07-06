<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders;

use Ewave\Pronto\ProntoApi\Process;

class Post extends Process
{
    const PROCESS_CODE = 'pronto_orders_post';
    const ORDER_RUN_OPTION_PARAMETER = 'order';
}
