<?php
/**
 * @file src/Korowai/Lib/Context/ResourceContextManager.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\ContextLib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context;

/**
 * A context manager that wraps a PHP resource.
 */
class ResourceContextManager implements ContextManagerInterface
{
    const DEFAULT_RESOURCE_DESTRUCTORS = [
        'bzip2' => '\\bzclose',
        'cubrid connection' => '\\cubrid_close',
        'persistent cubrid connection' => null,
        'cubrid request' => '\\cubrid_close_request',
        'cubrid lob' => '\\cubrid_lob_close',
        'cubrid lob2' => '\\cubrid_lob2_close',
        'curl' => '\\curl_close',
        'dba' => '\\dba_close',
        'dba persistent' => null,
        'dbase' => '\\dbase_close',
        'dbx_link_object' => '\\dbx_close',
        'dbx_result_object' => null,
        'xpath context' => null,
        'xpath object' => null,
        'fbsql link' => '\\fbsql_close',
        'fbsql plink' => null,
        'fbsql result' => '\\fbsql_free_result',
        'fdf' => '\\fdf_close',
        'ftp' => '\\ftp_close',
        'gd' => '\\imagedestroy',
        'gd font' => null,
        'gd PS encoding' => null,
        'gd PS font' => '\\imagepsfreefont',
        'GMP integer' => null,
        'imap' => '\\imap_close',
        'ingres' => '\\ingres_close',
        'ingres persistent' => null,
        'interbase blob' => null,
        'interbase link' => '\\ibase_close',
        'interbase link persistent' => null,
        'interbase query' => '\\ibase_free_query',
        'interbase result' => '\\ibase_free_result',
        'interbase transaction' => '\\ibase_free_transaction',
        'ldap link' => '\\ldap_close',
        'ldap result' => '\\ldap_free_result',
        'ldap result entry' => null,
        'SWFAction' => null,
        'SWFBitmap' => null,
        'SWFButton' => null,
        'SWFDisplayItem' => null,
        'SWFFill' => null,
        'SWFFont' => null,
        'SWFGradient' => null,
        'SWFMorph' => null,
        'SWFMovie' => null,
        'SWFShape' => null,
        'SWFSprite' => null,
        'SWFText' => null,
        'SWFTextField' => null,
        'mnogosearch agent' => null,
        'mnogosearch result' => null,
        'msql link' => '\\msql_close',
        'msql link persistent' => null,
        'msql query' => '\\msql_free_result',
        'mssql link' =>  '\\mssql_close',
        'mssql link persistent' => null,
        'mssql result' => '\\mssql_free_result',
        'mysql link' => '\\mysql_close',
        'mysql link persistent' => null,
        'mysql result' => '\\mysql_free_result',
        'oci8 collection' => '->free',
        'oci8 connection' => '\\oci_close',
        'oci8 lob' => '->free',
        'oci8 statement' => '\\oci_free_statement',
        'odbc link' => '\\odbc_close',
        'odbc link persistent' => null,
        'odbc result' => '\\odbc_free_result',
        'birdstep link' => null,
        'birdstep result' => null,
        'OpenSSL key' => '\\openssl_free_key',
        'OpenSSL X.509' => '\\openssl_x509_free',
        'pdf document' => '\\pdf_delete',
        'pdf image' => '\\pdf_close_image',
        'pdf object' => null,
        'pdf outline' => null,
        'pgsql large object' => '\\pg_lo_close',
        'pgsql link' => '\\pg_close',
        'pgsql link persistent' => null,
        'pgsql result' => '\\pg_free_result',
        'pgsql string' => null,
        'printer' => null,
        'printer brush' => null,
        'printer font' => null,
        'printer pen' => null,
        'pspell' => null,
        'pspell config' => null,
        'shmop' => '\\shmop_close',
        'sockets file descriptor set' => '\\close',
        'sockets i/o vector' => null,
        //'stream' => ['dir' => '\\closedir', 'STDIO' => '\fclose'],
        //'stream' => '\\pclose',
        'socket' => '\\fclose',
        'sybase-db link' => '\\sybase_close',
        'sybase-db link persistent' => null,
        'sybase-db result' => '\\sybase_free_result',
        'sybase-ct link' => '\\sybase_close',
        'sybase-ct link persistent' => null,
        'sybase-ct result' => '\\sybase_free_result',
        'sysvsem' => '\\sem_release',
        'sysvshm' => '\\shm_detach',
        'wddx' => '\\wddx_packet_end',
        'xml' => '\\xml_parser_free',
        'zlib' => '\\gzclose',
        'zlib.deflate' => null,
        'zlib.inflate' => null
    ];
    /**
     * @var resource
     */
    protected $resource;

    /**
     * Constructs the context manager.
     *
     * @param resource $resource The resource to be wrapped;
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Returns the resource wrapped by this context manager.
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function enterContext()
    {
        return $this->getResource();
    }

    /**
     * {@inheritdoc}
     */
    public function exitContext(?\Throwable $exception = null) : bool
    {
        $resource = $this->getResource();
        if (is_resource($resource)) {
            $this->destroyResource($resource);
        }
        return false;
    }

    protected function destroyResource($resource)
    {
        $dtor = $this->getResourceDestructor($resource);
        if ($dtor === null) {
            return null;
        }
        return call_user_func($dtor, $resource);
    }

    public function getResourceDestructor($resource)
    {
        $type = get_resource_type($resource);
        $func = self::DEFAULT_RESOURCE_DESTRUCTORS[$type] ?? null;
        if (is_string($func) && substr($func, 0, 2) === '->') {
            $method = substr($func, 2);
            return $this->mkObjectResourceDestructor($resource, $method);
        } elseif ($type === 'stream' && is_null($func)) {
            return $this->getStreamResourceDestructor($resource);
        } else {
            return $func;
        }
    }

    protected function mkObjectResourceDestructor($resource, string $method)
    {
        if (PHP_VERSION_ID >= 70200) {
            return function (object $resource) use ($method) {
                return call_user_func(array($resource, $method));
            };
        } else {
            return function ($resource) use ($method) {
                return call_user_func(array($resource, $method));
            };
        }
    }

    protected function getStreamResourceDestructor($resource)
    {
        $meta = stream_get_meta_data($resource);
        if ($meta['stream_type'] === 'dir') {
            return '\\closedir';
        } else {
            return '\\fclose';
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
