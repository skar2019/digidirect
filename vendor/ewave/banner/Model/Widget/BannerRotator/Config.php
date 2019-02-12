<?php

namespace Ewave\Banner\Model\Widget\BannerRotator;

use Ewave\Banner\Helper\IssetTrait;

class Config
{
    use IssetTrait;
    /**
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return array|mixed
     */
    public function getFieldsWithCustomizedDependencies()
    {
        return $this->getByKey($this->config, 'fields', []);
    }
}
