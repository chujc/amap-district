<?php

namespace ChuJC\AMapDistrict;

use Exception;
use Illuminate\Support\Carbon;

class District
{
    private $key;

    private $districtURL = 'https://restapi.amap.com/v3/config/district' ;

    private $config;

    private $parameters;

    private $output = 'JSON';

    private $queryURL;

    public function __construct()
    {
        $this->config = config('district');
        if (!$this->config) {
            throw new Exception('未找到配置文件, 请执行 "php artisan vendor:publish --provider="ChuJC\AMapDistrict\DistrictServiceProvider" --tag="config""');
        }

        $this->key = $this->config['key'];
    }


    public function getDistrict(array $params = [])
    {
        $this->validate($params);

        $this->parameters['key'] = $this->key;

        $this->queryURL = $this->districtURL . '?' . http_build_query($this->parameters);

        if ($this->config['cache']) {

            $cacheKey = 'district.'.md5($this->queryURL);

            if (cache()->has($cacheKey)) {

                return cache($cacheKey);
            }

            $response = $this->curl();

            if ($this->output == 'JSON') {

                $responseArray = json_decode($response, true);

            } else {
                $objectXml = simplexml_load_string($response);

                $xmlJson= json_encode($objectXml );

                $responseArray = json_decode($xmlJson,true);
            }
            //异常情况不缓存 直接返回结果
            if ($responseArray['status'] == 0) {

                return json_encode($responseArray);
            }
            $expiresAt = Carbon::now()->addMinutes($this->config['cache_time']);

            cache()->put($cacheKey, $response, $expiresAt);

            return $response;

        }
        return $this->curl();
    }


    private function validate($params)
    {
        //请求参数
        $parameters = array();

        // 参数范围参考 https://lbs.amap.com/api/webservice/guide/api/district

        if (array_key_exists('keywords', $params)) {
            $parameters['keywords'] = $params['keywords'];
        }

        if (array_key_exists('subdistrict', $params)) {

            if ($params['subdistrict'] >= 1 && $params['subdistrict'] <= 4) {

                $parameters['subdistrict'] = intval($params['subdistrict']);
            } else {
                $parameters['subdistrict'] = 1;
            }
        }

        if (array_key_exists('page', $params)) {
            $parameters['page'] = $params['page'] >= 1 ? intval($params['page']) : 1;
        }

        if (array_key_exists('offset', $params)) {
            $parameters['offset'] = $params['offset'] >= 1 ? intval($params['offset']) : 20;
        }

        if (array_key_exists('extensions', $params)) {
            $parameters['extensions'] = $params['extensions'] == 'all' ? 'all' : 'base';
        }

        if (array_key_exists('filter', $params)) {
            $parameters['filter'] = intval($params['filter']);
        }

        if (array_key_exists('callback', $params)) {
            $parameters['callback'] = $params['callback'];
        }

        if (array_key_exists('output', $params)) {
            $parameters['output'] = $params['output'] == 'XML' ? 'XML' : 'JSON';
            $this->output = $parameters['output'];
        }

        $this->parameters = $parameters;
    }

    private function curl()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->queryURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception($err);
        } else {
            return $response;
        }
    }

}
