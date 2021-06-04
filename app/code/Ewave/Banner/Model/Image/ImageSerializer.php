<?php

namespace Ewave\Banner\Model\Image;

use Ewave\Banner\Model\Image\ImageSerializationException as ISException;

/**
 * Class added for centralization of serialization
 * Throws custom exception if error
 */
class ImageSerializer
{
    /**
     * @param [] $data
     * @return mixed
     * @throws ImageSerializationException
     */
    public function serialize($data)
    {
        try {
            return serialize($data);
        } catch (\Throwable $exception) {
            throw new ISException('Error while serialize', $exception->getCode(), $exception);
        }
    }

    /**
     * @param string $data
     * @return mixed
     * @throws ImageSerializationException
     */
    public function unserialize($data)
    {
        try {
            return unserialize($data);
        } catch (\Throwable $exception) {
            throw new ISException('Error while unserialize', $exception->getCode(), $exception);
        }
    }
}
