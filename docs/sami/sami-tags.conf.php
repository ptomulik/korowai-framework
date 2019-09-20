<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
  ->files()
  ->name("*.php")
  ->exclude("Tests")
  ->exclude("Resources")
  ->exclude("Behat")
  ->exclude("vendor")
  ->in($dir = __DIR__ . "/../../src");

$versions = GitVersionCollection::create($dir)
          ->addFromTags()
          ->add('master', 'master branch')
          ->add('devel', 'devel branch');

return new Sami( $iterator, array(
  'theme'     => 'default',
  'versions'  => $versions,
  'title'     => 'Korowai Framework API',
  'build_dir' => __DIR__ . '/../build/%version%/html/api',
  'cache_dir' => __DIR__ . '/../cache/%version%/html/api',
  'remote_repository' => new GithubRemoteRepository('korowai/framework')
));
