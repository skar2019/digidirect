<?php
namespace Ewave\Faq\Model\ResourceModel;

/**
 * Class FaqRepository
 *
 * @package Ewave\Faq\Model\ResourceModel
 */
class FaqRepository implements \Ewave\Faq\Api\FaqRepositoryInterface
{
    /**
     * @var \Ewave\Faq\Model\FaqFactory
     */
    protected $faqFactory;

    /**
     * @var \Ewave\Faq\Model\ResourceModel\Faq
     */
    protected $faqResource;

    /**
     * FaqRepository constructor.
     * @param \Ewave\Faq\Model\FaqFactory $faqFactory
     * @param Faq $faqResource
     */
    public function __construct(
        \Ewave\Faq\Model\FaqFactory $faqFactory,
        \Ewave\Faq\Model\ResourceModel\Faq $faqResource
    ) {
        $this->faqFactory = $faqFactory;
        $this->faqResource = $faqResource;
    }

    /**
     * Get menu Item by ID
     *
     * @param int $id
     * @return \Ewave\Faq\Model\Faq
     */
    public function getById($id)
    {
        $item = $this->faqFactory->create();
        $this->faqResource->load($item, $id);
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Ewave\Faq\Model\Faq $item)
    {
        $this->faqResource->save($item);
        return $item;
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function deleteById($id)
    {
        return $this->faqResource->delete($this->getById($id));
    }

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        return $this->faqResource->updateStatus($ids, $status);
    }
}
