<?php

namespace Ewave\Migration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Ewave\Migration\Model\MigrationProcessor;

/**
 * Class MigrateCommand
 * @package Ewave\Migration\Console\Command
 */
class MigrateCommand extends Command
{
    /**
     * file argyment
     */
    const FILE_ARGUMENT = 'file';
    const FILE_OFFSET = 'offset';
    const FILE_LIMIT = 'limit';

    /** @var \Magento\Framework\Filesystem\Driver\File */
    protected $_filesystemDriver;

    /** @var null|\Psr\Log\LoggerInterface */
    protected $_logger = null;

    /**
     * @var null|\Magento\Framework\App\State
     */
    protected $_appState = null;


    /**
     * @var MigrationProcessor
     */
    protected $processor;

    /**
     * MigrateCommand constructor.
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $appState
     * @param MigrationProcessor $processor
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $appState,
        MigrationProcessor $processor
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
        $this->setName('ewave:data_migrate')
            ->setDescription('Migrate Kayweb Data.')->setDefinition([
                new InputArgument(
                    self::FILE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'JSON file for migration'
                ),
                new InputArgument(
                    self::FILE_OFFSET,
                    InputArgument::OPTIONAL,
                    'Offset of list for migration'
                ),
                new InputArgument(
                    self::FILE_LIMIT,
                    InputArgument::OPTIONAL,
                    'Limit of list for migration'
                ),
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
                $output,
                $input->getArgument(self::FILE_OFFSET),
                $input->getArgument(self::FILE_LIMIT)
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage() . ': ' . 'Step skipped');
        }
    }

    /**
     * @param $file
     * @param $output
     * @param $offset
     * @param $limit
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _migrateAll($file, $output, $offset, $limit)
    {
        $output->writeln('The process started for file ' . $file . ' with offset ' . $offset . ' and limit ' . $limit);

        $output->writeln('Reading the file');

        if ($this->_filesystemDriver->isExists($file)) {
            $output->writeln('Getting content');
            $data = $this->_filesystemDriver->fileGetContents($file);
            $data = json_decode(preg_replace('/[[:cntrl:]]/', '', $data), true);

            $this->processor->process($data, $output, $offset, $limit);

            $output->writeln('');
            $output->writeln('Parsed records:' . count($data['RECORDS']));
        } else {
            $output->writeln('File doesn\'t exist.');
        }

        $output->writeln('The process finished');
    }
}
