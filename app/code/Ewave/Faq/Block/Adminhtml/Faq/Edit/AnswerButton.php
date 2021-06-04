<?php
namespace Ewave\Faq\Block\Adminhtml\Faq\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class AnswerButton
 *
 * @package Ewave\Faq\Block\Adminhtml\Faq\Edit
 */
class AnswerButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $currentItem = $this->getCurrentFaqItem();

        if ($currentItem && $currentItem->getId() && $currentItem->getCustomerEmail() && $currentItem->getAnswer()) {
            $data = [
                'label' => __('Send Answer'),
                'class' => 'answer',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to send answer to customer?'
                ) . '\', \'' . $this->getAnswerUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getAnswerUrl()
    {
        return $this->getUrl('*/*/answer', ['id' => $this->getFaqId()]);
    }
}
