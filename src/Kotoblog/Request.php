<?php

namespace Kotoblog;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Request extends BaseRequest
{
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;
    }
}
