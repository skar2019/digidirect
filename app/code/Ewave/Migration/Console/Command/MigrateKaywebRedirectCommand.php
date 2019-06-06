<?php

declare(strict_types=1);

namespace Ewave\Migration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Registry;
use Magento\Framework\File\Csv as CsvReader;
use Ewave\Migration\Model\KaywebRedirectMigrationProcessor;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class MigrateKaywebRedirectCommand
 * @package Ewave\Migration\Console\Command
 */
class MigrateKaywebRedirectCommand extends Command
{
    const CRAWL_FILE_PATH = 'path_crawl_file';
    const PRODUCTS_FILE_PATH = 'path_products_file';
    const PATH_TO_RESULT = 'path_to_result';

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
     * @var Registry
     */
    private $registry;
    /**
     * @var CsvReader
     */
    private $csvReader;
    /**
     * @var KaywebRedirectMigrationProcessor
     */
    private $migrationProcessor;

    /**
     * MigrateKaywebRedirectCommand constructor.
     * @param File $filesystemDriver
     * @param State $appState
     * @param Registry $registry
     * @param CsvReader $csvReader
     * @param KaywebRedirectMigrationProcessor $migrationProcessor
     */
    public function __construct(
        File $filesystemDriver,
        State $appState,
        Registry $registry,
        CsvReader $csvReader,
        KaywebRedirectMigrationProcessor $migrationProcessor
    ) {
        $this->filesystemDriver = $filesystemDriver;
        $this->appState = $appState;
        $this->registry = $registry;
        parent::__construct();
        $this->csvReader = $csvReader;
        $this->migrationProcessor = $migrationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $arguments = [
            new InputArgument(
                self::CRAWL_FILE_PATH,
                InputArgument::REQUIRED,
                'The path to your Crawl csv file'
            ),
            new InputArgument(
                self::PRODUCTS_FILE_PATH,
                InputArgument::REQUIRED,
                'The path to your Product data csv file'
            ),
            new InputArgument(
                self::PATH_TO_RESULT,
                InputArgument::OPTIONAL,
                'The path to save result files'
            ),
            new InputOption(
                self::DEBUG_OPTION,
                '-d',
                InputOption::VALUE_NONE,
                'Debug mode'
            ),
        ];

        $this->setName('ewave:migrate:kayweb-redirect')
            ->setDescription('Migrate Kayweb Redirect')
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
                $input->getArgument(self::CRAWL_FILE_PATH),
                $input->getArgument(self::PRODUCTS_FILE_PATH),
                $input->getArgument(self::PATH_TO_RESULT) ?? '../',
                $input->getOption(self::DEBUG_OPTION)
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $crawlFilePath
     * @param string $productsFilePath
     * @param string $pathToResult
     * @param bool $debugOption
     */
    protected function _migrateAll(
        OutputInterface $output,
        string $crawlFilePath,
        string $productsFilePath,
        string $pathToResult,
        $debugOption = false
    ): void {
        $output->writeln(sprintf(
            'The process has started: file %s',
            $crawlFilePath
        ));

        if ($this->filesystemDriver->isExists($crawlFilePath)
            && $this->filesystemDriver->isExists($productsFilePath)
            && pathinfo($crawlFilePath)['extension'] == 'csv'
            && pathinfo($productsFilePath)['extension'] == 'csv') {
            $output->writeln('Getting content');
            $crawlContent = $this->prepareCrawlContent($this->csvReader->getData($crawlFilePath));
            $productsContent = $this->prepareProductsContent($this->csvReader->getData($productsFilePath));
            $productIterator = new \ArrayIterator(array_intersect_key($productsContent, $crawlContent));

            $result = $this->migrationProcessor
                ->process($output, $productIterator, $debugOption);

            $result = $this->prepareUrls($result, $crawlContent);

            $output->writeln('The data was received, an attempt to write data to a file ');
            $savedFilesName = $this->saveResult($result, $pathToResult);
            $output->writeln('The data is recorded successfully '. $savedFilesName);
        } else {
            $output->writeln('Files does not exist. Or format not .csv');
        }

        $output->writeln('The process has been finished');
    }

    /**
     * @param array $urls
     * @param array $crawled
     * @return array
     */
    protected function prepareUrls(array $urls, array $crawled): array
    {
        foreach ($urls['all'] as $key => $url) {
            $urls['all'][$key] = $crawled[$url];
        }

        return $urls;
    }

    /**
     * @param array $result
     * @param string $pathToSave
     * @return string
     * @throws FileSystemException
     */
    private function saveResult(array $result, string $pathToSave)
    {
        $savedFiles = [];
        $savedFiles[] = $this->writeDataInFile(
            ['KeyWeb URL','Magento Url'],
            $result['all'],
            $result['exist'],
            'exist',
            $pathToSave
        );
        $savedFiles[] = $this->writeDataInFile(
            ['Pronto Code','KeyWeb Url'],
            $result['all'],
            $result['notExecuted'],
            'not_executed',
            $pathToSave
        );

        return implode(';/n', $savedFiles);
    }

    /**
     * @param array $nameRow
     * @param array $allResult
     * @param array $recordResult
     * @param string $prefixFileName
     * @param string $pathToSave
     * @return string
     * @throws FileSystemException
     */
    private function writeDataInFile(
        array $nameRow,
        array $allResult,
        array $recordResult,
        string $prefixFileName,
        string $pathToSave
    ) {

        try {
            $fileName = (string)time() . '.csv';
            $path = sprintf('%s%s%s', $pathToSave, $prefixFileName, $fileName);
            $data = array_reduce(array_keys($recordResult), function ($acc, $code) use ($recordResult, $allResult) {
                $acc[] = [$allResult[$code], $recordResult[$code]];
                return $acc;
            }, [$nameRow]);

            $this->csvReader
                ->setEnclosure('"')
                ->setDelimiter(',')
                ->appendData($path, $data);
            return $path;
        } catch (FileSystemException $e) {
            throw new FileSystemException(
                __(
                    'An error occurred during save csv file "%1"',
                    [$e->getMessage()]
                )
            );
        }
    }
    /**
     * @param array $content
     * @return mixed
     */
    private function prepareProductsContent(array $content)
    {
        $nameArray = array_shift($content);
        $nameArray = array_map(function ($name) {
            return \preg_replace('/[^A-Za-z_]/', '', $name);
        }, $nameArray);
        return array_reduce($content, function ($acc, $product) use ($nameArray) {
            $product = array_combine($nameArray, $product);
            $acc[$product['url']] = $product['pronto_code'];

            return $acc;
        }, []);
    }

    /**
     * @param array $content
     * @return array
     */
    private function prepareCrawlContent(array $content)
    {
        unset($content[0]);
        $result = [];
        $nameArray = array_shift($content);
        if ($content && !empty($content)) {
            $result = array_reduce($content, function ($acc, $element) use ($nameArray) {
                $urlArray = array_combine($nameArray, $element);
                $urlParsed = explode('/', $urlArray['Address']);
                $url = array_pop($urlParsed);
                $acc[$url] = $urlArray['Address'];
                return $acc;
            }, []);
        }
        return $result;
    }
}
