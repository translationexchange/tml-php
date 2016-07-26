<?php

require __DIR__ . '/../vendor/autoload.php';

tml_init(array(
    "key"   => "c5d1005ced6df79cd2f7e78410134a68ec5546812013518a02402cfba1797eba",
    "host"  => "http://localhost:3000",
    "cdn_host" => "https://trex-snapshots-dev.s3-us-west-1.amazonaws.com",

    "locale" => array(
        "default" => 'en',
        "strategy" => 'param',
        "redirect" => true,
        "cookie" => true
    ),

//    "source" => function($original) {
//        $fragments = \Tml\Utils\StringUtils::split($original, '/');
//        array_shift($fragments);
//        $source = \Tml\Utils\StringUtils::join($fragments, '/');
//        return $source;
//    },

//    "source" => array(
//        "/\\/index\\/[\\d]*.*$/" => "/index",
//    ),

    "agent" => array(
        "host" => "http://localhost:8282/dist/agent.js"
    ),

    "cache_file" => array(
        "enabled" => true,
        "adapter" => "file",
        "path" => "",
        "version" => "20160323211903"
    ),
    "cache1" => array(
        "enabled" => true,
        "adapter" => "memcached",
        "host" => "localhost",
        "namespace" => "c5d1005",
        "port" => 11211,
        "version_check_interval" => 30
    ),
    "cache" => array(
        "enabled" => true,
        "adapter" => "memcache",
        "host" => "localhost",
        "namespace" => "c5d10051",
        "port" => 11211,
        "version_check_interval" => 30
    ),
    "cache_redis" => array(
        "enabled" => true,
        "adapter" => "redis",
        "host" => "localhost",
        "namespace" => "c5d1005",
        "port" => 6379,
        "version_check_interval" => 30
    ),
    "log" => array(
        "enabled" => true,
        "severity" => "debug"
    )
));

$router = new AltoRouter();

$router->map('GET', '/[:locale]?/docs/?', function ($locale = null) {
    require __DIR__ . '/www/docs.php';
});

$router->map('GET', '/[:locale]?/tml/?', function ($locale = null) {
    require __DIR__ . '/www/tml.php';
});

$router->map('GET', '/[:locale]?/editor/?', function ($locale = null) {
    require __DIR__ . '/www/editor.php';
});

$router->map('POST', '/[:locale]?/editor/?', function ($locale = null) {
    require __DIR__ . '/www/editor.php';
});

$router->map('GET', '/[:locale]?/editor_content/?', function ($locale = null) {
    require __DIR__ . '/www/editor_content.php';
});

$router->map('GET', '/[:locale]?/?', function ($locale = null) {
    require __DIR__ . '/www/index.php';
});

$match = $router->match();

// call closure or throw 404 status
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // no route was matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

tml_complete_request();




