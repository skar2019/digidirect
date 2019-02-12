<?php
namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Magento\Email\Model\TemplateFactory;

/**
 * Class TransactionEmail
 * @package Ewave\CmsUpgrade\Setup\Processor
 */
class TransactionEmailProcessor extends AbstractProcessor
{
    const PK_FIELD = 'template_code';

    /**
     * @var TemplateFactory
     */
    protected $_modelFactory;

    /**
     * WidgetProcessor constructor.
     * @param TemplateFactory $modelFactory
     * @internal param MapperWidget $helper
     */
    public function __construct(
        TemplateFactory $modelFactory
    ) {
        $this->_modelFactory = $modelFactory;
    }

    /**
     * @param array $data
     * @return \Magento\Config\Model\Config
     */
    protected function _prepareModel(array $data)
    {
        /** @var \Magento\Email\Model\Template $model */
        $model = $this->_modelFactory->create();
        $model->getResource()->load($model, $data[self::PK_FIELD], self::PK_FIELD);
        $model->addData($data);

        return $model;
    }
}
