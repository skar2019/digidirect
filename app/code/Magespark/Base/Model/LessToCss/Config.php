<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Model\LessToCss;

use Magento\Framework\Config\CacheInterface;
use MageSpark\Base\Model\LessToCss\Config\Reader;
use Magento\Framework\Config\Data;

/**
 * Class Config
 *
 * @package MageSpark\Base\Model\LessToCss
 */
class Config extends Data
{
    const CACHE_ID = 'magespark_less_to_css';

    /**
     * Initialize reader and cache.
     *
     * @param Reader $reader
     * @param CacheInterface $cache
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, self::CACHE_ID);
    }
}
