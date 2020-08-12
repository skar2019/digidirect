<?php

namespace Ewave\AbstractGiftCard\Service\Http\Converter\Soap;

use Ewave\AbstractGiftCard\Service\Http\ConverterInterface;

/**
 * Class ObjectToArrayConverter
 * @package Ewave\AbstractGiftCard\Service\Http\Converter\Soap
 * @api
 */
class ObjectToArrayConverter implements ConverterInterface
{
    /**
     * Converts gateway response to ENV structure
     *
     * @param mixed $response
     * @return array
     * @throws \Ewave\AbstractGiftCard\Service\Http\ConverterException
     */
    public function convert($response)
    {
        $response = (array) $response;
        foreach ($response as $key => $value) {
            if (is_object($value)) {
                $response[$key] = $this->convert($value);
            }
        }

        return $response;
    }
}
