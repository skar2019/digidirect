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
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Controller\Adminhtml\Ajax;

class ConditionsValue extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        parent::__construct($context);
        $this->jsonSerializer = $jsonSerializer;
    }

    public function execute()
    {
        $result['status'] = false;
        try {
            $post    = $this->getRequest()->getPostValue();
            $options = [];
            parse_str($post['values'], $options);
            $result['value']  = $this->jsonSerializer->serialize($options['parameters']['conditions']);
            $result['status'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['message'] = $e->getMessage();
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Exception $e) {
            $result['message'] = __('Something went wrong while process the request.');
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while processing the request.'));
        }
        $this->getResponse()->setBody($this->jsonSerializer->serialize($result));
    }
}
