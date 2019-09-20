<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/LdapLinkTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests\Adapter\ExtLdap;

use PHPUnit\Framework\TestCase;

use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReference;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class LdapLinkTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    public function getLdapFunctionMock(...$args)
    {
        return $this->getFunctionMock('\Korowai\Component\Ldap\Adapter\ExtLdap', ...$args);
    }

    private function createLdapLink($host = 'host', $port = 123, $resource = 'ldap link')
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with($host, $port)
                ->willReturn($resource);
        return LdapLink::connect($host, $port);
    }

    private function getResultMock($link = null, $resource = 'ldap result')
    {
        $result = $this->createMock(Result::class);
        if($link) {
            $result->method('getLink')
                   ->with()
                   ->willReturn($link);
        }
        if($resource) {
            $result->method('getResource')
                   ->with()
                   ->willReturn($resource);
        }
        return $result;
    }

    private function getResultEntryMock($result = null, $resource = 'ldap result entry')
    {
        $entry = $this->createMock(ResultEntry::class);
        if($result) {
            $entry->method('getResult')
                  ->with()
                  ->willReturn($result);
        }
        if($resource) {
            $entry->method('getResource')
                  ->with()
                  ->willReturn($resource);
        }
        return $entry;
    }

    public function test_isLdapLinkResource_Null()
    {
        $this->assertSame(false, LdapLink::isLdapLinkResource(null));
    }

    public function test_isLdapLinkResource_NotResource()
    {
        $this->assertSame(false, LdapLink::isLdapLinkResource("foo"));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_isLdapLinkResource()
    {
        $this->assertSame(true, LdapLink::isLdapLinkResource(ldap_connect("localhost")));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_isLdapLinkResource_Closed()
    {
        $res = ldap_connect("localhost");
        ldap_close($res);
        $this->assertSame(false, LdapLink::isLdapLinkResource($res));
    }

    public function test_getResource_Null()
    {
        $link = new LdapLink(null);
        $this->assertNull($link->getResource());
    }

    public function test_getResource_LdapLink()
    {
        $link = new LdapLink("ldap link");
        $this->assertSame("ldap link", $link->getResource());
    }

    public function test_isValid_Null()
    {
        $link = new LdapLink(null);
        $this->assertSame(false, $link->isValid());
    }

    public function test_isValid_NotResource()
    {
        $link = new LdapLink("foo");
        $this->assertSame(false, $link->isValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_isValid()
    {
        // Mocking it would be so hard...
        $link = new LdapLink(ldap_connect("localhost"));
        $this->assertSame(true, $link->isValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_isValid_Closed()
    {
        $res = ldap_connect("localhost");
        ldap_close($res);
        $link = new LdapLink($res);
        $this->assertSame(false, $link->isValid());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_add()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_add")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', array('foo', 'bar'))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->add('dc=korowai,dc=org', array('foo', 'bar')));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_bind()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_bind")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', '$3cr3t')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->bind('dc=korowai,dc=org', '$3cr3t'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_bind_0args()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_bind")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->bind());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_bind_1arg()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_bind")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->bind('dc=korowai,dc=org'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_close()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_close")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->close());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_compare()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_compare")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'attribute', 'value')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->compare('dc=korowai,dc=org', 'attribute', 'value'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_connect_Defaults()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with()
                ->willReturn('ldap link');

        $ldap = LdapLink::connect();

        $this->assertSame('ldap link', $ldap->getResource());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_connect_Host()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with('host')
                ->willReturn('ldap link');

        $ldap = LdapLink::connect('host');

        $this->assertSame('ldap link', $ldap->getResource());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_connect_HostPort()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with('host', 123)
                ->willReturn('ldap link');

        $ldap = LdapLink::connect('host', 123);

        $this->assertSame('ldap link', $ldap->getResource());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_control_paged_result_response_1()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_control_paged_result_response")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->control_paged_result_response($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_control_paged_result_response_2()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_control_paged_result_response")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturnCallback(function($ldap, $result, &...$tail) {
                    if(count($tail) > 0) { $tail[0] = 'cookie'; }
                    if(count($tail) > 1) { $tail[1] = 'estimated'; }
                    return 'ok';
                });

        $this->assertSame('ok', $ldap->control_paged_result_response($result, $cookie));
        $this->assertSame('cookie', $cookie);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_control_paged_result_response_3()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_control_paged_result_response")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturnCallback(function($ldap, $result, &...$tail) {
                    if(count($tail) > 0) { $tail[0] = 'cookie'; }
                    if(count($tail) > 1) { $tail[1] = 'estimated'; }
                    return 'ok';
                });

        $this->assertSame('ok', $ldap->control_paged_result_response($result, $cookie, $estimated));
        $this->assertSame('cookie', $cookie);
        $this->assertSame('estimated', $estimated);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_control_paged_result_1()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_control_paged_result")
                ->expects($this->once())
                ->with('ldap link', 333)
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->control_paged_result(333));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_control_paged_result_2()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_control_paged_result")
                ->expects($this->once())
                ->with('ldap link', 333, true, "cookie")
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->control_paged_result(333, true, "cookie"));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_count_entries()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_count_entries")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn(333);

        $this->assertSame(333, $ldap->count_entries($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_count_references()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

// FIXME: uncomment, once it's implemented
//        $this   ->getLdapFunctionMock("ldap_count_references")
//                ->expects($this->once())
//                ->with('ldap link', 'ldap result')
//                ->willReturn(333);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Not implemented');
        $this->assertSame(333, $ldap->count_references($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_delete()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_delete")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->delete('dc=korowai,dc=org'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_dn2ufn()
    {
        $this   ->getLdapFunctionMock("ldap_dn2ufn")
                ->expects($this->once())
                ->with('dc=korowai,dc=org')
                ->willReturn('korowai.org');

        $this->assertSame('korowai.org', LdapLink::dn2ufn('dc=korowai,dc=org'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_err2str()
    {
        $this   ->getLdapFunctionMock("ldap_err2str")
                ->expects($this->once())
                ->with(2)
                ->willReturn('Protocol error');

        $this->assertSame('Protocol error', LdapLink::err2str(2));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_errno()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with('host', 123)
                ->willReturn('ldap link');

        $this   ->getLdapFunctionMock("ldap_errno")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn(2);

        $ldap = LdapLink::connect('host', 123);
        $this->assertSame(2, $ldap->errno());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_error()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with('host', 123)
                ->willReturn('ldap link');

        $this   ->getLdapFunctionMock("ldap_error")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn('Protocol error');

        $ldap = LdapLink::connect('host', 123);
        $this->assertSame('Protocol error', $ldap->error());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_escape()
    {
        $this   ->getLdapFunctionMock("ldap_escape")
                ->expects($this->once())
                ->with('dc=korowai,dc=org', null, 2)
                ->willReturn('dc\\3dkorowai\\2cdc\\3dorg');

        $this->assertSame('dc\\3dkorowai\\2cdc\\3dorg', LdapLink::escape('dc=korowai,dc=org', null, 2));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_explode_dn()
    {
        $this   ->getLdapFunctionMock("ldap_explode_dn")
                ->expects($this->once())
                ->with('dc=korowai,dc=org', 0)
                ->willReturn(array(
                    0 => 'dc=korowai',
                    1 => 'dc=org',
                    'count' => 2,
                ));

        $this->assertSame(array(0 => 'dc=korowai', 1 => 'dc=org', 'count' => 2), LdapLink::explode_dn('dc=korowai,dc=org', 0));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_first_attribute()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);
        $entry = $this->getResultEntryMock($result);

        $this   ->getLdapFunctionMock("ldap_first_attribute")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry')
                ->willReturn('first attribute');

        $this->assertSame('first attribute', $ldap->first_attribute($entry));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_first_entry_1()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_first_entry")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn(false);

        $this->assertFalse($ldap->first_entry($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_first_entry_2()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_first_entry")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn('ldap result entry');


        $this->assertEquals(new ResultEntry('ldap result entry', $result), $ldap->first_entry($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_first_reference_1()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_first_reference")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn(false);

        $this->assertFalse($ldap->first_reference($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_first_reference_2()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_first_reference")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn('ldap result reference');


        $this->assertEquals(new ResultReference('ldap result reference', $result), $ldap->first_reference($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_free_result()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_free_result")
                ->expects($this->once())
                ->with('ldap result')
                ->willReturn('ok');

        $this->assertSame('ok', LdapLink::free_result($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_attributes()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with('host', 123)
                ->willReturn('ldap link');

        $ldap = LdapLink::connect('host', 123);
        $entry = $this->getResultEntryMock();

        $this   ->getLdapFunctionMock("ldap_get_attributes")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry')
                ->willReturn('all attributes');

        $this->assertSame('all attributes', $ldap->get_attributes($entry));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_dn()
    {
        $this   ->getLdapFunctionMock("ldap_connect")
                ->expects($this->once())
                ->with('host', 123)
                ->willReturn('ldap link');

        $ldap = LdapLink::connect('host', 123);
        $entry = $this->getResultEntryMock();

        $this   ->getLdapFunctionMock("ldap_get_dn")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry')
                ->willReturn('dc=korowai,dc=org');

        $this->assertSame('dc=korowai,dc=org', $ldap->get_dn($entry));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_entries()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_get_entries")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturn('entries');

        $this->assertSame('entries', $ldap->get_entries($result));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_option()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_get_option")
                ->expects($this->once())
                ->with('ldap link', 12)
                ->willReturnCallback(function($link, $name, &$value) {
                    $value = 12;
                    return 'ok';
                });

        $this->assertSame('ok', $ldap->get_option(12, $value));
        $this->assertSame(12, $value);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_values_len()
    {
        $ldap = $this->createLdapLink();
        $entry = $this->getResultEntryMock();

        $this   ->getLdapFunctionMock("ldap_get_values_len")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry', 'attribute')
                ->willReturn(3);

        $this->assertSame(3, $ldap->get_values_len($entry, 'attribute'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_values()
    {
        $ldap = $this->createLdapLink();
        $entry = $this->getResultEntryMock();

        $this   ->getLdapFunctionMock("ldap_get_values")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry', 'attribute')
                ->willReturn('values');

        $this->assertSame('values', $ldap->get_values($entry, 'attribute'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_list_1()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_list")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'objectclass=*')
                ->willReturn(false);

        $this->assertSame(false, $ldap->list('dc=korowai,dc=org', 'objectclass=*'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_list_2()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_list")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'objectclass=*')
                ->willReturn('ldap result');

        $this->assertEquals(new Result('ldap result', $ldap), $ldap->list('dc=korowai,dc=org', 'objectclass=*'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_mod_add()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_mod_add")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', array('entry'))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->mod_add('dc=korowai,dc=org', array('entry')));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_mod_del()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_mod_del")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', array('entry'))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->mod_del('dc=korowai,dc=org', array('entry')));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_mod_replace()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_mod_replace")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', array('entry'))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->mod_replace('dc=korowai,dc=org', array('entry')));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_modify_batch()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_modify_batch")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', array('entry'))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->modify_batch('dc=korowai,dc=org', array('entry')));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_modify()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_modify")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', array('entry'))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->modify('dc=korowai,dc=org', array('entry')));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_next_attribute()
    {
        $ldap = $this->createLdapLink();
        $entry = $this->getResultEntryMock();

        $this   ->getLdapFunctionMock("ldap_next_attribute")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry')
                ->willReturn('next attribute');

        $this->assertSame('next attribute', $ldap->next_attribute($entry));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_next_entry_1()
    {
        $ldap = $this->createLdapLink();
        $entry = $this->getResultEntryMock();

        $this   ->getLdapFunctionMock("ldap_next_entry")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry')
                ->willReturn(false);

        $this->assertFalse($ldap->next_entry($entry));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_next_entry_2()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock();
        $entry = $this->getResultEntryMock($result);

        $this   ->getLdapFunctionMock("ldap_next_entry")
                ->expects($this->once())
                ->with('ldap link', 'ldap result entry')
                ->willReturn('next entry');

        $this->assertEquals(new ResultEntry('next entry', $result), $ldap->next_entry($entry));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_next_reference_1()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);
        $first = new ResultReference('first reference', $result);

        $this   ->getLdapFunctionMock("ldap_next_reference")
                ->expects($this->once())
                ->with('ldap link', 'first reference')
                ->willReturn(false);

        $this->assertFalse($ldap->next_reference($first));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_next_reference_2()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);
        $first = new ResultReference('first reference', $result);

        $this   ->getLdapFunctionMock("ldap_next_reference")
                ->expects($this->once())
                ->with('ldap link', 'first reference')
                ->willReturn('next reference');


        $this->assertEquals(new ResultReference('next reference', $result), $ldap->next_reference($first));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_parse_reference()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);
        $ref = new ResultReference('ldap result reference', $result);

        $this   ->getLdapFunctionMock("ldap_parse_reference")
                ->expects($this->once())
                ->with('ldap link', 'ldap result reference')
                ->willReturnCallback(function($link, $ref, &$referrals) {
                    $referrals = array('ref 1', 'ref 2');
                    return 'ok';
                });


        $this->assertSame('ok', $ldap->parse_reference($ref, $referrals));
        $this->assertSame(array('ref 1', 'ref 2'), $referrals);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_parse_result()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_parse_result")
                ->expects($this->once())
                ->with('ldap link', 'ldap result')
                ->willReturnCallback(function ($link, $result, &$errcode, &...$tail) {
                    $errcode = 12;
                    if(count($tail) > 0) { $tail[0] = 'matcheddn'; }
                    if(count($tail) > 1) { $tail[1] = 'errmsg'; }
                    if(count($tail) > 2) { $tail[2] = 'referrals'; }
                    return 'ok';
                });

        $this->assertSame('ok', $ldap->parse_result($result, $errcode));
        $this->assertSame(12, $errcode);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_read_1()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_read")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'objectclass=*')
                ->willReturn(false);

        $this->assertSame(false, $ldap->read('dc=korowai,dc=org', 'objectclass=*'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_read_2()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_read")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'objectclass=*')
                ->willReturn('ldap result');

        $this->assertEquals(new Result('ldap result', $ldap), $ldap->read('dc=korowai,dc=org', 'objectclass=*'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_rename()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_rename")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'cn=ldap', 'dc=example,dc=org', true)
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->rename('dc=korowai,dc=org', 'cn=ldap', 'dc=example,dc=org', true));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_sasl_bind()
    {
        $ldap = $this->createLdapLink();
        $this   ->getLdapFunctionMock("ldap_sasl_bind")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', '$3cr3t', 'mech', 'realm', 'authc_id', 'authz_id', 'props')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->sasl_bind('dc=korowai,dc=org', '$3cr3t', 'mech', 'realm', 'authc_id', 'authz_id', 'props'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_search_1()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_search")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'objectclass=*')
                ->willReturn(false);

        $this->assertSame(false, $ldap->search('dc=korowai,dc=org', 'objectclass=*'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_search_2()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_search")
                ->expects($this->once())
                ->with('ldap link', 'dc=korowai,dc=org', 'objectclass=*')
                ->willReturn('ldap result');

        $this->assertEquals(new Result('ldap result', $ldap), $ldap->search('dc=korowai,dc=org', 'objectclass=*'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_set_option()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_set_option")
                ->expects($this->once())
                ->with('ldap link', 12, 'value')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->set_option(12, 'value'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_set_rebind_proc()
    {
        $ldap = $this->createLdapLink();

        $proc = function() {};

        $this   ->getLdapFunctionMock("ldap_set_rebind_proc")
                ->expects($this->once())
                ->with('ldap link', $this->identicalTo($proc))
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->set_rebind_proc($proc));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_sort()
    {
        $ldap = $this->createLdapLink();
        $result = $this->getResultMock($ldap);

        $this   ->getLdapFunctionMock("ldap_sort")
                ->expects($this->once())
                ->with('ldap link', 'ldap result', 'sortfilter')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->sort($result, 'sortfilter'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_start_tls()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_start_tls")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->start_tls());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_unbind()
    {
        $ldap = $this->createLdapLink();

        $this   ->getLdapFunctionMock("ldap_unbind")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn('ok');

        $this->assertSame('ok', $ldap->unbind());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_destruct_Uninitialized()
    {
        $this   ->getLdapFunctionMock("ldap_unbind")
                ->expects($this->never());
        $link = new LdapLink(null);
        unset($link);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_destruct_UnbindSuccess()
    {
        $this   ->getLdapFunctionMock("ldap_unbind")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn(true);
        $this   ->getLdapFunctionMock("is_resource")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn(true);
        $this   ->getLdapFunctionMock("get_resource_type")
                ->expects($this->once())
                ->with('ldap link')
                ->willReturn('ldap link');
        $link = new LdapLink('ldap link');
        unset($link);
    }
}

// vim: syntax=php sw=4 ts=4 et:
