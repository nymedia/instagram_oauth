<?php

require_once __DIR__ . '/../vendor/autoload.php';

$yaml = new \Symfony\Component\Yaml\Parser();

$app = new Silex\Application();
// $app['debug'] = TRUE;
$app['settings'] = $yaml->parse(file_get_contents(__DIR__ . '/../config.yml'));

$app->get('/', 'InstagramOAuth\\Controller\\Redirect::auth');
$app->get('/privacy.html', 'InstagramOAuth\\Controller\\Page::privacy');

$app->run();
