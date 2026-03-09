<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Controller\Adminhtml\Ajax\Template;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_PageBuilder::template_save';

    /**
     * @var \Magezon\PageBuilder\Model\TemplateFactory
     */
    private $templateFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magezon\PageBuilder\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        parent::__construct($context);
        $this->templateFactory = $templateFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result['status'] = false;
        $data             = $this->getRequest()->getPostValue();
        if ($data) {
            /** @var \Magezon\PageBuilder\Model\Template $model */
            $model = $this->templateFactory->create();
            try {
                $model->setData($data);
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the template.'));
                $result['status'] = true;
            } catch (LocalizedException $e) {
                $result['message'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['message'] = __('Something went wrong while saving the template.');
            }
        }
        $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($result)
        );

        return $result;
    }
}
