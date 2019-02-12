<?php
namespace Ewave\AbstractAttributesNavigation\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Ewave\AbstractAttributes\Api\AbstractAttributeRepositoryInterface;
use Ewave\AbstractAttributes\Api\OptionRepositoryInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Config
 * @package Ewave\AbstractAttributesNavigation\Block\Adminhtml
 */
class Config extends Template
{
    /**
     * @var OptionRepositoryInterface
     */
    protected $optionsRepository;

    /**
     * @var AbstractAttributeRepositoryInterface
     */
    protected $abstractAttributesRepository;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Config constructor.
     * @param AbstractAttributeRepositoryInterface $abstractAttributeRepository
     * @param OptionRepositoryInterface $optionRepository
     * @param Template\Context $context
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        AbstractAttributeRepositoryInterface $abstractAttributeRepository,
        OptionRepositoryInterface $optionRepository,
        Template\Context $context,
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        $this->optionsRepository = $optionRepository;
        $this->abstractAttributesRepository = $abstractAttributeRepository;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $attributes = $this->abstractAttributesRepository->getAbstractAttributes();

        $config = [];
        foreach ($attributes as $attribute) {

            $options = $this->optionsRepository->getAttributeOptions($attribute->getAttributeId());
            $optionsIds = [];
            foreach ($options as $option) {
                $optionsIds[] = $option->getOptionId();
            }

            $config[$attribute->getAttributeId()] = $optionsIds;
        }

        return $this->jsonHelper->jsonEncode($config);
    }
}
