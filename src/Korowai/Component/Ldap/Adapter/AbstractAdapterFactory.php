<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/AbstractAdapterFactory.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

use Korowai\Component\Ldap\Adapter\AdapterFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Abstract base class for Adapter factories.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class AbstractAdapterFactory implements AdapterFactoryInterface
{
    /** @var array */
    private $config;

    /**
     * Creates an AbstractAdapterFactory
     *
     * @param array $config A config to be passed to configure() (if present).
     */
    public function __construct(array $config = null)
    {
        if (isset($config)) {
            $this->configure($config);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $config)
    {
        $resolver = new OptionsResolver;

        $this->configureOptionsResolver($resolver);
        $resolver->setDefault('options', function (OptionsResolver $nestedResolver) {
            return $this->configureNestedOptionsResolver($nestedResolver);
        });

        $this->config = $resolver->resolve($config);
    }

    /**
     * Return configuration array previously set with configure().
     *
     * If configuration is not set yet, null is returned.
     *
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Configures OptionsResolver for this AdapterFactory
     *
     * This method only configures common config options, that are provider
     * independent. Provider-specific options should be implented as nested
     * options. They should be configured by `configureNestedOptionsResolver()`.
     *
     * @param OptionsResolver $resolver The resolver to be configured
     *
     * @internal
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'host' => 'localhost',
            'uri' => null,
            'encryption' => 'none',
        ));

        $resolver->setDefault('port', function (Options $options) {
            return ('ssl' === $options['encryption']) ? 636 : 389;
        });

        $resolver->setDefault('uri', function (Options $options) {
            $port = (
                'ssl' === $options['encryption'] && $options['port'] !== 636 ||
                'ssl' !== $options['encryption'] && $options['port'] !== 389
            ) ? sprintf(':%d', $options['port']) : '';
            $protocol = ('ssl' === $options['encryption'] ? 'ldaps' : 'ldap');
            return  $protocol .  '://' . $options['host'] .  $port;
        });

        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('port', 'numeric');
        $resolver->setAllowedTypes('uri', 'string');
        $resolver->setAllowedValues('encryption', array('none', 'ssl', 'tls'));

        $resolver->setAllowedValues('port', function ($port) {
            return $port > 0 && $port < 65536;
        });
    }

    /**
     * Configures options resolver for nested options (provider-specific)
     *
     * The resolver passed to method as $resolver will be responsible for
     * resolving ``$config['options']`` array, i.e. options nested in
     * config array. The nested options are thought to be adapter-specific
     * (e.g specific to ext-ldap).
     *
     * @param OptionsResolver $resolver The resolver to be configured
     */
    abstract protected function configureNestedOptionsResolver(OptionsResolver $resolver);
}

// vim: syntax=php sw=4 ts=4 et:
