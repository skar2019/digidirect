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

namespace MageSpark\Base\Model\Config\Backend;

use MageSpark\Base\Model\Source\NotificationType;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\Data\ProcessorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use MageSpark\Base\Model\AdminNotification\Messages;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Unsubscribe
 *
 * @package MageSpark\Base\Model\Config\Backend
 */
class Unsubscribe extends Value implements ProcessorInterface
{
    const PATH_TO_FEED_IMAGES = 'https://notification.magespark.com/';

    /**
     * @var Messages
     */
    private $messageManager;

    /**
     * @var NotificationType
     */
    private $notificationType;

    /**
     * Unsubscribe constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Messages $messageManager
     * @param NotificationType $notificationType
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Messages $messageManager,
        NotificationType $notificationType,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->messageManager = $messageManager;
        $this->notificationType = $notificationType;
    }

    /**
     * @return Value
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $value = explode(',', $this->getValue());
            if (in_array(NotificationType::UNSUBSCRIBE_ALL, $value)) {
                $changes = [NotificationType::UNSUBSCRIBE_ALL];
            } else {
                $oldValue = explode(',', $this->getOldValue());
                $changes = array_diff($oldValue, $value);
                $changes = array_diff($changes, [NotificationType::UNSUBSCRIBE_ALL]);
            }

            if (!empty($changes)) {
                foreach ($changes as $change) {
                    $message = $this->generateMessage($change);
                    $this->messageManager->addMessage($message);
                }
            } else {
                $this->messageManager->clear();
            }
        }

        return parent::afterSave();
    }

    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $value;
    }

    /**
     * Generate the message
     *
     * @param $change
     * @return string
     */
    private function generateMessage($change)
    {
        $message = '';
        $titles = $this->notificationType->toOptionArray();
        foreach ($titles as $title) {
            if ($title['value'] == $change) {
                if ($change == NotificationType::UNSUBSCRIBE_ALL) {
                    $label = __('All Notifications');
                } else {
                    $label = $title['label'];
                }

                $message = '<img src="' . $this->generateLink($change) .'"/><span>'
                    . __('You have successfully unsubscribed from %1.', $label) .'</span>';
                break;
            }
        }

        return $message;
    }

    /**
     * Generating the image link
     *
     * @param $change
     * @return string
     */
    private function generateLink($change)
    {
        $change = mb_strtolower($change);
        return self::PATH_TO_FEED_IMAGES . $change . '.svg';
    }
}
