<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
  ->files()
  ->name("*.php")
  ->exclude("Tests")
  ->exclude("Resources")
  ->exclude("Behat")
  ->exclude("vendor")
  ->in(__DIR__ . "/src");

return new Sami( $iterator, array(
  'theme'     => 'default',
  'title'     => 'Korowai - API documentation',
  'build_dir' => __DIR__ . '/build/docs/api/build/local',
  'cache_dir' => __DIR__ . '/build/docs/api/cache/local',
));
