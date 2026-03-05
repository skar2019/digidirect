<?php
namespace Digidirect\AddressVerification\Model\Import;

use Magento\Framework\Api\ObjectFactory;

/**
 * Class ParserFactory
 * @package Digidirect\AddressVerification\Model\Import
 */
class ParserFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectFactory
     */
    protected $objectFactory = null;

    /**
     * @var array
     */
    protected $types = [];

    /**
     * ParserFactory constructor.
     * @param ObjectFactory $objectFactory
     * @param array $types
     */
    public function __construct(
        ObjectFactory $objectFactory,
        array $types = []
    ) {
        $this->objectFactory = $objectFactory;
        $this->types = $types;
    }

    /**
     * @param string $type
     * @return SourceAdapterInterface|AbstractSource
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($type)
    {
        $type = strtolower($type);
        if (!array_key_exists($type, $this->types) || !class_exists($this->types[$type])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File is not supported', $type)
            );
        }
        $adapter = $this->objectFactory->get($this->types[$type]);
        if (!$adapter instanceof SourceAdapterInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Adapter must be an instance of \Digidirect\AddressVerification\Model\Import\SourceAdapterInterface')
            );
        }
        return $adapter;
    }
}
