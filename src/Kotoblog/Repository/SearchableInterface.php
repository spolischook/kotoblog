<?php

namespace Kotoblog\Repository;

interface SearchableInterface
{
    public function createIndex($object);
    public function updateIndex($object);
}