<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Company\Api\RoleManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Company permissions based on user access.
 */
class CompanyUserPermission
{
    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $customerContext;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Company\Api\StatusServiceInterface
     */
    private $moduleConfig;

    /**
     * @var RoleManagementInterface
     */
    private $roleManagement;

    /**
     * CompanyAdminPermission constructor.
     *
     * @param \Magento\Authorization\Model\UserContextInterface $customerContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Company\Api\StatusServiceInterface $moduleConfig
     * @param RoleManagementInterface|null $roleManagement
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $customerContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Company\Api\StatusServiceInterface $moduleConfig,
        ?RoleManagementInterface $roleManagement = null
    ) {
        $this->customerContext = $customerContext;
        $this->customerRepository = $customerRepository;
        $this->moduleConfig = $moduleConfig;
        $this->roleManagement = $roleManagement ?: ObjectManager::getInstance()->get(RoleManagementInterface::class);
    }

    /**
     * Check is current user company user.
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function isCurrentUserCompanyUser()
    {
        $customer = $this->customerRepository->getById($this->customerContext->getUserId());
        return $this->isUserCompanyUser($customer);
    }

    /**
     * Check is user a company user.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    private function isUserCompanyUser(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return $this->moduleConfig->isActive() &&
            $customer->getExtensionAttributes() !== null &&
            $customer->getExtensionAttributes()->getCompanyAttributes() !== null &&
            $customer->getExtensionAttributes()->getCompanyAttributes()->getCompanyId();
    }

    /**
     * Check if role exists for company.
     *
     * @param int $roleId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function companyHasRole(int $roleId): bool
    {
        if ($this->isCurrentUserCompanyUser()) {
            $customer = $this->customerRepository->getById($this->customerContext->getUserId());
            $companyId = $customer->getExtensionAttributes()->getCompanyAttributes()->getCompanyId();
            $roles = $this->roleManagement->getRolesByCompanyId($companyId);
            foreach ($roles as $role) {
                if ($roleId === (int)$role->getId()) {
                    return true;
                }
            }
        }
        return false;
    }
}
