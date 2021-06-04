<?php

namespace Ewave\CmsUpgrade\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CmsUpgradeCommand
 *
 * @package Ewave\CmsUpgrade\Console\Command
 */
class CmsUpgradeCommand extends Command
{
    /**
     * Version argument
     */
    const VERSION_ARGUMENT = 'version';

    /** @var \Ewave\CmsUpgrade\Helper\Data */
    protected $_helper;

    /** @var \Magento\Framework\Filesystem\Driver\File */
    protected $_filesystemDriver;

    /** @var \Ewave\CmsUpgrade\Model\SetupProcessorFactory */
    protected $_setupProcessorFactory;

    /** @var null|\Ewave\CmsUpgrade\Model\DataVersion */
    protected $_dataVersion = null;

    /** @var null|\Psr\Log\LoggerInterface */
    protected $_logger = null;

    /**
     * @var null|\Magento\Framework\App\State
     */
    protected $_appState = null;

    /**
     * UpgradeData constructor.
     *
     * @param \Ewave\CmsUpgrade\Model\SetupProcessorFactory $setupProcessorFactory
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Ewave\CmsUpgrade\Helper\Data $helper
     * @param \Ewave\CmsUpgrade\Model\DataVersion $dataVersion
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Ewave\CmsUpgrade\Model\SetupProcessorFactory $setupProcessorFactory,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Ewave\CmsUpgrade\Helper\Data $helper,
        \Ewave\CmsUpgrade\Model\DataVersion $dataVersion,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $appState
    ) {
        $this->_setupProcessorFactory = $setupProcessorFactory;
        $this->_filesystemDriver = $filesystemDriver;
        $this->_helper = $helper;
        $this->_dataVersion = $dataVersion;
        $this->_logger = $logger;
        $this->_appState = $appState;
        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('ewave:cms_upgrade')
            ->setDescription('Upgrade Cms Data.')->setDefinition([
                new InputArgument(
                    self::VERSION_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Version of upgrade script for update'
                ),
            ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'executeCmsUpgradeCommand'],
            [$input, $output]
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function executeCmsUpgradeCommand(InputInterface $input, OutputInterface $output)
    {
        try {
            if ($input->getArgument('version')) {
                $this->_upgradeVersion($input->getArgument('version'), $output);
            } else {
                $this->_upgradeAll($output);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage() . ': ' . 'Step skipped');
        }
    }

    /**
     * @param string $version
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _upgradeVersion($version, $output)
    {
        if ($this->_filesystemDriver->isExists($this->_helper->getPathToUpgradeScript($version))) {
            if ($this->_applyUpgradeScript($this->_helper->getPathToUpgradeScript($version))) {
                $output->writeln('Upgrade script with version ' . $version . ' has been successfully applied');
            } else {
                $output->writeln('Cannot apply upgrade script');
            }
        } else {
            $output->writeln('Upgrade script file not found: ' . $this->_helper->getPathToUpgradeScript($version));
        }
    }

    /**
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _upgradeAll($output)
    {
        if ($this->_filesystemDriver->isExists($this->_helper->getModuleSetupDataDir())) {
            $dir = $this->_filesystemDriver->readDirectory($this->_helper->getModuleSetupDataDir());
            try {
                $lastUpdatedVersion = null;
                foreach ($dir as $file) {
                    $fileName = basename($file);
                    $fileNameArr = explode('-', $file);
                    $fileVersion = end($fileNameArr);
                    $isFileInstalled = $this->_dataVersion->isFileInstalled($fileName);

                    if (version_compare($this->_dataVersion->getVersion(), $fileVersion) < 0) {
                        if ($this->_applyUpgradeScript($file)) {
                            $output->writeln('Upgraded to version ' . $fileVersion);
                            $lastUpdatedVersion = $fileVersion;
                        }
                        if (!$isFileInstalled) {
                            $this->_dataVersion->installFile($fileName);
                        }
                    } else {
                        if (!$isFileInstalled) {
                            $this->_applyUpgradeScript($file, true);
                            $this->_dataVersion->installFile($fileName);
                            $output->writeln('Installed old version ' . $fileVersion);
                        }
                    }
                }
                if ($lastUpdatedVersion !== null) {
                    $this->_dataVersion->updateVersion($lastUpdatedVersion);
                    $output->writeln('DB data version is updated to version: ' . $lastUpdatedVersion);
                } else {
                    $output->writeln('No data for update');
                }
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $output->writeln('Some error is occurred: ' . $e->getMessage());
            }
        } else {
            $output->writeln('Upgrade files not found');
        }
    }

    /**
     * @param string $file
     * @param bool $checkUpdateDate
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _applyUpgradeScript($file, $checkUpdateDate = false)
    {
        $data = $this->_filesystemDriver->fileGetContents($file);
        $data = json_decode($data, true);
        $processor = $this->_setupProcessorFactory->makeProcessor($data['entityType']);
        if ($processor) {
            $processor->setUpgradeData($data['items']);
            $processor->upgrade($checkUpdateDate);
            return true;
        }

        return false;
    }
}
