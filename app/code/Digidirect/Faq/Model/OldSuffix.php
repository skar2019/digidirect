<?php

namespace Digidirect\Faq\Model;

/**
 * Old suffix Helper
 */
class OldSuffix
{
    /**
     * @var null|string
     */
    private $oldSuffix = null;

    /**
     * @param string $suffix
     * @return OldSuffix
     */
    public function setOldSuffix(string $suffix): self
    {
        if (null === $this->oldSuffix) {
            $this->oldSuffix = $suffix;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOldSuffix()
    {
        return $this->oldSuffix;
    }
}
