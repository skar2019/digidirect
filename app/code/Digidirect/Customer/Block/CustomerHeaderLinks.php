<?php
namespace Digidirect\Customer\Block;

use Magento\Framework\View\Element\Template;
use Digidirect\Customer\ViewModel\Customer;

class CustomerHeaderLinks extends Template
{
    /**
     * @var Customer
     */
    protected $viewModel;

    /**
     * @param Template\Context $context
     * @param Customer $viewModel
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Customer  $viewModel,
        array            $data = []
    )
    {
        $this->viewModel = $viewModel;
        parent::__construct($context, $data);
    }

    /**
     * @return Customer
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}
