<?php

namespace Ewave\Migration\Helper;

/**
 * Class Profiler
 *
 * @author Michael Marchanka <michail.marchenko@ewave.com>
 */
class Profiler
{
    /**
     * @return string
     */
    public function getProcessMemoryUsage(): string
    {
        $status = file_get_contents('/proc/' . getmypid() . '/status');

        $matchArr = [];
        preg_match_all('~^(VmRSS|VmSwap):\s*([0-9]+).*$~im', $status, $matchArr);

        if (!isset($matchArr[2][0]) || !isset($matchArr[2][1])) {
            return '';
        }

        return sprintf(
            'Vm Resident Set Size (resides in RAM): %d MB. Vm Swap: %d MB. Total: %d MB',
            round(floatval($matchArr[2][0] / 1024), 2),
            round(floatval($matchArr[2][1] / 1024), 2),
            round(floatval(($matchArr[2][0] + $matchArr[2][1]) / 1024), 2)
        );
    }
}
