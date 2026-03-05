<?php
namespace Digidirect\AI\Model\Lib\Backup;

use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Archive\ArchiveInterface;

/**
 * Class BackupFactory
 * @package Digidirect\AI\Model\Lib\Backup
 */
class BackupFactory
{
    /**
     * @var \Magento\Framework\Api\ObjectFactory
     */
    private $objectFactory;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param string $instanceName
     */
    public function __construct(
        ObjectFactory $objectFactory,
        $instanceName = '\\Digidirect\\AI\\Model\\Lib\\Backup\\Backup'
    ) {
        $this->objectFactory = $objectFactory;
        $this->instanceName = $instanceName;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function create(array $data = [])
    {
        if (empty($data['archiveType']['instance']) && empty($data['archiveType']['instanceObject'])) {
            $data['archiveType']['instance'] = '\Digidirect\AI\Model\Lib\Archive\Tar';
        }

        $archive = $this->resolveArchive($data['archiveType'] ?? []);
        $data['archiveType']['instanceObject'] = $archive;
        $data['archiveType']['instance'] = $archive;

        return $this->objectFactory->create(ltrim($this->instanceName, '\\'), $data);
    }

    /**
     * @param array $archiveType
     * @return \Magento\Framework\Archive\ArchiveInterface
     * @throws \Exception
     */
    private function resolveArchive(array $archiveType): ArchiveInterface
    {
        $archive = $archiveType['instanceObject'] ?? ($archiveType['instance'] ?? null);

        if (is_string($archive)) {
            $archive = $this->objectFactory->create(ltrim($archive, '\\'));
        }

        if (!($archive instanceof ArchiveInterface)) {
            throw new \Exception(
                'Wrong archive type, class is not implement Magento\Framework\Archive\Archive Interface'
            );
        }

        return $archive;
    }
}
