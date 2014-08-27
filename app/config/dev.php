<?php

require __DIR__ . '/prod.php';

$app['debug'] = true;
$app['kotoblog.host'] = 'http://kotoblog.local';
$app['cache.options'] = array("default" => array(
    "driver" => function() {
        $cache = new \Doctrine\Common\Cache\MemcacheCache();
        $memcache = new \Memcache;
        $memcache->pconnect('localhost', 11211);

        $cache->setMemcache($memcache);

        return $cache;
    }
));
