<?php

namespace Ewave\Localization\Model\Import;

interface ParserProcessorInterface
{
    /**
     * @param string $fileData
     * @return array
     */
    public function execute($fileData);
}
