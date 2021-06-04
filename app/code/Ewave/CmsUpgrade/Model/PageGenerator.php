<?php
namespace Ewave\CmsUpgrade\Model;

use Magento\Cms\Api\PageRepositoryInterface as PageRepository;


/**
 * Class PageGenerator
 * @package Ewave\CmsUpgrade\Model
 */
class PageGenerator extends Generator
{

    /** @var PageRepository  */
    protected $pageRepository;

    /**
     * PageGenerator constructor.
     * @param GeneratorContext $context
     * @param PageRepository $pageRepository
     */
    public function __construct(
        GeneratorContext $context,
        PageRepository $pageRepository
    ) {
        $this->pageRepository = $pageRepository;
        parent::__construct($context);
    }

    /**
     * @param array $entityIds
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript(array $entityIds)
    {
        $data = $this->_getUpgradeData();
        foreach ($entityIds as $pageId) {

            $page = $this->pageRepository->getById($pageId);
            $this->_dispatchBeforeFillData($page);
            $data['items'][] = $this->_clearPageData($page);
        }
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }


    /**
     * Exclude pre-configured data from entity
     *
     * @param object $entity
     * @return array
     */
    protected function _clearPageData($entity)
    {
        $result = $entity->getData();
        foreach ($this->_generateEntity->getUpgradeFields() as $field) {
            unset($result[$field]);
        }
        return $result;
    }
}
