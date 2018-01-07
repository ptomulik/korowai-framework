<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Finder\Finder;

$versions = GitVersionCollection::create($dir)
          ->addFromTags();

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
  'versions'  => $versions,
  'title'     => 'Korowai - API documentation',
  'build_dir' => __DIR__ . '/build/docs/api/cache/%version%',
  'cache_dir' => __DIR__ . '/build/docs/api/build/%version%',
));
