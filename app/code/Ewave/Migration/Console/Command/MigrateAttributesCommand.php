<?php

namespace Ewave\Migration\Console\Command;

use Magento\Setup\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Ewave\Migration\Model\AttributesProcessor;

/**
 * Class MigrateAttributesCommand
 * @package Ewave\Migration\Console\Command
 */
class MigrateAttributesCommand extends Command
{
    /**
     * file argyment
     */
    const FILE_ARGUMENT = 'file';

    /** @var \Magento\Framework\Filesystem\Driver\File */
    protected $_filesystemDriver;

    /** @var null|\Psr\Log\LoggerInterface */
    protected $_logger = null;

    /**
     * @var null|\Magento\Framework\App\State
     */
    protected $_appState = null;

    /**
     * @var AttributesProcessor
     */
    protected $processor;

    /**
     * MigrateCommand constructor.
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $appState
     * @param AttributesProcessor $processor
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $appState,
        AttributesProcessor $processor
    ) {
        $this->_filesystemDriver = $filesystemDriver;
        $this->_logger = $logger;
        $this->_appState = $appState;
        $this->processor = $processor;
        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('ewave:migrate_attributes')
            ->setDescription('Migrate Custom Attributes.')->setDefinition([
                new InputArgument(
                    self::FILE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'CSV file for migration'
                )
            ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'executeMigrateCommand'],
            [$input, $output]
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function executeMigrateCommand(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->_migrateAll(
                $input->getArgument(self::FILE_ARGUMENT),
                $output
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage() . ': ' . 'Step skipped');
        }
    }

    /**
     * @param $file
     * @param $output
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _migrateAll($file, $output)
    {
        $output->writeln('The process started for file ' . $file);

        $output->writeln('Reading the file');

        if ($this->_filesystemDriver->isExists($file)) {
            $output->writeln('Start attributes creation');

            $data = $this->prepareCSVFileData($file);
            $this->processor->process($data, $output);

            $output->writeln('');
            $output->writeln('Parsed records:' . count($data));
        } else {
            $output->writeln('File doesn\'t exist.');
        }

        $output->writeln('The process finished');
    }

    /**
     * @param string $file
     * @return array
     */
    private function prepareCSVFileData(string $file):array
    {
        $data = [];


        try {
            $resource = $this->_filesystemDriver->fileOpen($file, 'r');
            $columns = $this->_filesystemDriver->fileGetCsv($resource);

            while ($csvRow = $this->_filesystemDriver->fileGetCsv($resource)) {
                $data[] = array_combine($columns, $csvRow);
            }

        } catch (\Exception $e) {
            // nothing
        }

        return $data;
    }
}
