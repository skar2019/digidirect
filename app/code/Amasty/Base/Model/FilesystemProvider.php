<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\FilesystemFactory;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\Directory\DenyListPathValidator;
use Magento\Framework\Filesystem\Directory\WriteFactoryFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

/**
 * Class to provide either default Filesystem class or with configured DenyListPathValidator exception paths
 */
class FilesystemProvider
{
    /**
     * @var FilesystemFactory
     */
    private $filesystemFactory;

    /**
     * @var WriteFactoryFactory
     */
    private $writeFactoryFactory;

    /**
     * @var DriverPool
     */
    private $driverPool;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $exceptionPaths;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /** FilesystemProvider constructor.
     *
     * @param FilesystemFactory $filesystemFactory
     * @param WriteFactoryFactory $writeFactoryFactory
     * @param DriverPool $driverPool
     * @param DirectoryList $directoryList
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param LoggerInterface $logger
     * @param array $exceptionPaths
     */
    public function __construct(
        FilesystemFactory $filesystemFactory,
        WriteFactoryFactory $writeFactoryFactory,
        DriverPool $driverPool,
        DirectoryList $directoryList,
        ComponentRegistrarInterface $componentRegistrar,
        LoggerInterface $logger,
        array $exceptionPaths = []
    ) {
        $this->filesystemFactory = $filesystemFactory;
        $this->writeFactoryFactory = $writeFactoryFactory;
        $this->driverPool = $driverPool;
        $this->directoryList = $directoryList;
        $this->componentRegistrar = $componentRegistrar;
        $this->logger = $logger;
        $this->exceptionPaths = $exceptionPaths;
    }

    public function get(): Filesystem
    {
        if ($this->filesystem === null) {
            try {
                if (!empty($this->exceptionPaths) && class_exists(DenyListPathValidator::class)) {
                    $this->filesystem = $this->createConfiguredFilesystem();
                } else {
                    $this->filesystem = $this->filesystemFactory->create();
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->filesystem = $this->filesystemFactory->create();
            }
        }

        return $this->filesystem;
    }

    /**
     * @return Filesystem
     */
    private function createConfiguredFilesystem(): Filesystem
    {
        $denyListPathValidator = new DenyListPathValidator($this->driverPool->getDriver(DriverPool::FILE));
        $rootDirectory = $this->directoryList->getRoot();

        foreach ($this->exceptionPaths as $module => $pathsList) {
            $componentPath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $module);

            foreach ($pathsList as $path) {
                $denyListPathValidator->addException($rootDirectory . DIRECTORY_SEPARATOR . $path);
                $denyListPathValidator->addException($componentPath . DIRECTORY_SEPARATOR . $path);
                $denyListPathValidator->addException(
                    str_replace(
                        $rootDirectory . DIRECTORY_SEPARATOR,
                        '',
                        $componentPath . DIRECTORY_SEPARATOR . $path
                    )
                );
            }
        }
        $writeFactory = $this->writeFactoryFactory->create(['denyListPathValidator' => $denyListPathValidator]);

        return $this->filesystemFactory->create(['writeFactory' => $writeFactory]);
    }
}
