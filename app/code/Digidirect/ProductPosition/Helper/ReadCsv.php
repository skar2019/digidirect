<?php
 
namespace Digidirect\ProductPosition\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
 
class ReadCsv extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var Csv
     */
    protected $csv;
    /**
     * @var File
     */
    protected $file;
    
    
    protected $_logger;
    
 
    public function __construct(
        DirectoryList $directoryList,
        Csv $csv,
        File $file,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->file = $file;
        $this->_logger = $logger;
    }
 
    public function readCsv($csvFilePath)
    {
        $rootDirectory = $this->directoryList->getPath('etc');
        $csvFile = $rootDirectory . "/" . $csvFilePath;
        $this->_logger->info($csvFile);
        try {
            if ($this->file->isExists($csvFile)) {
                //set delimiter, for tab pass "\t"
                $this->csv->setDelimiter(",");
                //get data as an array
                $data = $this->csv->getData($csvFile);
                if (!empty($data)) {
                    // ignore first header column and read data
                    foreach ($data as $key => $value) {
                        $columnFirst = trim($value['0']);
                        $columnSecond = trim($value['1']);
                        //and so on.
                        $this->_logger->info("Position: " . $columnFirst);
                        $this->_logger->info("Value: " . $columnSecond);
                    }
                }
            } else {
                $this->_logger->info('Csv file not exist');
                return __('Csv file not exist');
            }
        } catch (FileSystemException $e) {
            $this->_logger->info($e->getMessage());
        }
    }
}