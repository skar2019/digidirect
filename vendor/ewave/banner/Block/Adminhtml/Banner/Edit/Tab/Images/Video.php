<?php

namespace Ewave\Banner\Block\Adminhtml\Banner\Edit\Tab\Images;

use Ewave\Banner\Component\Json;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Video extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Anchor is product video
     */
    const PATH_ANCHOR_PRODUCT_VIDEO = 'banner_video-link';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $videoSelector = '#banner_images_gallery__content';

    /**
     * @var string
     */
    protected $_template = "Ewave_Banner::slideout/form.phtml";

    /**
     * @var \Ewave\Banner\Helper\Video\Config
     */
    protected $videoConfig;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * Video constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param Json $jsonComponent
     * @param \Ewave\Banner\Helper\Video\Config $videoConfig
     * @param string $template
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        Json $jsonComponent,
        \Ewave\Banner\Helper\Video\Config $videoConfig,
        $template = 'Ewave_Banner::slideout/form.phtml',
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonComponent = $jsonComponent;
        $this->setUseContainer(true);
        $this->_template = $template;
        $this->videoConfig = $videoConfig;
        $this->assetRepo = $context->getAssetRepository();
    }

    /**
     * Form preparation
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'new_video_form',
                'class' => 'admin__scope-old',
                'enctype' => 'multipart/form-data',
            ],
        ]);
        $form->setUseContainer($this->getUseContainer());
        $form->addField('new_video_messages', 'note', []);
        $fieldset = $form->addFieldset('new_video_form_fieldset', []);
        $fieldset->addField(
            '',
            'hidden',
            [
                'name' => 'form_key',
                'value' => $this->getFormKey(),
            ]
        );
        $fieldset->addField(
            'item_id',
            'hidden',
            []
        );
        $fieldset->addField(
            'file_name',
            'hidden',
            []
        );
        $fieldset->addField(
            'video_provider',
            'hidden',
            [
                'name' => 'video_provider',
            ]
        );

        $fieldset->addField(
            'video_title',
            'text',
            [
                'class' => 'edited-data',
                'label' => __('Title'),
                'title' => __('Title'),
                'name' => 'video_title',
            ]
        );

        $fieldset->addField(
            'use_video_as_a_preview',
            'select',
            [
                'class' => 'edited-data',
                'label' => __('Use video as a Preview'),
                'title' => __('Use video as a Preview'),
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'name' => 'use_video_as_a_preview',
            ]
        );

        $fieldset->addField(
            'new_video_screenshot',
            'file',
            [
                'label' => __('Preview Image'),
                'title' => __('Preview Image'),
                'name' => 'image',
            ]
        );

        $fieldset->addField(
            'new_video_screenshot_preview',
            'button',
            [
                'class' => 'preview-image-hidden-input',
                'label' => '',
                'name' => '_preview',
            ]
        );

        $fieldset->addField(
            'new_video_file',
            'file',
            [
                'label' => __('Video'),
                'title' => __('Video'),
                'name' => 'video',
                'note' => __('Max allowed file size %1', ini_get('upload_max_filesize')),
            ]
        );

        $fieldset->addField(
            'new_video_video_preview',
            'button',
            [
                'class' => 'preview-image-hidden-input',
                'label' => '',
                'name' => '_preview',
            ]
        );

        $fieldset->addField(
            'play_video_automatically_for_desktop',
            'select',
            [
                'class' => 'edited-data',
                'label' => __('Play video automatically for desktop'),
                'title' => __('Play video automatically for desktop'),
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'name' => 'play_video_automatically_for_desktop',
            ]
        );

        $fieldset->addField(
            'play_video_automatically_for_mobile',
            'select',
            [
                'class' => 'edited-data',
                'label' => __('Play video automatically for mobile'),
                'title' => __('Play video automatically for mobile'),
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'name' => 'play_video_automatically_for_mobile',
            ]
        );

        $fieldset->addField(
            'play_video_after',
            'text',
            [
                'class' => 'edited-data validate-number',
                'label' => __('Play after X Seconds'),
                'title' => __('Play after X Seconds'),
                'name' => 'play_video_after',
                'note' => __('If empty the video will play automatically after zero seconds'),
            ]
        );

        $fieldset->addField(
            'play_video_in_a_loop',
            'select',
            [
                'class' => 'edited-data',
                'label' => __('Play video in a loop'),
                'title' => __('Play video in a loop'),
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'name' => 'play_video_in_a_loop',
            ]
        );

        $fieldset->addField(
            'allow_video_popup',
            'select',
            [
                'class' => 'edited-data',
                'label' => __('Allow Video Popup'),
                'title' => __('Allow Video Popup'),
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'name' => 'allow_video_popup',
            ]
        );

        $fieldset->addField(
            'video_folder',
            'hidden',
            [
                'class' => 'edited-data',
                'name' => 'video_folder',
            ]
        );

        $this->addMediaRoleAttributes($fieldset);
        $this->setForm($form);
    }

    /**
     * Get html id
     *
     * @return mixed
     */
    public function getHtmlId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->mathRandom->getUniqueHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get widget options
     *
     * @return string
     */
    public function getWidgetOptions()
    {
        return $this->jsonComponent->encode(
            [
                'saveVideoUrl' => $this->getUrl('ewave_banner/video/upload'),
                'saveImageUrl' => $this->getUrl('ewave_banner/gallery/upload'),
                'htmlId' => $this->getHtmlId(),
                'videoSelector' => $this->videoSelector,
                'previewImageAllowedExtensions' => $this->videoConfig->getPreviewImageAllowedExtensions(true),
                'placeholder' => $this->assetRepo->getUrl('Ewave_Banner::images/placeholder.jpg'),
            ]
        );
    }

    /**
     *
     * Add media role attributes to fieldset
     *
     * @param Fieldset $fieldset
     * @return $this
     */
    protected function addMediaRoleAttributes(Fieldset $fieldset)
    {
        $fieldset->addField('role-label', 'note', []);

        $options = [];
        foreach ($this->videoConfig->getRoles() as $code => $title) {
            $options[$code]['label'] = $title;
            $options[$code]['value'] = $code;
        }

        $fieldset->addField(
            'video_roles',
            'multiselect',
            [
                'class' => 'edited-data',
                'label' => __('Role'),
                'title' => __('Role'),
                'values' => $options,
                'name' => 'video_roles[]',
                'required' => true,
            ]
        );

        return $this;
    }
}
