<?php

namespace Ewave\CmsUpgrade\Controller\Adminhtml;

/**
 * Class Generate
 * @package Ewave\CmsUpgrade\Controller\Adminhtml
 */
abstract class Generate extends \Magento\Backend\App\Action
{
    /**
     * @param string $result
     * @return void
     */
    public function setGenerateResult($result)
    {
        if ($result->getScriptApplied()) {
            $this->messageManager->addSuccessMessage(
                __(
                    'Upgrade Script Generated and placed to cms_upgrade_setup folder. FileName is: %1',
                    $result->getScriptFileName()
                )
            );
        } else {
            $this->messageManager->addNotice(
                __(
                    'Upgrade Script Generated but can\'t be moved to cms_upgrade_setup folder. Move it there manually.
                     File is : %1',
                    $result->getScriptFileName()
                )
            );
        }

        if ($result->getConfigVersionApplied()) {
            $this->messageManager->addSuccessMessage(
                __('Data Version Changed successfully.' . ' New Version is: %1', $result->getNextConfigVersion())
            );
        } else {
            $this->messageManager->addErrorMessage(
                __(
                    'Extension Version should be changed manually. ' . 'New Data Version is: %1',
                    $result->getNextConfigVersion()
                )
            );
        }
    }
}
