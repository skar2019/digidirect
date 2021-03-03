<?php
namespace Digidirect\Navigation\Model;

use Magento\Framework\Model\AbstractModel;
use Digidirect\Navigation\Api\Data\SetInterface;

class Set extends AbstractModel implements SetInterface
{
    /**
     * Set resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Navigation\Model\ResourceModel\Set');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getSetId()
    {
        return $this->getData(self::SET_ID);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getSetCode()
    {
        return $this->getData(self::SET_CODE);
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return SetInterface
     */
    public function setSetId($id)
    {
        return $this->setData(self::SET_ID, $id);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return SetInterface
     */
    public function setSetCode($code)
    {
        return $this->setData(self::SET_CODE, $code);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SetInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return SetInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
