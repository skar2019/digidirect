<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Slider;

use Amasty\BannerSlider\Api\Data\SliderInterface;
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
    const ADMIN_RESOURCE = 'Amasty_BannerSlider::sliders_slider';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Amasty\BannerSlider\Model\Repository\SliderRepository
     */
    protected $repository;

    /**
     * @var \Amasty\BannerSlider\Model\ResourceModel\Slider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Amasty\BannerSlider\Model\SliderFactory
     */
    protected $modelFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \Amasty\BannerSlider\Model\Repository\SliderRepository $repository,
        \Amasty\BannerSlider\Model\ResourceModel\Slider\CollectionFactory $collectionFactory,
        \Amasty\BannerSlider\Model\SliderFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
    }

    /**
     * Execute action for group
     *
     * @param SliderInterface $slider
     */
    abstract protected function itemAction(SliderInterface $slider);

    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $model) {
                    $this->itemAction($model);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
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
