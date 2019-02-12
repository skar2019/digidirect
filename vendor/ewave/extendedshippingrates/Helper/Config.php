<?php

namespace Ewave\ExtendedShippingRates\Helper;

use Ewave\Utilities\Model\System\Config\Backend\DefaultOptionModel;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_HIDED_METHODS_RELATIONS = 'ewave_extendedshippingrates/hide_methods/hided_methods_relations';

    const COUNTRY_COLUMN = 'country_column';
    const STATE_COLUMN = 'state_column';

    /**
     * @var DefaultOptionModel
     */
    protected $defaultOptionModel;

    /**
     * @var array
     */
    protected $hidedMethodsArr;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param DefaultOptionModel $defaultOptionModel
     */
    public function __construct(
        Context $context,
        DefaultOptionModel $defaultOptionModel
    ) {
        parent::__construct($context);

        $this->defaultOptionModel = $defaultOptionModel;
    }

    /**
     * @param null $scopeCode
     *
     * @return array
     */
    public function getHidedMethods($scopeCode = null)
    {
        if (null === $this->hidedMethodsArr) {
            $hidedMethods = $this->scopeConfig->getValue(
                self::XML_PATH_HIDED_METHODS_RELATIONS,
                ScopeInterface::SCOPE_STORE,
                $scopeCode
            );

            $this->hidedMethodsArr = [];
            if (!empty($hidedMethods)) {
                $hidedMethodsRows = $this->defaultOptionModel->convertValueToArray($hidedMethods);
                foreach ($hidedMethodsRows as $hidedMethodsRow) {
                    $this->hidedMethodsArr[$hidedMethodsRow[self::COUNTRY_COLUMN]] =
                        $hidedMethodsRow[self::STATE_COLUMN];
                }
            }
        }

        return $this->hidedMethodsArr;
    }
}
