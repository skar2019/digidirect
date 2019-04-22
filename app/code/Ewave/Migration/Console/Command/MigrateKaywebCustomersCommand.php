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
use Ewave\Migration\Model\KaywebCustomersMigrationProcessor;
use Magento\Framework\Registry;

/**
 * Class MigrateKaywebCustomersCommand
 *
 * @author Michael Marchanka <michail.marchenko@ewave.com>
 */
class MigrateKaywebCustomersCommand extends Command
{
    const FILENAME_ARGUMENT = 'filename';
    const OFFSET_ARGUMENT = 'offset';
    const LIMIT_ARGUMENT = 'limit';

    const DEBUG_OPTION = 'debug';
    const CLEAR_OPTION = 'clear';

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
     * @param File $filesystemDriver
     * @param State $appState
     * @param KaywebCustomersMigrationProcessor $processor
     * @param Registry $registry
     */
    public function __construct(
        File $filesystemDriver,
        State $appState,
        KaywebCustomersMigrationProcessor $processor,
        Registry $registry
    ) {
        $this->filesystemDriver = $filesystemDriver;
        $this->appState = $appState;
        $this->processor = $processor;
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
            new InputArgument(
                self::OFFSET_ARGUMENT,
                InputArgument::OPTIONAL,
                'Offset'
            ),
            new InputArgument(
                self::LIMIT_ARGUMENT,
                InputArgument::OPTIONAL,
                'Limit'
            ),
            new InputOption(
                self::CLEAR_OPTION,
                '-c',
                InputOption::VALUE_NONE,
                'Clear Magento customers before migration'
            ),
            new InputOption(
                self::DEBUG_OPTION,
                '-d',
                InputOption::VALUE_NONE,
                'Debug mode'
            ),
        ];

        $this->setName('ewave:migrate:kayweb-customers')
            ->setDescription('Migrate Kayweb Customers')
            ->setDefinition($arguments);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output

     * @throws \Exception
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
                $input->getArgument(self::OFFSET_ARGUMENT),
                $input->getArgument(self::LIMIT_ARGUMENT),
                $input->getOption(self::DEBUG_OPTION),
                $input->getOption(self::CLEAR_OPTION)
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * @param string $file
     * @param OutputInterface $output
     * @param string|null $offset
     * @param string|null $limit
     * @param bool $debugOption
     * @param bool $clearOption
     *
     * @throws FileSystemException
     */
    protected function _migrateAll(
        OutputInterface $output,
        string $file,
        ?string $offset,
        ?string $limit,
        $debugOption = false,
        $clearOption = false
    ): void
    {
        $output->writeln(sprintf(
            'The process has started: file %s%s%s',
            $file,
            $offset ? " with offset {$offset}" : '',
            $limit ? " and limit {$limit}" : ''
        ));

        if ($this->filesystemDriver->isExists($file)) {
            $output->writeln('Getting content');
            $data = $this->filesystemDriver->fileGetContents($file);
            $data = json_decode(preg_replace('/[[:cntrl:]]/', '', $data), true);
            $this->processor->process($output, $data, (int) $offset, (int) $limit, $debugOption, $clearOption);
            $output->writeln('');
        } else {
            $output->writeln('The file does not exist.');
        }

        $output->writeln('The process has been finished');
    }
}
