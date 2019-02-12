<?php

namespace Ewave\Feed\Helper\CategoryMapping\Multiplicity;

use \Magento\Framework\Module\Dir\Reader as DirReader;
use \Ewave\Feed\Helper\CategoryMapping\FileReaderFactory;

class FileReaderMultiplicity extends ReaderMultiplicity
{
    /**
     * @var DirReader
     */
    protected $dirReader;

    /**
     * @var FileReaderFactory
     */
    protected $fileReaderFactory;

    /**
     * FileReaderMultiplicity constructor.
     * @param DirReader $dirReader
     * @param FileReaderFactory $fileReaderFactory
     */
    public function __construct(
        DirReader $dirReader,
        FileReaderFactory $fileReaderFactory
    ) {
        $this->dirReader = $dirReader;
        $this->fileReaderFactory = $fileReaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $mappingPath = $this->getMappingPath();

        foreach (glob($mappingPath . "/*.txt") as $filename) {
            /** @var \Ewave\Feed\Helper\CategoryMapping\FileInterface $fileReader */
            $fileReader = $this->getReader();
            $this->addItem($fileReader->setFile($filename));
        }

        return $this;
    }

    /**
     * @return \Ewave\Feed\Helper\CategoryMapping\FileInterface
     */
    protected function getReader()
    {
        return $this->fileReaderFactory->create();
    }

    /**
     * @return string
     */
    protected function getMappingPath()
    {
        return realpath($this->dirReader->getModuleDir('etc', 'Ewave_Feed') . '/../Setup/data/mapping/');
    }
}
