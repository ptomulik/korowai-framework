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
  ->in(__DIR__ . "/../../src");

return new Sami($iterator, array(
  'theme'     => 'default',
  'title'     => 'Korowai Framework API',
  'build_dir' => __DIR__ . '/../build/local/html/api',
  'cache_dir' => __DIR__ . '/../cache/local/html/api'
));
