<?php
/**
 * Class Abandoned
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AbandonedCart\Model\ResourceModel;

/**
 * Class Abandoned
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Abandoned extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * HelperData
     *
     * @var \Sparsh\AbandonedCart\Helper\Data
     */
    public $helper;

    /**
     * Initialize resource.
     *
     * @return null
     */
    public function _construct()
    {
        $this->_init('sparsh_abandoned_cart', 'id');
    }

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context context
     * @param \Sparsh\AbandonedCart\Helper\Data             $data    data
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Sparsh\AbandonedCart\Helper\Data $data
    ) {
        $this->helper = $data;
        parent::__construct($context);
    }
}
