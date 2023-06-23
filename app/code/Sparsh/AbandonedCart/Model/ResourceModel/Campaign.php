<?php
/**
 * Class Campaign
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
 * Class Campaign
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Campaign extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $datetime;

    /**
     * RuleFactory
     *
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    public $ruleFactory;

    /**
     * MassgeneratorFactory
     *
     * @var \Magento\SalesRule\Model\Coupon\MassgeneratorFactory
     */
    public $massGeneratorFactory;

    /**
     * CouponFactory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    public $couponFactory;

    /**
     * Coupon
     *
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon
     */
    public $coupon;

    /**
     * Rule
     *
     * @var \Magento\SalesRule\Model\ResourceModel\Rule
     */
    public $ruleResource;

    /**
     * Initialize resource.
     *
     * @return null
     */
    public function _construct()
    {
        $this->_init('sparsh_abandoned_cart_email_campaign', 'id');
    }
}
