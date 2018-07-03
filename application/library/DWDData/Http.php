<?php
/**
 * Created by PhpStorm.
 * User: caowei
 * Date: 8/24/15
 * Time: 17:48
 */

class DWDData_Http
{
    private $_responses  = array();

    const  API_SERVER    = 'http://localhost:9501';//'http://cron.wx.jaeapp.com';

    public function __construct()
    {
    }

    protected static function _initPreRequestId($request)
    {
        if (false == isset(HttpServer::$server['request_id'])) {
            return $request;
        }

        if (!isset($request['data'])) {
            $request['data'] = array(
                'preRequestId'  => HttpServer::$server['request_id'],
            );
        } elseif (is_array($request['data'])) {
            $request['data']['preRequestId'] = HttpServer::$server['request_id'];
        }

        return $request;
    }

    public static function getInternalApiServer()
    {
        $config          = Yaf\Registry::get("config");
        return $config->internalapi->hostname;
    }

    public static function callback($data, $delay)
    {
        usleep($delay);
        return $data;
    }

    public static function PackageGetRequest(&$ch, $request, $timeout = 10)
    {
        $request         = self::_initPreRequestId($request);
        $path            =  http_build_query($request['data']);
        $url             =  isset($request['host']) ? $request['host'] : SELF::getInternalApiServer();
        $request['url'] .= '?' . $path;
        curl_setopt($ch, CURLOPT_URL, $url . $request['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
    }

    public static function PackagePostRequest(&$ch, $request, $timeout = 10)
    {
        $request         = self::_initPreRequestId($request);
        $url             =  isset($request['host']) ? $request['host'] : SELF::getInternalApiServer();

        curl_setopt($ch, CURLOPT_URL, $url . $request['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['data']);
    }

    public function processMutliReseout($instance)
    {
        $this->_responses[$instance->id] = $instance->response;
    }

    public function getResponse()
    {
        ksort($this->_responses);
        return $this->_responses;
    }

    public static function Call($request, $retry = 1, $timeout = 10)
    {
        $ch       = curl_init();
        $start    = DWDData_Util::getmicrotime();
        switch ($request['method']) {
            case 'get':
            case 'GET':
                self::PackageGetRequest($ch, $request, $timeout);
                break;
            case 'post':
            case 'POST':
                self::PackagePostRequest($ch, $request, $timeout);
                break;
            default: break;
        }

        $retryTimes           = 0;
        $response             = false;
        while ($retryTimes < $retry) {
            $response         = curl_exec($ch);
            $status           = curl_getinfo($ch);


            $cost             = DWDData_Util::getmicrotime() - $start;
            DWDData_Logger::getInstance()->netrcd($request, $response, $cost);
            curl_close($ch);

            if (intval($status["http_code"]) !=200) {
                $response     = false;
            } else {
                break;
            }

            sleep(pow(2, $retryTimes));
            ++ $retryTimes;
        }

        return $response;
    }

    public static function MutliCall($requests, $delay = 0)
    {
        $queue                   = curl_multi_init();
        $map                     = array();

        foreach ($requests as $reqId => $request) {
            if (false == isset($request['data']) || false == is_array($request['data'])) {
                $request['data'] = array();
            }
            $ch                  = curl_init();
            switch ($request['method']) {
                case 'get':
                case 'GET':
                    self::PackageGetRequest($ch, $request);
                    break;
                case 'post':
                case 'POST':
                    self::PackagePostRequest($ch, $request);
                    break;
                default: break;
            }
            // self::PackageGetRequest( $ch, $request );
            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $request['key'];
        }

        $responses        = array();
        $start            = DWDData_Util::getmicrotime();

        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if ($code != CURLM_OK) {
                break;
            }

            // a request was just completed -- find out which one
            while ($done  = curl_multi_info_read($queue)) {

                // get the info and content returned on the request
                $info     = curl_getinfo($done['handle']);
                $error    = curl_error($done['handle']);
                $results  = curl_multi_getcontent($done['handle']);

                if (empty($error)) {
                    $responses[$map[(string) $done['handle']]] = json_decode($results, true);
                } else {
                    $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results');
                }
                // remove the curl handle that just completed
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }
        } while ($active);

        curl_multi_close($queue);
        $cost             = DWDData_Util::getmicrotime() - $start;
        DWDData_Logger::getInstance()->netrcd($requests, $responses, $cost);
//        ksort( $responses );

        return $responses;
    }

    /*
 * raw格式消息发送
 * */
    public static function rawCurl($url, $data_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
