<?php
namespace Ewave\ProductCalculator\Plugin\Controller\Adminhtml;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\AbstractResult;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\PageCache\Model\Cache\Type as FullPageCache;
use Magento\Framework\App\Cache\Type\Block as BlockCache;

class CacheInvalidatePlugin
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var TypeListInterface
     */
    protected $typeList;

    /**
     * @var array
     */
    protected $invalidateTypes;

    /**
     * CacheInvalidatePlugin constructor.
     * @param Context $context
     * @param TypeListInterface $typeList
     * @param array $invalidateTypes
     */
    public function __construct(
        Context $context,
        TypeListInterface $typeList,
        array $invalidateTypes = []
    ) {
        $this->messageManager = $context->getMessageManager();
        $this->typeList = $typeList;
        $this->invalidateTypes = $invalidateTypes ?: [
            FullPageCache::TYPE_IDENTIFIER,
            BlockCache::TYPE_IDENTIFIER,
        ];
    }

    /**
     * @param AbstractAction $subject
     * @param AbstractResult $result
     * @return AbstractResult
     */
    public function afterExecute(AbstractAction $subject, $result)
    {
        foreach ($this->messageManager->getMessages(false)->getItems() as $message) {
            if ($message->getType() == MessageInterface::TYPE_SUCCESS) {
                $this->typeList->invalidate($this->invalidateTypes);
                break;
            }
        }
        return $result;
    }
}
