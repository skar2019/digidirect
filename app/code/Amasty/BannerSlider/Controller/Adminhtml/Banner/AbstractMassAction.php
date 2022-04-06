<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Banner;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_BannerSlider::banners_banner';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Amasty\BannerSlider\Model\Repository\BannerRepository
     */
    protected $repository;

    /**
     * @var \Amasty\BannerSlider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Amasty\BannerSlider\Model\BannerFactory
     */
    protected $modelFactory;

    /**
     * @var Filter
     */
    private $filter;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \Amasty\BannerSlider\Model\Repository\BannerRepository $repository,
        \Amasty\BannerSlider\Model\ResourceModel\Banner\CollectionFactory $collectionFactory,
        \Amasty\BannerSlider\Model\BannerFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
        $this->filter = $filter;
    }

    /**
     * Execute action for group
     *
     * @param BannerInterface $banner
     */
    abstract protected function itemAction(BannerInterface $banner);

    /**
     * Mass action execution
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        if ($size = $collection->getSize()) {
            try {
                $collection->addDynamicData();
                foreach ($collection->getItems() as $model) {
                    $this->itemAction($model);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($size));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
