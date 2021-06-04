<?php

namespace Ewave\Security\Model;

use Ewave\Security\Helper\Data as SecurityHelper;
use Zend\Validator\Ip;

class Security
{
    /**
     * @var SecurityHelper
     */
    protected $_helper;

    /**
     * Security constructor.
     * @param SecurityHelper $helper
     */
    public function __construct(
        SecurityHelper $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Network ranges can be specified as:
     * 1. Wildcard format:     1.2.3.* OR 1.2.3.4
     * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * 3. Start-End IP format: 1.2.3.0-1.2.3.255
     * The function will return true if the supplied IP is within the range.
     * Note little validation is done on the range inputs - it expects you to
     * use one of the above 3 formats.
     *
     * @param string $clientIp
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function checkRangeIp($clientIp)
    {
        if (!$this->_helper->isAccessEnabled()) {
            return true;
        }

        /**
         * TODO: hack for the wrong proxy configuration
         */
        if (strpos($clientIp, ',') !== false) {
            $clientIp = strstr($clientIp, ',', true);
        }

        $ranges = $this->_helper->getIpsToArray();
        foreach ($ranges as $range) {
            $range = trim($range);
            if (strpos($range, '/') !== false) {
                // $range is in IP/NETMASK format
                list($range, $netmask) = explode('/', $range, 2);

                if (strpos($netmask, '.') !== false) {
                    // $netmask is a 255.255.0.0 format
                    $netmask = str_replace('*', '0', $netmask);

                    if (!$this->_validate($range) || !$this->_validate($netmask)) {
                        continue;
                    }

                    $netmaskDec = ip2long($netmask);
                    $strategyOne = ((ip2long($clientIp) & $netmaskDec) == (ip2long($range) & $netmaskDec));
                    if (!$strategyOne) {
                        continue;
                    }
                    return true;
                } else {
                    // $netmask is a CIDR size block
                    // fix the range argument
                    $x = explode('.', $range);
                    while (count($x) < 4) {
                        $x[] = '0';
                    }
                    list($a, $b, $c, $d) = $x;
                    $a = empty($a) ? '0' : $a;
                    $b = empty($b) ? '0' : $b;
                    $c = empty($c) ? '0' : $c;
                    $d = empty($d) ? '0' : $d;
                    $range = sprintf('%s.%s.%s.%s', $a, $b, $c, $d);

                    if (!$this->_validate($range)) {
                        continue;
                    }

                    $rangeDec = ip2long($range);
                    $ipDec = ip2long($clientIp);

                    # Strategy 2 - Use math to create it
                    $wildcardDec = pow(2, (32 - $netmask)) - 1;
                    $netmaskDec = ~$wildcardDec;

                    $strategyTwo = (($ipDec & $netmaskDec) == ($rangeDec & $netmaskDec));
                    if (!$strategyTwo) {
                        continue;
                    }
                    return true;
                }
            } else {
                /**
                 * Range might be 255.255.*.* or 1.2.3.0-1.2.3.255
                 */
                if (strpos($range, '*') !== false) { // a.b.*.* format
                    // Just convert to A-B format by setting * to 0 for A and 255 for B
                    $lower = str_replace('*', '0', $range);
                    $upper = str_replace('*', '255', $range);
                    $range = $lower . '-' . $upper;
                }

                /**
                 * A-B format
                 */
                if (strpos($range, '-') !== false) {
                    list($lower, $upper) = explode('-', $range, 2);

                    if (!$this->_validate($lower) || !$this->_validate($upper)) {
                        continue;
                    }
                    $lowerDec = (float)sprintf('%u', ip2long($lower));
                    $upperDec = (float)sprintf('%u', ip2long($upper));
                    $ipDec = (float)sprintf('%u', ip2long($clientIp));
                    $startEndFormat = (($ipDec >= $lowerDec) && ($ipDec <= $upperDec));
                    if (!$startEndFormat) {
                        continue;
                    }
                    return true;
                }

                /**
                 * IP might be 1.2.3.4
                 */
                if ($this->_validate($range) && (ip2long($clientIp) === ip2long($range))) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Validation IP
     * @param string $ip
     * @return bool
     */
    protected function _validate($ip)
    {
        $validator = new Ip;
        return $validator->isValid($ip);
    }
}
