<?php
/**
 * @file src/Korowai/Lib/Context/Tests/ResourceContextManagerTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldif
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context\Tests;

use PHPUnit\Framework\TestCase;

use Korowai\Lib\Context\ResourceContextManager;
use Korowai\Lib\Context\ContextManagerInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResourceContextManagerTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    /**
     * @runInSeparateProcess
     */
    public function prepareForGetResourceDestructor($resource, $type)
    {
        $this->getFunctionMock('Korowai\\Lib\\Context', 'get_resource_type')
             ->expects($this->once())
             ->with($resource)
             ->willReturn($type);
        return new ResourceContextManager($resource);
    }

    public function test__implements__ContextManagerInterface()
    {
        $interfaces = class_implements(ResourceContextManager::class);
        $this->assertContains(ContextManagerInterface::class, $interfaces);
    }

    public function test__construct()
    {
        $arg = ['foo'];
        $cm = new ResourceContextManager($arg);
        $this->assertSame($arg, $cm->getResource());
    }

    public function test__enterContext()
    {
        $arg = ['foo'];
        $cm = new ResourceContextManager($arg);
        $this->assertSame($arg, $cm->enterContext());
    }

    public function test__exitContext__withNonResource()
    {
        $arg = ['foo'];

        $cm = $this->getMockBuilder(ResourceContextManager::class)
                   ->disableOriginalConstructor()
                   ->setMethods(['destroyResource', 'getResource'])
                   ->getMock();

        $cm->expects($this->once())
           ->method('getResource')
           ->willReturn($arg);

        $cm->expects($this->never())
           ->method('destroyResource');

        $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource')
             ->expects($this->once())
             ->with($arg)
             ->willReturn(false);

        $this->assertFalse($cm->exitContext(null));
    }

    /**
     * @runInSeparateProcess
     */
    public function test__exitContext__withResource()
    {
        $arg = ['foo'];

        $cm = $this->getMockBuilder(ResourceContextManager::class)
                   ->disableOriginalConstructor()
                   ->setMethods(['destroyResource', 'getResource'])
                   ->getMock();

        $cm->expects($this->once())
           ->method('getResource')
           ->willReturn($arg);

        $cm->expects($this->once())
           ->method('destroyResource')
           ->with($arg);

        $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource')
             ->expects($this->once())
             ->with($arg)
             ->willReturn(true);

        $this->assertFalse($cm->exitContext(null));
    }

    /**
     * @runInSeprarateProcess
     */
    public function test__exitContext__withResource_and_nullResourceDtor()
    {
        $arg = ['foo'];


        $cm = $this->getMockBuilder(ResourceContextManager::class)
                   ->disableOriginalConstructor()
                   ->setMethods(['getResourceDestructor', 'getResource'])
                   ->getMock();

        $cm->expects($this->once())
           ->method('getResource')
           ->willReturn($arg);

        $cm->expects($this->once())
           ->method('getResourceDestructor')
           ->with($arg)
           ->willReturn(null);

        $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource')
             ->expects($this->once())
             ->with($arg)
             ->willReturn(true);

        $this->assertFalse($cm->exitContext(null));
    }

    /**
     * @runInSeprarateProcess
     */
    public function test__exitContext__withResource_and_NonNullResourceDtor()
    {
        $arg = ['foo'];
        $deleted = null;

        $cm = $this->getMockBuilder(ResourceContextManager::class)
                   ->disableOriginalConstructor()
                   ->setMethods(['getResourceDestructor', 'getResource'])
                   ->getMock();

        $cm->expects($this->once())
           ->method('getResource')
           ->willReturn($arg);

        $cm->expects($this->once())
           ->method('getResourceDestructor')
           ->with($arg)
           ->willReturn(function ($res) use (&$deleted) { $deleted = $res; });

        $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource')
             ->expects($this->once())
             ->with($arg)
             ->willReturn(true);

        $this->assertFalse($cm->exitContext(null));
        $this->assertSame($deleted, $arg);
    }

    public function test__getResourceDestructor__bzip2()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'bzip2');
        $this->assertEquals('\\bzclose', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__cubrid_connection()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'cubrid connection');
        $this->assertEquals('\\cubrid_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__persistent_cubrid_connection()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'persistent cubrid connection');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__cubrid_request()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'cubrid request');
        $this->assertEquals('\\cubrid_close_request', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__cubrid_lob()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'cubrid lob');
        $this->assertEquals('\\cubrid_lob_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__cubrid_lob2()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'cubrid lob2');
        $this->assertEquals('\\cubrid_lob2_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__curl()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'curl');
        $this->assertEquals('\\curl_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__dba()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'dba');
        $this->assertEquals('\\dba_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__dba_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'dba persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__dbase()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'dbase');
        $this->assertEquals('\\dbase_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__dbx_link_object()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'dbx_link_object');
        $this->assertEquals('\\dbx_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__dbx_result_object()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'dbx_result_object');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__xpath_context()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'xpath context');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__xpath_object()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'xpath object');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__fbsql_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'fbsql link');
        $this->assertEquals('\\fbsql_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__fbsql_plink()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'fbsql plink');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__fbsql_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'fbsql result');
        $this->assertEquals('\\fbsql_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__fdf()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'fdf');
        $this->assertEquals('\\fdf_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__ftp()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'ftp');
        $this->assertEquals('\\ftp_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__gd()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'gd');
        $this->assertEquals('\\imagedestroy', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__gd_font()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'gd font');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__gd_PS_encoding()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'gd PS encoding');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__gd_PS_font()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'gd PS font');
        $this->assertEquals('\\imagepsfreefont', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__GMP_integer()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'GMP integer');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__imap()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'imap');
        $this->assertEquals('\\imap_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__ingres()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'ingres');
        $this->assertEquals('\\ingres_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__ingres_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'ingres persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__interbase_blob()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'interbase blob');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__interbase_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'interbase link');
        $this->assertEquals('\\ibase_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__interbase_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'interbase link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__interbase_query()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'interbase query');
        $this->assertEquals('\\ibase_free_query', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__interbase_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'interbase result');
        $this->assertEquals('\\ibase_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__interbase_transaction()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'interbase transaction');
        $this->assertEquals('\\ibase_free_transaction', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__ldap_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'ldap link');
        $this->assertEquals('\\ldap_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__ldap_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'ldap result');
        $this->assertEquals('\\ldap_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__ldap_result_entry()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'ldap result entry');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFAction()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFAction');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFBitmap()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFBitmap');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFButton()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFButton');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFDisplayItem()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFDisplayItem');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFFill()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFFill');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFFont()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFFont');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFGradient()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFGradient');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFMorph()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFMorph');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFMovie()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFMovie');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFShape()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFShape');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFSprite()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFSprite');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFText()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFText');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__SWFTextField()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'SWFTextField');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mnogosearch_agent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mnogosearch agent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mnogosearch_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mnogosearch result');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__msql_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'msql link');
        $this->assertEquals('\\msql_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__msql_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'msql link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__msql_query()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'msql query');
        $this->assertEquals('\\msql_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mssql_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mssql link');
        $this->assertEquals('\\mssql_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mssql_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mssql link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mssql_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mssql result');
        $this->assertEquals('\\mssql_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mysql_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mysql link');
        $this->assertEquals('\\mysql_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mysql_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mysql link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__mysql_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'mysql result');
        $this->assertEquals('\\mysql_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__oci8_collection()
    {
        $res = new class {
            public $destroyed = false;
            public function free() { $this->destroyed = true; }
        };

        $cm = $this->prepareForGetResourceDestructor($res, 'oci8 collection');
        $dtor = $cm->getResourceDestructor($res);

        $this->assertIsCallable($dtor);

        call_user_func($dtor, $res);

        $this->assertTrue($res->destroyed);
    }

    public function test__getResourceDestructor__oci8_connection()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'oci8 connection');
        $this->assertEquals('\\oci_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__oci8_lob()
    {
        $res = new class {
            public $destroyed = false;
            public function free() { $this->destroyed = true; }
        };

        $cm = $this->prepareForGetResourceDestructor($res, 'oci8 lob');
        $dtor = $cm->getResourceDestructor($res);

        $this->assertIsCallable($dtor);

        call_user_func($dtor, $res);

        $this->assertTrue($res->destroyed);
    }

    public function test__getResourceDestructor__oci8_statement()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'oci8 statement');
        $this->assertEquals('\\oci_free_statement', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__odbc_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'odbc link');
        $this->assertEquals('\\odbc_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__odbc_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'odbc link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__odbc_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'odbc result');
        $this->assertEquals('\\odbc_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__birdstep_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'birdstep link');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__birdstep_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'birdstep result');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__OpenSSL_key()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'OpenSSL key');
        $this->assertEquals('\\openssl_free_key', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__OpenSSL_X_509()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'OpenSSL X.509');
        $this->assertEquals('\\openssl_x509_free', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pdf_document()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pdf document');
        $this->assertEquals('\\pdf_delete', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pdf_image()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pdf image');
        $this->assertEquals('\\pdf_close_image', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pdf_object()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pdf object');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pdf_outline()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pdf outline');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pgsql_large_object()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pgsql large object');
        $this->assertEquals('\\pg_lo_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pgsql_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pgsql link');
        $this->assertEquals('\\pg_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pgsql_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pgsql link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pgsql_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pgsql result');
        $this->assertEquals('\\pg_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pgsql_string()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pgsql string');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__printer()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'printer');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__printer_brush()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'printer brush');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__printer_font()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'printer font');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__printer_pen()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'printer pen');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pspell()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pspell');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__pspell_config()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'pspell config');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__shmop()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'shmop');
        $this->assertEquals('\\shmop_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sockets_file_descriptor_set()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sockets file descriptor set');
        $this->assertEquals('\\close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sockets_i_o_vector()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sockets i/o vector');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    /**
     * @runInSeprarateProcess
     */
    public function test__getResourceDestructor__dir_stream()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'stream');
        $this->getFunctionMock('Korowai\\Lib\\Context', 'stream_get_meta_data')
             ->expects($this->once())
             ->with('foo')
             ->willReturn(['stream_type' => 'dir']);
        $this->assertEquals('\\closedir', $cm->getResourceDestructor('foo'));
    }

    /**
     * @runInSeparateProcess
     */
    public function test__getResourceDestructor__stream()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'stream');
        $this->getFunctionMock('Korowai\\Lib\\Context', 'stream_get_meta_data')
             ->expects($this->once())
             ->with('foo')
             ->willReturn(['stream_type' => 'geez']);
        $this->assertEquals('\\fclose', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__socket()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'socket');
        $this->assertEquals('\\fclose', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sybase_db_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sybase-db link');
        $this->assertEquals('\\sybase_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sybase_db_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sybase-db link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sybase_db_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sybase-db result');
        $this->assertEquals('\\sybase_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sybase_ct_link()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sybase-ct link');
        $this->assertEquals('\\sybase_close', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sybase_ct_link_persistent()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sybase-ct link persistent');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sybase_ct_result()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sybase-ct result');
        $this->assertEquals('\\sybase_free_result', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sysvsem()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sysvsem');
        $this->assertEquals('\\sem_release', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__sysvshm()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'sysvshm');
        $this->assertEquals('\\shm_detach', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__wddx()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'wddx');
        $this->assertEquals('\\wddx_packet_end', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__xml()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'xml');
        $this->assertEquals('\\xml_parser_free', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__zlib()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'zlib');
        $this->assertEquals('\\gzclose', $cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__zlib_deflate()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'zlib.deflate');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }

    public function test__getResourceDestructor__zlib_inflate()
    {
        $cm = $this->prepareForGetResourceDestructor('foo', 'zlib.inflate');
        $this->assertNull($cm->getResourceDestructor('foo'));
    }
}

// vim: syntax=php sw=4 ts=4 et:
