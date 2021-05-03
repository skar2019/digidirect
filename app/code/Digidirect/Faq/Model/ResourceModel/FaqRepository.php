<?php
namespace Digidirect\Faq\Model\ResourceModel;

/**
 * Class FaqRepository
 *
 * @package Digidirect\Faq\Model\ResourceModel
 */
class FaqRepository implements \Digidirect\Faq\Api\FaqRepositoryInterface
{
    /**
     * @var \Digidirect\Faq\Model\FaqFactory
     */
    protected $faqFactory;

    /**
     * @var \Digidirect\Faq\Model\ResourceModel\Faq
     */
    protected $faqResource;

    /**
     * FaqRepository constructor.
     * @param \Digidirect\Faq\Model\FaqFactory $faqFactory
     * @param Faq $faqResource
     */
    public function __construct(
        \Digidirect\Faq\Model\FaqFactory $faqFactory,
        \Digidirect\Faq\Model\ResourceModel\Faq $faqResource
    ) {
        $this->faqFactory = $faqFactory;
        $this->faqResource = $faqResource;
    }

    /**
     * Get menu Item by ID
     *
     * @param int $id
     * @return \Digidirect\Faq\Model\Faq
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
    public function save(\Digidirect\Faq\Model\Faq $item)
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
