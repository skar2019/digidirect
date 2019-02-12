<?php

namespace Ewave\CmsUpgrade\Model\Widget;

use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use Magento\Theme\Model\Theme;

class ThemeResolver
{
    /**
     * @var CollectionFactory
     */
    protected $themeCollectionFactory;

    /**
     * @var array
     */
    protected $themeById = [];

    /**
     * @var array
     */
    protected $themeByCode = [];

    /**
     * ThemeResolver constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->themeCollectionFactory = $collectionFactory;
    }

    /**
     * @param $id
     * @return Theme|null
     */
    public function getThemeCodeById($id)
    {
        return $this->callIfObject($this->getTheme('id', $id), 'getCode');
    }

    /**
     * @param $code
     * @return Theme|null
     */
    public function getThemeIdByCode($code)
    {
        return $this->callIfObject($this->getTheme('code', $code), 'getId');
    }

    protected function callIfObject($theme, $method)
    {
        return $theme instanceof Theme ? $theme->$method() : null;
    }

    /**
     * @param string $by
     * @param mixed $value
     * @return Theme|null
     */
    protected function getTheme($by, $value)
    {
        $variable = 'themeBy' . ucfirst($by);
        if (property_exists($this, $variable)) {

            $theme = $this->$variable[$value] ?? null;
            if (null === $theme) {
                $themes = $this->themeCollectionFactory->create();
                /**
                 * @var $theme Theme
                 */
                foreach ($themes as $theme) {
                    $this->themeById[$theme->getThemeId()] = $theme;
                    $this->themeByCode[$theme->getCode()] = $theme;
                }
            }
        }

        return $this->$variable[$value] ?? null;
    }
}
