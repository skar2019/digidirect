<?php

namespace Ewave\Emailblocker\Helper;

/**
 * Class Data
 * @package Ewave\Emailblocker\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const
        EMAIL_BLOCKER_ENABLED = 'ewave_emailblocker/general/emailblocker_enabled',
        EMAIL_EXEPTIONAL_EMAIL_ADDRESSES = 'ewave_emailblocker/general/exeptional_email_addresses',
        EMAIL_EXEPTIONAL_DOMAINS = 'ewave_emailblocker/general/exeptional_domains';

    /**
     * @return mixed
     */
    public function emailBlockerIsEnabled()
    {
        return $this->scopeConfig->getValue(self::EMAIL_BLOCKER_ENABLED);
    }

    /**
     * @return array|bool
     */
    protected function _getExceptionalEmails()
    {
        $allowedEmailsString = $this->scopeConfig->getValue(self::EMAIL_EXEPTIONAL_EMAIL_ADDRESSES);
        if ($allowedEmailsString) {
            $allowedEmailsArray = explode(',', $allowedEmailsString);
            array_walk($allowedEmailsArray, [$this, 'trimAllowedValues']);
            return $allowedEmailsArray;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    protected function _getExceptionalDomains()
    {
        $allowedDomainsString = $this->scopeConfig->getValue(self::EMAIL_EXEPTIONAL_DOMAINS);
        if ($allowedDomainsString) {
            $allowedDomainsArray = explode(',', $allowedDomainsString);
            array_walk($allowedDomainsArray, [$this, 'trimAllowedValues']);
            return $allowedDomainsArray;
        }
        return false;
    }

    /**
     * @param string $allowedValuesArray
     * @return void
     */
    public function trimAllowedValues(string &$allowedValuesArray)
    {
        $allowedValuesArray = trim($allowedValuesArray);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function needToBlock(string $email)
    {
        if (!$this->emailBlockerIsEnabled()) {
            return false;
        }

        $domains = $this->_getExceptionalDomains();
        $emails = $this->_getExceptionalEmails();

        if (($emails && in_array($email, $emails)) || (is_array($domains) && $this->validDomain($email, $domains))) {
            return false;
        }
        return true;
    }

    /**
     * @param string $email
     * @param array $domains
     * @return bool
     */
    protected function validDomain(string $email, array $domains)
    {
        $regExp = '/^(http:\/\/|https:\/\/){0,1}(www\.){0,1}([0-9a-z_\-\.]*[0-9a-z]*\.[a-z]{2,5})$/iu';

        $emailDomain = substr(strrchr($email, "@"), 1);

        foreach ($domains as $domain) {

            if (preg_match($regExp, $domain, $parts) && $emailDomain == $parts[3]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $address
     * @return mixed
     */
    public function logBlockedAddress(string $address)
    {
        $this->_logger->info('blocked address by email blocker: ' . $address);
        return $address;
    }
}
