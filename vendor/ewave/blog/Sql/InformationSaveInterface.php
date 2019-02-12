<?php

namespace Ewave\Blog\Sql;

interface InformationSaveInterface
{
    /**
     * @param \Ewave\Blog\Model\Post $entity
     * @return mixed
     */
    public function saveInformation($entity);
}
