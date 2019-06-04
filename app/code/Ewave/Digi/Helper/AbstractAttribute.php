<?php
namespace Ewave\Digi\Helper;


use Magento\Framework\App\ResourceConnection;

/**
 * Class AbstractEntities
 * @package Ewave\Digi\Helper
 */
class AbstractAttribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BRAND_ATTRIBUTE_CODE = 'brand';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * AbstractAttribute constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->connection = $resource->getConnection();
    }

    /**
     * @param string $brandUrlKey
     * @return int
     */
    public function getBrandIdByUrlKey(string $brandUrlKey)
    {
        $select = $this->connection->select()
            ->from(['aa' => 'ewave_aa_options'], 'option_id')
            ->joinLeft(['eao' => 'eav_attribute_option'], 'aa.option_id = eao.option_id', [])
            ->joinLeft(['eav' => 'eav_attribute'], 'eao.attribute_id = eav.attribute_id', [])
            ->where('eav.attribute_code = :brand AND aa.url_key = :url_key')
            ->limit(1);

        $option_id = $this->connection->fetchOne(
            $select,
            [
                'brand' => self::BRAND_ATTRIBUTE_CODE,
                'url_key' => $brandUrlKey
            ]
        );

        return (int) $option_id;
    }
}
