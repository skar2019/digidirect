<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Controller\Role;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Company\Model\CompanyUserPermission;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;
use Magento\Company\Model\CompanyContext;
use Magento\Framework\App\Action\Context;
use Magento\Company\Controller\AbstractAction;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Adds or edits a role
 */
class Edit extends AbstractAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a company session.
     */
    public const COMPANY_RESOURCE = 'Magento_Company::roles_edit';

    /**
     * @var CompanyUserPermission
     */
    private $companyUserPermission;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param CompanyContext $companyContext
     * @param LoggerInterface $logger
     * @param CompanyUserPermission|null $companyUserPermission
     */
    public function __construct(
        Context $context,
        CompanyContext $companyContext,
        LoggerInterface $logger,
        ?CompanyUserPermission $companyUserPermission = null
    ) {
        parent::__construct($context, $companyContext, $logger);
        $this->companyUserPermission = $companyUserPermission ?:
            ObjectManager::getInstance()->get(CompanyUserPermission::class);
    }

    /**
     * Role access checks.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $roleId = (int)$this->getRequest()->getParam('id');
        if ($roleId && !$this->companyUserPermission->companyHasRole($roleId)) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * Roles and permissions edit.
     *
     * @return ResultInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $roleId = (int)$this->getRequest()->getParam('id');
        /** @var ResultInterface $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $result->getConfig()->getTitle()->set(__($roleId ? 'Edit Role' : 'Add New Role'));
        return $result;
    }
}
