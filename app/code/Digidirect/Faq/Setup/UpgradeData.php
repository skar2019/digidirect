<?php
namespace Digidirect\Faq\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->context = $context;

        if ($this->compareVersions('1.0.3')) {
            $this->createCustomerQuestionsCategory();
        }
    }

    /**
     * Create "Customers' Questions category"
     * Set it as category for questions in system -> configuration
     * @return void
     */
    protected function createCustomerQuestionsCategory()
    {
        $this->setup->getConnection()->insert(
            'digidirect_faq_category',
            [
                'title' => 'Customers\' Questions',
                'status' => 0,
                'identifier' => 'customer_questions',
            ]
        );

        $select = $this->setup->getConnection()
            ->select()
            ->from('digidirect_faq_category')
            ->where($this->setup->getConnection()->quoteInto('identifier = ?', 'customer_questions'));

        $categoryId = $this->setup->getConnection()->fetchOne($select);

        if ($categoryId) {
            $this->setup->getConnection()->insert(
                'core_config_data',
                [
                    'scope' => 'default',
                    'scope_id' => 0,
                    'path' => 'digidirect_faq/customer_questions/category_id',
                    'value' => $categoryId,
                ]
            );
        }
    }

    /**
     * @param string $version
     * @return bool
     */
    protected function compareVersions($version)
    {
        return $this->context->getVersion() && (version_compare($this->context->getVersion(), $version) < 0);
    }
}
