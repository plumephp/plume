# plume

## Intro

plume is a micro framework for php.

## Feature

- Pure PHP
- multi environment support
- MVC and customizable view
- Fast implementation API with json data
- simple API to use
- cache with request and db
- exception logs
- performance logs
- unit test
- customizable
- Utils Class

## Install and Use

See [plume-demo-web](https://github.com/plumephp/plume-demo-web) and [docs](https://github.com/plumephp/docs).

## show details
index page:
```
require_once __DIR__.'/../vendor/autoload.php';
use Plume\Application;
$app = new Application();
$app->run();
```
change env:
```
$app = new Application(‘test’);
$app = new Application('pro');
```
setting default module or cache:
```
$app['plume.module.default']='example';
$app['plume.cache.request']=true;
$app['plume.cache.db']=true;
```

## Depends
Plume is pure PHP and just have mini dependence on phpunit, mysqli.

Have a good time with plume.