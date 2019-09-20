<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/Query.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\AbstractQuery;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ResultInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\LastLdapException;

use function Korowai\Lib\Context\with;
use Korowai\Lib\Error\EmptyErrorHandler;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Query extends AbstractQuery
{
    use EnsureLdapLink;
    use LastLdapException;

    /** @var LdapLink */
    private $link;

    /**
     * Constructs Query
     */
    public function __construct(LdapLink $link, string $base_dn, string $filter, array $options = array())
    {
        $this->link  = $link;
        parent::__construct($base_dn, $filter, $options);
    }

    /**
     * Returns a link resource
     *
     * @return resource
     */
    public function getLink()
    {
        return $this->link;
    }

    protected static function getDerefOption(array $options)
    {
        if (isset($options['deref'])) {
            return constant('LDAP_DEREF_' . strtoupper($options['deref']));
        } else {
            return LDAP_DEREF_NEVER;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecuteQuery() : ResultInterface
    {
        $options = $this->getOptions();
        $scope = strtolower(isset($options['scope']) ? $options['scope'] : 'sub');
        switch ($scope) {
            case 'base':
                $func = 'read';
                break;
            case 'one':
                $func = 'list';
                break;
            case 'sub':
                $func = 'search';
                break;
            default:
                // This should be actualy caught by OptionsResolver
                throw new \RuntimeException(sprintf('Unsupported search scope "%s"', $options['scope']));
        }

        static::ensureLdapLink($this->link);
        return with(EmptyErrorHandler::getInstance())(function ($eh) use ($func) {
            return $this->doExecuteQueryImpl($func);
        });
    }

    private function doExecuteQueryImpl($func)
    {
        $options = $this->getOptions();
        $result = call_user_func(
            array($this->link, $func),
            $this->base_dn,
            $this->filter,
            $options['attributes'],
            $options['attrsOnly'],
            $options['sizeLimit'],
            $options['timeLimit'],
            static::getDerefOption($options)
        );
        if (false === $result) {
            throw static::lastLdapException($this->link);
        }
        return $result;
    }


    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        return parent::configureOptionsResolver($resolver);
    }
}

// vim: syntax=php sw=4 ts=4 et:
