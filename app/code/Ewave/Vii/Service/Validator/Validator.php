<?php

namespace Ewave\Vii\Service\Validator;

use Ewave\AbstractGiftCard\Service\Command\CommandException;
use Ewave\AbstractGiftCard\Service\Validator\AbstractValidator;
use Ewave\AbstractGiftCard\Service\Validator\ValidatorInterface;
use Ewave\AbstractGiftCard\Service\Helper\SubjectReader;
use Psr\Log\LoggerInterface;
use Ewave\AbstractGiftCard\Service\Validator\ResultInterfaceFactory;
use Ewave\Vii\Service\Exeption\ServiceExeption;

/**
 * Class Validator
 * @package Ewave\Vii\Service\Validator
 */
class Validator extends AbstractValidator implements ValidatorInterface
{
    const SECTION_RESPONSE_CODE = 'ResponseCode';
    const SECTION_RESPONSE_MESSAGE = 'ResponseMessage';
    const SUCCESS_RESPONSE_CODE = '0';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Validator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($resultFactory);
        $this->logger = $logger;
    }

    /**
     * @param array $validationSubject
     * @return \Ewave\AbstractGiftCard\Service\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);
        try {
            if (!isset($response[self::SECTION_RESPONSE_CODE])) {
                throw new ServiceExeption(__('Response structure is not valid.'));
            }

            if ($response[self::SECTION_RESPONSE_CODE] !== self::SUCCESS_RESPONSE_CODE) {
                throw new CommandException(__("%1", $response[self::SECTION_RESPONSE_MESSAGE]));
            }
        } catch (CommandException $e) {
            return $this->_createResult(false, [$e->getMessage()]);
        } catch (ServiceExeption $exception) {
            return $this->_createResult(false, [$exception->getMessage()]);
        }
        $service = SubjectReader::readService($validationSubject);
        $service->setCheckResponse($response);
        return $this->_createResult(true);
    }
}
