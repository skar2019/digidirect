<?php

namespace Ewave\AI\Preferences\Magento\ConfigurableImportExport\Model\Import\Product\Type;

use Ewave\AI\Preferences\Model\Import\Product as ImportProductAI;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

class Configurable extends \Magento\ConfigurableImportExport\Model\Import\Product\Type\Configurable
{
    /**
     * Save product type specific data.
     *
     * @throws \Exception
     * @return \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function saveData()
    {
        $ignoreScopeForCollectSuperData = $this->shouldIgnoreScopeForCollectSuperData();
        if (!$ignoreScopeForCollectSuperData) {
            return parent::saveData();
        }

        $newSku = $this->_entityModel->getNewSku();
        $oldSku = $this->_entityModel->getOldSku();
        $this->_productSuperData = [];
        $this->_productData = null;

        while ($bunch = $this->_entityModel->getNextBunch()) {
            if ($this->_entityModel->getBehavior() == \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND) {
                $this->_loadSkuSuperDataForBunch($bunch);
            }
            if (!$this->configurableInBunch($bunch)) {
                continue;
            }

            $this->_superAttributesData = [
                'attributes' => [],
                'labels' => [],
                'super_link' => [],
                'relation' => [],
            ];

            $this->_simpleIdsToDelete = [];

            $this->_loadSkuSuperAttributeValues($bunch, $newSku, $oldSku);

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->_entityModel->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }
                // remember SCOPE_DEFAULT row data
                $scope = $this->_entityModel->getRowScope($rowData);
                if (($ignoreScopeForCollectSuperData || ImportProduct::SCOPE_DEFAULT == $scope) &&
                    !empty($rowData[ImportProduct::COL_SKU])) {
                    $sku = strtolower($rowData[ImportProduct::COL_SKU]);
                    $this->_productData = isset($newSku[$sku]) ? $newSku[$sku] : $oldSku[$sku];

                    if ($this->_type != $this->_productData['type_id']) {
                        $this->_productData = null;
                        continue;
                    }
                    $this->_collectSuperData($rowData);
                }
            }

            // save last product super data
            $this->_processSuperData();

            $this->_deleteData();

            $this->_insertData();
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function shouldIgnoreScopeForCollectSuperData()
    {
        $behavior = $this->_entityModel->getBehavior();
        if ($behavior !== \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND) {
            return false;
        }

        $parameters = $this->_entityModel->getParameters();
        return !empty($parameters[ImportProductAI::IMPORT_PARAM_IGNORE_SCOPE_FOR_CONFIGURABLE_COLLECT_SUPER_DATA]);
    }
}
