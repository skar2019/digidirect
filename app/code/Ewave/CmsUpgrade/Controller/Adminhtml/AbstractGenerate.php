<?php
namespace Ewave\CmsUpgrade\Controller\Adminhtml;

use Ewave\CmsUpgrade\Model\GeneratorInterface;
use Magento\Backend\App\Action\Context;

/**
 * Class AbstractGenerate
 * @package Ewave\CmsUpgrade\Controller\Adminhtml
 */
class AbstractGenerate extends Generate
{
    /**
     * @var string
     */
    protected $entityLabel = '';

    /**
     * @var string
     */
    protected $nameMassForm = '';

    /**
     * @var GeneratorInterface
     */
    protected $entity;

    /**
     * Generate constructor.
     * @param Context $context
     * @param GeneratorInterface $entity
     * @param string $nameMassForm
     * @param string $entityLabel
     */
    public function __construct(
        Context $context,
        GeneratorInterface $entity,
        $nameMassForm,
        $entityLabel
    ) {
        $this->entity = $entity;
        $this->nameMassForm = $nameMassForm;
        $this->entityLabel = $entityLabel;
        parent::__construct($context);
    }

    /**
     * Default action
     *
     * @return $this
     */
    public function execute()
    {
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $ids = $this->getRequest()->getParam($this->nameMassForm);
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select a ' . $this->entityLabel . '(s).'));
        } else {
            $this->entity->setItemsIds($ids);
            try {
                $result = $this->entity->generate();
                $this->setGenerateResult($result);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
