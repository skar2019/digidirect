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

namespace MageSpark\Base\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigProviderAbstract
 *
 * @package MageSpark\Base\Model
 */
abstract class ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     * @var string '{section}/'
     */
    protected $pathPrefix = '/';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Stored values by scopes
     *
     * @var array
     */
    protected $data = [];

    /**
     * ConfigProviderAbstract constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     *
     * @throws \LogicException
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        if ($this->pathPrefix === '/') {
            throw new \LogicException('$pathPrefix should be declared');
        }
    }

    /**
     * clear local storage
     *
     * @return void
     */
    public function clean()
    {
        $this->data = [];
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $path '{group}/{field}'
     * @param int|ScopeInterface|null $storeId Scope code
     * @param string $scope
     *
     * @return mixed
     */
    protected function getValue(
        $path,
        $storeId = null,
        $scope = ScopeInterface::SCOPE_STORE
    ) {
        if ($storeId instanceof \Magento\Framework\App\ScopeInterface) {
            $storeId = $storeId->getId();
        }
        $scopeKey = $storeId;
        if ($scopeKey === null) {
            $scopeKey = 'current_';
        }
        $scopeKey .= $scope;
        if (empty($this->data[$path][$scopeKey])) {
            $this->data[$path][$scopeKey] = $this->getValue($this->pathPrefix . $path, $scope, $storeId);
        }

        return $this->data[$path][$scopeKey];
    }

    /**
     * An alias for scope config with scope type Default
     *
     * @param string $path '{group}/{field}'
     *
     * @return mixed
     */
    protected function getGlobalValue($path)
    {
        return $this->getValue($path, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * Return falg value either true or false
     *
     * @param string $path '{group}/{field}'
     * @param int|ScopeInterface|null $storeId
     * @param string $scope
     *
     * @return bool
     */
    protected function isSetFlag(
        $path,
        $storeId = null,
        $scope = ScopeInterface::SCOPE_STORE
    ) {
        return (bool)$this->getValue($path, $storeId, $scope);
    }

    /**
     * Return global flag with default scope type
     *
     * @param string $path '{group}/{field}'
     *
     * @return bool
     */
    protected function isSetGlobalFlag($path)
    {
        return $this->isSetFlag($path, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
