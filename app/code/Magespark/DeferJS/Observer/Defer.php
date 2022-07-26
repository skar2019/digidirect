<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark_CopyCmsPageBlock
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */
namespace MageSpark\DeferJS\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use MageSpark\DeferJS\Helper\Utils as DeferHelper;

class Defer implements ObserverInterface
{
    /**
     * @var DeferHelper
     */
    public $helper;

    /**
     * Defer constructor.
     * @param DeferHelper $helper
     */
    public function __construct(
        DeferHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var Http  $request */
        $request = $observer->getEvent()->getRequest();
        if (!$this->helper->isEnabled($request)) {
            return;
        }

        $response = $observer->getEvent()->getResponse();
        if (!$response) {
            return;
        }

        $html = $response->getBody();
        if ($html === '') {
            return;
        }

        /** get and remove script tag */
        $conditionalJsPattern = '#(<\!--\[if[^\>]*>\s*<script.*</script>\s*<\!\[endif\]-->)|(<\!--\s*<script(?! nodefer).*</script>\s*-->)|(<script(?! nodefer).*</script>)#isU';
        preg_match_all($conditionalJsPattern, $html, $_matches);
        $_js = implode('', $_matches[0]);
        $html = preg_replace($conditionalJsPattern, '', $html);

        /** Check defer iframe setting and process defer iframe */
        if ($this->helper->isDeferIframe()) {
            $conditionalJsPattern = '#<iframe([^>]*) src="([^"/]*/?[^".]*\.[^"]*)"([^>]*)>#';
            preg_match_all($conditionalJsPattern, $html, $_matches);
            $iframe = $_matches[0];
            if ((!empty($iframe)) > 0) {
                $replace = '<iframe$1 data-src="$2"$3>';
                $html = preg_replace($conditionalJsPattern, $replace, $html);
                $_js .= '<script>
                function initDefer(){
                    for(var t=document.getElementsByTagName("iframe"),e=0;e<t.length;e++)
                        t[e].getAttribute("data-src")&&t[e].setAttribute("src",t[e].getAttribute("data-src"));
                };
                window.onload=initDefer;</script>';
            }
        }

        /** TODO: Always process Show controller path on setting even if defer js is disabled for controller or path */
        $html .= $this->getShowControllerPathHtmlBlock($request);

        /** Check if to Move Defer Javascript In HTML Body Tag and process the logic */
        if ($this->helper->inBody()) {
            /** remove <body></html> tag */
            $conditionalJsPattern = '#</body>\s*</html>#isU';
            preg_match_all($conditionalJsPattern, $html, $_matches);
            $_end = implode('', $_matches[0]);
            $html = preg_replace($conditionalJsPattern, '', $html);
            $html .= $_js . $_end;
        } else {
            $html .= $_js;
        }

        $response->setBody($html);
    }

    /**
     * Show path and Controller in bottom of the page
     *
     * @param $request
     * @return string
     */
    private function getShowControllerPathHtmlBlock($request)
    {
        $htmlContent = '';
        if ($this->helper->isShowControllersPath()) {
            $htmlContent = '<table border="1" style="width:auto;background-color:white">
                <tbody>
                    <tr>
                        <th>' . __('Controllers') . '</th>
                        <th>' . __('Path') . '</th>
                    </tr>
                    <tr>
                        <td>' . $request->getFullActionName() . '</td>
                        <td>' . $request->getRequestUri() . '</td>
                </tr>
                </tbody>
            </table>';
        }
        return $htmlContent;
    }
}
