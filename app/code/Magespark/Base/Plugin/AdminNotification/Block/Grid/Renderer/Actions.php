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

namespace MageSpark\Base\Plugin\AdminNotification\Block\Grid\Renderer;

use Magento\AdminNotification\Block\Grid\Renderer\Actions as NativeActions;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;

/**
 * Class Actions
 *
 * @package MageSpark\Base\Plugin\AdminNotification\Block\Grid\Renderer
 */
class Actions
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Actions constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param NativeActions $subject
     * @param \Closure $proceed
     * @param DataObject $row
     * @return mixed|string
     */
    public function aroundRender(
        NativeActions $subject,
        \Closure $proceed,
        DataObject $row
    ) {
        $result = $proceed($row);
        if ($row->getData('is_magespark')) {
            $result .= sprintf(
                '<a class="action" href="%s" title="%s">%s</a>',
                $this->urlBuilder->getUrl('msbase/notification/frequency/'). 'action/less',
                __('Show less of these messages'),
                __('Show less of these messages')
            );
            $result .= sprintf(
                '<a class="action" href="%s" title="%s">%s</a>',
                $this->urlBuilder->getUrl('msbase/notification/frequency/'). 'action/more',
                __('Show more of these messages'),
                __('Show more of these messages')
            );
            $result .= sprintf(
                '<a class="action" href="%s" title="%s">%s</a>',
                $this->urlBuilder->getUrl('adminhtml/system_config/edit/'). 'section/magespark_base',
                __('Unsubscribe'),
                __('Unsubscribe')
            );
        }

        return  $result;
    }
}
