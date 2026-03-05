<?php
namespace Digidirect\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote;

use Digidirect\ExtendedShippingRates\Api\RuleRepositoryInterface;
use Digidirect\ExtendedShippingRates\Model\RuleFactory;
use Digidirect\ExtendedShippingRates\Ui\DataProvider\Quote\Form\QuoteDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Psr\Log\LoggerInterface;

class NewConditionHtml extends \Digidirect\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote
{
    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param RuleFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param LoggerInterface $logger
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        LoggerInterface $logger,
        ObjectFactory $objectFactory
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter, $ruleFactory, $ruleRepository, $logger);
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
            $this->ruleFactory->create()
        )->setPrefix(
            'conditions'
        )->setFormName(
            QuoteDataProvider::FORM_NAME
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
