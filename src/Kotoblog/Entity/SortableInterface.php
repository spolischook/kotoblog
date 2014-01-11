<?php

namespace Kotoblog\Entity;

interface SortableInterface
{
    const LATEST = 'latest';
    const POPULAR = 'popular';

    public static function getPreferredSorting();
}
