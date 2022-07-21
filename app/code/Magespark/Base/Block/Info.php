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

namespace MageSpark\Base\Block;

use Magento\Framework\App\State;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\LayoutFactory;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Info
 *
 * @package MageSpark\Base\Block
 */
class Info extends Fieldset
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var CollectionFactory
     */
    private $cronFactory;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * Info constructor.
     *
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param LayoutFactory $layoutFactory
     * @param CollectionFactory $cronFactory
     * @param DirectoryList $directoryList
     * @param Reader $reader
     * @param ResourceConnection $resourceConnection
     * @param ProductMetadataInterface $productMetadata
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        LayoutFactory $layoutFactory,
        CollectionFactory $cronFactory,
        DirectoryList $directoryList,
        Reader $reader,
        ResourceConnection $resourceConnection,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->layoutFactory = $layoutFactory;
        $this->scopeConfig   = $context->getScopeConfig();
        $this->cronFactory = $cronFactory;
        $this->directoryList = $directoryList;
        $this->resourceConnection = $resourceConnection;
        $this->productMetadata = $productMetadata;
        $this->reader = $reader;
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $html .= $this->getMagentoMode($element);
        $html .= $this->getMagentoPathInfo($element);
        $html .= $this->getOwnerInfo($element);
        $html .= $this->getSystemTime($element);
        $html .= $this->getCronInfo($element);

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Get the field rendrer from config form
     *
     * @return BlockInterface
     */
    private function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $layout = $this->layoutFactory->create();

            $this->_fieldRenderer = $layout->createBlock(
                Field::class
            );
        }

        return $this->_fieldRenderer;
    }

    /**
     * Get the magento mode
     *
     * @param $fieldset
     * @return string
     * @throws \Exception
     */
    private function getMagentoMode($fieldset)
    {
        $label = __("Magento Mode");

        $env = $this->reader->load();
        $mode = isset($env[State::PARAM_MODE]) ? $env[State::PARAM_MODE] : '';

        return $this->getFieldHtml($fieldset, 'magento_mode', $label, ucfirst($mode));
    }

    /**
     * Get information about magento path
     *
     * @return string
     */
    private function getMagentoPathInfo($fieldset)
    {
        $label = __("Magento Path");
        $path = $this->directoryList->getRoot();

        return $this->getFieldHtml($fieldset, 'magento_path', $label, $path);
    }


    /**
     * Compare the magento version and get the system time
     *
     * @param AbstractElement $fieldset
     * @return string
     */
    private function getSystemTime($fieldset)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2', '>=')) {
            $time = $this->resourceConnection->getConnection()->fetchOne("select now()");
        } else {
            $time = $this->_localeDate->date()->format('H:i:s');
        }
        return $this->getFieldHtml($fieldset, 'mysql_current_date_time', __("Current Time"), $time);
    }

    /**
     * @param $userInfo
     * @param $fieldset
     * @return string
     */
    private function fieldHtml($userInfo, $fieldset)
    {
        $label = __("Server User");

        return $this->getFieldHtml(
            $fieldset,
            'magento_user',
            $label,
            $userInfo
        );
    }

    /**
     * Get the cron information
     *
     * @param AbstractElement $fieldset
     * @return string
     */
    private function getCronInfo($fieldset)
    {
        $crontabCollection = $this->cronFactory->create();
        $crontabCollection->setOrder('schedule_id')->setPageSize(5);

        if ($crontabCollection->count() === 0) {
            $value = '<div class="red">';
            $value .= __('No cron jobs found') . "</div>";
            $value .=
                "<a target='_blank'
                  href='https://support.magespark.com/index.php?/Knowledgebase/Article/View/72/24/magento-cron'>" .
                __("Learn more") .
                "</a>";
        } else {
            $value = '<table>';
            foreach ($crontabCollection as $crontabRow) {
                $value .=
                    '<tr>' .
                        '<td>' . $crontabRow['job_code'] . '</td>' .
                        '<td>' . $crontabRow['status'] . '</td>' .
                        '<td>' . $crontabRow['created_at'] . '</td>' .
                    '</tr>';
            }
            $value .= '</table>';
        }

        $label = __('Cron (Last 5)');

        return $this->getFieldHtml($fieldset, 'cron_configuration', $label, $value);
    }

    /**
     * Add field by fieldset
     *
     * @param AbstractElement $fieldset
     * @param string $fieldName
     * @param string $label
     * @param string $value
     * @return string
     */
    private function getFieldHtml($fieldset, $fieldName, $label = '', $value = '')
    {
        $field = $fieldset->addField($fieldName, 'label', [
            'name'  => 'dummy',
            'label' => $label,
            'after_element_html' => $value,
        ])->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
