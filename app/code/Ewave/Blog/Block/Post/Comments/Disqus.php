<?php
namespace Ewave\Blog\Block\Post\Comments;

use Ewave\Blog\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;

/**
 * Class Disqus
 */
class Disqus extends AbstractCommentType
{
    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->dataHelper->getCommentSettingsConfig('disqus_shortname');
    }
}
