<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/AbstractQuery.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

use Korowai\Component\Ldap\Adapter\QueryInterface;
use Korowai\Component\Ldap\Adapter\ResultInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class AbstractQuery implements QueryInterface
{
    /** @var string */
    protected $base_dn;
    /** @var string */
    protected $filter;
    /** @var ResultInterface */
    protected $result = null;
    /** @var array */
    protected $options;


    /**
     * Constructs AbstractQuery
     *
     * @param string $base_dn
     * @param string $filter
     * @param array $options
     */
    public function __construct(string $base_dn, string $filter, array $options = array())
    {
        $this->base_dn = $base_dn;
        $this->filter = $filter;
        $resolver = new OptionsResolver;
        $this->configureOptionsResolver($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Returns defaults for query options
     * @return array Default options
     */
    public static function getDefaultOptions() : array
    {
        return array(
            'scope' => 'sub',
            'attributes' => '*',
            'attrsOnly' => 0,
            'deref' => 'never',
            'sizeLimit' => 0,
            'timeLimit' => 0,
        );
    }

    /**
     * Returns ``$base_dn`` provided to ``__construct()`` at creation time
     * @return string The ``$base_dn`` value provided to ``__construct()``
     */
    public function getBaseDn()
    {
        return $this->base_dn;
    }

    /**
     * Returns ``$filter`` provided to ``__construct()`` at creation time
     * @return string The ``$filter`` value provided to ``__construct()``
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Get the options used by this query.
     *
     * The returned array contains ``$options`` provided to ``__construct()``,
     * but also includes defaults applied internally by this object.
     *
     * @return array Options used by this query
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult() : ResultInterface
    {
        if (!isset($this->result)) {
            return $this->execute();
        }
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : ResultInterface
    {
        $this->result = $this->doExecuteQuery();
        return $this->result;
    }

    /**
     * Executes query and returns result
     *
     * This method should be implemented in subclass.
     *
     * @return ResultInterface Result of the query.
     */
    abstract protected function doExecuteQuery() : ResultInterface;


    /**
     * @internal
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults(static::getDefaultOptions());

        $resolver->setAllowedValues('scope', array('base', 'one', 'sub'));
        $resolver->setAllowedValues('deref', array('always', 'never', 'finding', 'searching'));

        $resolver->setNormalizer('attributes', function (Options $optins, $value) {
            return is_array($value) ? $value : array($value);
        });
    }
}

// vim: syntax=php sw=4 ts=4 et:
