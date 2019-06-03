<?php
namespace Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Helper;

use Magento\Framework\App\RequestInterface;

class FilterRemember
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * FilesystemReader constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Ewave\LayeredNavigation\Helper\FilterRemember $subject
     * @param \Magento\Catalog\Model\Category|null $result
     * @return \Magento\Catalog\Model\Category|null
     */
    public function afterGetCategory(
        \Ewave\LayeredNavigation\Helper\FilterRemember $subject,
        $result
    ) {
        if ($this->request->getModuleName() == 'eaa') {
            return null;
        }
        return $result;
    }
}
