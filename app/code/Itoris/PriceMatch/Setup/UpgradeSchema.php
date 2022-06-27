<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_ITORIS_PRICE_MATCH
 * @copyright  Copyright (c) 2018 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\PriceMatch\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Digidirect\AI\Api\Data\ScheduleInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) 
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('itoris_pricematch_data'),
                'contact_number',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Contact Number',
                    'after' => 'email',
                ]
            ); 
        }
        
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $setup->run("
                ALTER TABLE `{$setup->getTable('itoris_pricematch_data')}`
                    MODIFY `status` VARCHAR(255),
                    MODIFY `method` VARCHAR(255)"
            );
            $setup->run("
                ALTER TABLE `{$setup->getTable('itoris_pricematch_setting')}`
                    MODIFY `attr_code` VARCHAR(255) NOT NULL"
            );
        }
        
        $setup->endSetup();
    }
}
