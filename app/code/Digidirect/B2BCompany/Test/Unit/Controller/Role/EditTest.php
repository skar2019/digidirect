<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Test\Unit\Controller\Role;

use Magento\Company\Controller\Role\Edit;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Page\Config;
use Magento\Company\Model\CompanyUserPermission;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    /**
     * @var ViewInterface|MockObject
     */
    private $request;

    /**
     * @var Edit
     */
    private $controller;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactory;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactory;

    /**
     * @var Page|MockObject
     */
    private $resultPage;

    /**
     * @var CompanyUserPermission|MockObject
     */
    private $companyUserPermission;

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getParam'])
            ->getMockForAbstractClass();
        $this->resultRedirectFactory = $this->createPartialMock(
            RedirectFactory::class,
            ['create']
        );

        $this->companyUserPermission = $this->createMock(CompanyUserPermission::class);
        $this->resultFactory = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->resultPage = $this->getMockBuilder(Page::class)
            ->addMethods(['getTitle', 'set', 'getBlock', 'setData'])
            ->onlyMethods(['getConfig', 'getLayout'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPage->expects($this->any())->method('getConfig')->willReturnSelf();
        $this->resultPage->expects($this->any())->method('getTitle')->willReturnSelf();
        $this->resultPage->expects($this->any())->method('set')->willReturnSelf();
        $this->resultPage->expects($this->any())->method('getLayout')->willReturnSelf();
        $this->resultPage->expects($this->any())->method('getBlock')->willReturnSelf();
        $this->resultPage->expects($this->any())->method('setData')->willReturnSelf();

        $objectManager = new ObjectManager($this);
        $this->controller = $objectManager->getObject(
            Edit::class,
            [
                'resultFactory' => $this->resultFactory,
                'resultRedirectFactory' => $this->resultRedirectFactory,
                'companyUserPermission' => $this->companyUserPermission,
                '_request' => $this->request,
            ]
        );
    }

    /**
     * Test execute method.
     *
     * @return void
     */
    public function testExecute()
    {
        $resultConfig = $this->createMock(Config::class);
        $resultTitle  = $this->createMock(Title::class);

        $this->resultFactory->expects($this->any())->method('create')->willReturn($this->resultPage);
        $this->request->expects($this->any())->method('getParam')->with('id')->willReturn(1);
        $this->resultPage->expects($this->once())->method('getConfig')->willReturn($resultConfig);
        $this->resultPage->expects($this->once())->method('getTitle')->willReturn($resultTitle);
        $this->controller->execute();
    }

    /**
     * Test dispatch company role check method.
     *
     * @return void
     */
    public function testDispatchRoleIdNotFound()
    {
        $this->companyUserPermission->expects($this->once())->method('companyHasRole')->willReturn(false);
        $this->request->expects($this->any())->method('getParam')->with('id')->willReturn(1);
        $this->expectException('Magento\Framework\Exception\NotFoundException');
        $this->expectExceptionMessage('Page not found.');
        $this->controller->dispatch($this->request);
    }
}
