<?php
namespace Ewave\AI\Model\Integrations\Rule\Mapping;

abstract class MapperAbstract implements MapperInterface
{
    /**
     * @var \Ewave\AI\Api\MapperDataRepositoryInterface
     */
    protected $_mapperDataRepository;

    /**
     * MappingAbstract constructor.
     * @param \Ewave\AI\Api\MapperDataRepositoryInterface $mapperDataRepository
     */
    public function __construct(
        \Ewave\AI\Api\MapperDataRepositoryInterface $mapperDataRepository
    ) {
        $this->_mapperDataRepository = $mapperDataRepository;
    }

    /**
     * @return mixed
     */
    public function getSavedData()
    {
        return $this->_mapperDataRepository->loadByMappingCode($this->getMapperCode());
    }
}
