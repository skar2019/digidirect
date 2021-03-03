<?php
namespace Digidirect\Navigation\Block\Adminhtml\Set\Edit;

use Magento\Backend\Block\Widget\Context;
use Digidirect\Navigation\Api\SetRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 * @package Digidirect\Navigation\Block\Adminhtml\Set\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var SetRepositoryInterface
     */
    protected $setRepository;

    /**
     * @param Context $context
     * @param SetRepositoryInterface $setRepository
     */
    public function __construct(
        Context $context,
        SetRepositoryInterface $setRepository
    ) {
        $this->context = $context;
        $this->setRepository = $setRepository;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Return set ID
     *
     * @return int|null
     */
    public function getSetId()
    {
        try {
            return $this->setRepository->getById(
                $this->context->getRequest()->getParam('set_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }
}
