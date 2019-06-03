<?php
namespace Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Helper;

use Ewave\AbstractAttributes\Api\AbstractAttributeRepositoryInterface;
use Ewave\LayeredNavigation\Helper\Url;
use Magento\Framework\App\RequestInterface;

class Data
{
    const CATEGORY_FILTER = 'cat';

    /**
     * @var AbstractAttributeRepositoryInterface
     */
    protected $abstractAttributeRepository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param AbstractAttributeRepositoryInterface $abstractAttributeRepository
     * @param RequestInterface $request
     */
    public function __construct(
        AbstractAttributeRepositoryInterface $abstractAttributeRepository,
        RequestInterface $request
    ) {
        $this->request = $request;
        $this->abstractAttributeRepository = $abstractAttributeRepository;
    }

    /**
     * @param \Ewave\LayeredNavigation\Helper\Data $subject
     * @param string $path
     * @return string
     */
    public function beforeParseRequestUri(
        \Ewave\LayeredNavigation\Helper\Data $subject,
        $path
    ) {
        if (null === $path) {
            $path = $this->request->getRequestUri();
        }

        $urlParts = explode('/', $path);
        if (count($urlParts) === 3 && in_array($urlParts[0], $this->getAbstractAttributesCodes())) {
            $categoryPartsWithSuffix = array_filter(explode('.', $urlParts[2]));
            $categoryPart = $categoryPartsWithSuffix[0];
            if (!empty($categoryPart)) {
                $newPath = implode('/', [
                    $urlParts[0],
                    $urlParts[1],
                    Url::FILTERS_DELIMITER,
                    self::CATEGORY_FILTER,
                    $categoryPart,
                ]);

                if (count($categoryPartsWithSuffix) === 2) {
                    $newPath .= '.' . $categoryPartsWithSuffix[1];
                }

                $path = $newPath;
            }
        }

        return $path;
    }

    /**
     * @return array
     */
    protected function getAbstractAttributesCodes()
    {
        $codes = [];
        $attributes = $this->abstractAttributeRepository->getAbstractAttributes(1);
        foreach ($attributes as $attribute) {
            /** @var \Ewave\AbstractAttributes\Model\AbstractAttribute $attribute */
            $codes[] = $attribute->getAttributeCode();
        }
        return $codes;
    }
}
