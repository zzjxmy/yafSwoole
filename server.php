<?php
if (false == defined("APPLICATION_PATH")) {
    define('APPLICATION_PATH', __DIR__);
}

class HttpServer
{
    public static $instance;

    public $http;

    public static $get;

    public static $post;

    public static $header;

    public static $server;

    public static $files;

    public static $start;

    public function __construct()
    {
        HttpServer::$server                   = DWDData_Util::getServerParams();
        HttpServer::$server['request_id']     = DWDData_Util::getLogid();
        HttpServer::$server['request_method'] = $_SERVER['REQUEST_METHOD'];
        HttpServer::$get                      = $_GET;
        HttpServer::$post                     = $_POST;
        HttpServer::$files                    = $_FILES;
        HttpServer::$start                    = DWDData_Util::getmicrotime();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new HttpServer;
        }
        return self::$instance;
    }
}

HttpServer::getInstance();
