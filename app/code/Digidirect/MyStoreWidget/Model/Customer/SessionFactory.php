<?php
namespace Digidirect\MyStoreWidget\Model\Customer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;

class SessionFactory extends \Magento\Customer\Model\SessionFactory
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * SessionFactory constructor.
     * 
     * @param ObjectManagerInterface $objectManager
     * @param RequestInterface $request
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        RequestInterface $request,
        $instanceName = CustomerSession::class
    ) {
        parent::__construct($objectManager, $instanceName);
        $this->request = $request;
    }

    /**
     * @param array $data
     * @return CustomerSession
     */
    public function create(array $data = [])
    {
        if ($this->request->getModuleName() === 'page_cache') {
            return parent::create($data);
        }

        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
