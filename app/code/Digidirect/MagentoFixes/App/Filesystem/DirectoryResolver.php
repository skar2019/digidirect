<?php
declare(strict_types=1);

/**
 * TODO: temporary override until patch or Magento 2.3 is released.
 * https://github.com/magento/magento2/issues/13929
 */
namespace Digidirect\MagentoFixes\App\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Magento directories resolver.
 */
class DirectoryResolver
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Validate path.
     *
     * Gets real path for directory provided in parameters and compares it with specified root directory.
     * Will return TRUE if real path of provided value contains root directory path and FALSE if not.
     * Throws the \Magento\Framework\Exception\FileSystemException in case when directory path is absent
     * in Directories configuration.
     *
     * @param string $path
     * @param string $directoryConfig
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function validatePath($path, $directoryConfig = DirectoryList::MEDIA)
    {
        $realPath = realpath($path);
        // BEGIN EDIT by @erikhansen
        /**
         * Since media directory is a symlink, need to run both paths through realpath in order for the comparison to
         * work.
         * The proper fix for this should involve a STORE > Configuration setting where an admin can choose whether to
         * allow symlinked directories.
         */
        $root = realpath($this->directoryList->getPath($directoryConfig));
        // END EDIT

        return strpos($realPath, $root) === 0;
    }
}