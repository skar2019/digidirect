<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Plugin\Config\Block\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field as NativeField;
use Magento\Framework\View\Asset\Repository;

/**
 * Class Field
 *
 * @package MageSpark\Base\Plugin\Config\Block\System\Config\Form
 */
class Field
{
    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * Field constructor.
     *
     * @param Repository $assetRepo
     */
    public function __construct(
        Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    /**
     * @param NativeField $field
     * @param string $html
     *
     * @return string
     */
    public function afterRender(
        NativeField $field,
        $html
    ) {
        if (strpos($html, 'tooltip-content') !== false) {
            preg_match('/<img.*?src="(MageSpark.*?)"/', $html, $result);
            if (count($result) >=2) {
                $path = $result[1];
                $newPath = $this->assetRepo->getUrl($path);
                if ($newPath) {
                    $html = str_replace($path, $newPath, $html);
                }
            }
        }

        return $html;
    }
}
