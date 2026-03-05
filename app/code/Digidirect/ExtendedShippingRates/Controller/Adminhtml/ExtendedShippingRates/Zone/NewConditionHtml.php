<?php
namespace Digidirect\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone;

use Digidirect\ExtendedShippingRates\Api\ZoneRepositoryInterface;
use Digidirect\ExtendedShippingRates\Model\ZoneFactory;
use Digidirect\ExtendedShippingRates\Ui\DataProvider\Zone\Form\Modifier\AbstractModifier;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class NewConditionHtml extends \Digidirect\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone
{
    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ZoneFactory $zoneFactory
     * @param ZoneRepositoryInterface $zoneRepository
     * @param LoggerInterface $logger
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ZoneFactory $zoneFactory,
        ZoneRepositoryInterface $zoneRepository,
        LoggerInterface $logger,
        ObjectFactory $objectFactory
    ) {
        parent::__construct($context, $coreRegistry, $zoneFactory, $zoneRepository, $logger);
        $this->objectFactory = $objectFactory;
    }

    /**
     * New condition html action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->objectFactory->create(
            $type,
            []
        )->setId(
            $id
        )->setType(
            $type
        )->setRule(
            $this->zoneFactory->create()
        )->setPrefix(
            'conditions'
        )->setFormName(
            AbstractModifier::FORM_NAME
        );
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
