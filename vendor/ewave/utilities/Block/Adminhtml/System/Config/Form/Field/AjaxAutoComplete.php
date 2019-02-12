<?php
namespace Ewave\Utilities\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class AjaxAutoComplete
 * @package Ewave\Utilities\Block\Adminhtml\System\Config\Form\Field
 */
class AjaxAutoComplete extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var AjaxAutoCompleteInterface
     */
    protected $ajaxAutoCompleteEntity;

    /**
     * AjaxAutoComplete constructor.
     * @param Context $context
     * @param \Ewave\Utilities\Model\System\Config\Frontend\AjaxAutoCompleteInterface $ajaxAutoCompleteEntity
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Ewave\Utilities\Model\System\Config\Frontend\AjaxAutoCompleteInterface $ajaxAutoCompleteEntity,
        array $data = []
    ) {
        $this->ajaxAutoCompleteEntity = $ajaxAutoCompleteEntity;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return \Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $ajaxUrl = $this->ajaxAutoCompleteEntity->getAjaxUrl();
        $currentId = $this->ajaxAutoCompleteEntity->getCurrentId();
        $currentName = $this->ajaxAutoCompleteEntity->getNameById($currentId);

        $block = $this->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Ewave_Utilities::ajax_auto_complete.phtml')
            ->setData(
                [
                    'ajax_url'        => $this->getUrl($ajaxUrl),
                    'current_id'      => $currentId,
                    'current_name'    => $currentName,
                    'element_name'    => $element->getName(),
                    'element_html_id' => $element->getHtmlId()
                ]
            );

        return $block->toHtml();
    }
}
