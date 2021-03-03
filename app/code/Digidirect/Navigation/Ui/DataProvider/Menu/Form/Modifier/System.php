<?php

namespace Digidirect\Navigation\Ui\DataProvider\Menu\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;

class System extends MenuModifier
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * System constructor.
     * @param UrlInterface $urlBuilder
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($registry, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Implemented method
     *
     * @param [] $meta
     * @return []
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Modify buttons urls - add store parameter to save and validate urls
     *
     * @param [] $data
     * @return []
     */
    public function modifyData(array $data)
    {
        $actionParameters = [
            'id' => $this->_getCurrentMenuItem()->getId(),
            'store' => $this->_getCurrentMenuItem()->getCurrentStoreId()
        ];
        $submitUrl = $this->urlBuilder->getUrl('digidirect_navigation/menu/save', $actionParameters);
        $validateUrl = $this->urlBuilder->getUrl('digidirect_navigation/menu/validate', $actionParameters);

        $data = array_replace_recursive(
            $data,
            [
                'config' => [
                    'submit_url' => $submitUrl,
                    'validate_url' => $validateUrl,
                ]
            ]
        );

        return $data;
    }
}
