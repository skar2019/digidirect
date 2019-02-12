<?php

namespace Ewave\Banner\Plugin\Magento\Widget\Model\Widget;

use Ewave\Banner\Helper\IssetTrait;
use Magento\Widget\Model\Widget\Instance as MagentoWidgetInstance;
use Magento\Framework\App\RequestInterface;

class Instance
{
    use IssetTrait;
    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @since 3.0.0 due EE/CE support simplifying
     * @var string
     */
    protected $requiredInstance;

    /**
     * Instance constructor.
     *
     * @param RequestInterface $request
     * @param string $requiredInstance
     */
    public function __construct(
        RequestInterface $request,
        $requiredInstance = \Magento\Banner\Block\Widget\Banner::class
    ) {
        $this->requiredInstance = $requiredInstance;
        $this->_request = $request;
    }

    /**
     * Additional validation for field "rotation speed", missed in default
     *
     * @param \Magento\Widget\Model\Widget\Instance $widgetInstance
     * @param mixed $result
     * @return null|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidate(\Magento\Widget\Model\Widget\Instance $widgetInstance, $result)
    {
        $widgetParameters = $this->_request->getParam('parameters');
        $rotationSpeed = $this->getByKey($widgetParameters, 'rotation_speed', null);
        if ($rotationSpeed && !is_numeric($rotationSpeed)) {
            return 'Rotation speed is not numeric';
        }

        return null;
    }

    /**
     * @param MagentoWidgetInstance $instance
     * @param string $container
     * @param string $templatePath
     * @return array
     */
    public function beforeGenerateLayoutUpdateXml(MagentoWidgetInstance $instance, $container, $templatePath = '')
    {
        if ($instance->getInstanceType() == $this->requiredInstance) {
            if ($templatePath == 'custom') {
                $templatePath = '';
            }
        }
        return [$container, $templatePath];
    }
}
