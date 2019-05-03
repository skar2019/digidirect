<?php

declare(strict_types=1);

namespace Ewave\Migration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Ewave\Migration\Model\KaywebOrderMigrationProcessor;
use Magento\Framework\Registry;

/**
 * Class MigrateKaywebOrdersCommand
 * @package Ewave\Migration\Console\Command
 */
class MigrateKaywebOrdersCommand extends Command
{
    const FILENAME_ARGUMENT = 'filename';

    const DEBUG_OPTION = 'debug';

    /**
     * @var File
     */
    private $filesystemDriver;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var KaywebCustomersMigrationProcessor
     */
    private $processor;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * MigrateKaywebOrdersCommand constructor.
     * @param File $filesystemDriver
     * @param State $appState
     * @param Registry $registry
     * @param KaywebOrderMigrationProcessor $kaywebOrderMigrationProcessor
     */
    public function __construct(
        File $filesystemDriver,
        State $appState,
        Registry $registry,
        KaywebOrderMigrationProcessor $kaywebOrderMigrationProcessor
    ) {
        $this->filesystemDriver = $filesystemDriver;
        $this->appState = $appState;
        $this->processor = $kaywebOrderMigrationProcessor;
        $this->registry = $registry;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $arguments = [
            new InputArgument(
                self::FILENAME_ARGUMENT,
                InputArgument::REQUIRED,
                'The path to your JSON file'
            ),
            new InputOption(
                self::DEBUG_OPTION,
                '-d',
                InputOption::VALUE_NONE,
                'Debug mode'
            ),
        ];

        $this->setName('ewave:migrate:kayweb-orders')
            ->setDescription('Migrate Kayweb Orders')
            ->setDefinition($arguments);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registry->register('isSecureArea', true);
        $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'executeMigrateCommand'],
            [$input, $output]
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function executeMigrateCommand(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->_migrateAll(
                $output,
                $input->getArgument(self::FILENAME_ARGUMENT),
                $input->getOption(self::DEBUG_OPTION)
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $filePath
     * @param bool $debugOption
     */
    protected function _migrateAll(
        OutputInterface $output,
        string $filePath,
        $debugOption = false
    ): void {
        $output->writeln(sprintf(
            'The process has started: file %s',
            $filePath
        ));

        if ($this->filesystemDriver->isExists($filePath) && pathinfo($filePath)['extension'] == 'csv') {
            $output->writeln('Getting content');
            $result = $this->processor->process($output, $filePath, $debugOption);
            $output->writeln('');
            $output->writeln(
                sprintf(
                    'Handled orders email %d / imported orders email %d',
                    $result['all'],
                    $result['executed']
                )
            );
        } else {
            $output->writeln('The file does not exist. Or format not .csv');
        }

        $output->writeln('The process has been finished');
    }
}
