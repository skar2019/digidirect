<?php

namespace Ewave\AbstractGiftCard\Service\Validator;

use Magento\Framework\Exception\NotFoundException;
use Ewave\AbstractGiftCard\Service\ConfigInterface;
use Ewave\AbstractGiftCard\Service\Validator\ResultInterfaceFactory;

/**
 * Class CountryValidator
 * @package Ewave\AbstractGiftCard\Service\Validator
 * @api
 */
class CountryValidator extends AbstractValidator
{
    /**
     * @var \Ewave\AbstractGiftCard\Service\ConfigInterface
     */
    private $_config;

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param \Ewave\AbstractGiftCard\Service\ConfigInterface $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        ConfigInterface $config
    ) {
        $this->_config = $config;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return bool
     * @throws NotFoundException
     * @throws \Exception
     */
    public function validate(array $validationSubject)
    {
        $isValid = true;
        $storeId = $validationSubject['storeId'];

        if ((int)$this->_config->getValue('allowspecific', $storeId) === 1) {
            $availableCountries = explode(
                ',',
                $this->_config->getValue('specificcountry', $storeId)
            );

            if (!in_array($validationSubject['country'], $availableCountries)) {
                $isValid =  false;
            }
        }

        return $this->createResult($isValid);
    }
}
