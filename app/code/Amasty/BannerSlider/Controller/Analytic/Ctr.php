<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Analytic;

use Amasty\BannerSlider\Model\Analytics\Temp\AddActionInTempTable;
use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

abstract class Ctr implements HttpGetActionInterface
{
    /**
     * @var AddActionInTempTable
     */
    private $addActionInTempTable;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    public function __construct(
        AddActionInTempTable $addActionInTempTable,
        RequestInterface $request,
        ResultFactory $resultFactory,
        FormKeyValidator $formKeyValidator
    ) {
        $this->addActionInTempTable = $addActionInTempTable;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->formKeyValidator = $formKeyValidator;
    }

    abstract protected function getType(): string;

    public function execute()
    {
        if ($this->request->isAjax() && $this->formKeyValidator->validate($this->request)) {
            $this->addActionInTempTable->execute(
                $this->getType(),
                [
                    [
                        TempEntity::BANNER_ID => (int) $this->request->getParam('id')
                    ]
                ]
            );
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData([]);
    }
}
