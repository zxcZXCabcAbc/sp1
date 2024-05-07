<?php

namespace app\libs\HttpSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use think\facade\Log;

class HttpService
{
    protected $baseUrl;
    protected $method = 'GET';
    protected $path = '';
    protected $options = [];
    protected $channel = 'file';
    protected $header = [];
    protected $debug;
    protected array $auth = [];

    public function getAuth(): array
    {
        return $this->auth;
    }

    public function setAuth(array $auth): HttpService
    {
        $this->auth = $auth;
        return $this;
    }
    public function setMethod(string $method):HttpService
    {
        $this->method = $method;
        return $this;
    }

    protected function getMethod(){
        return strtoupper($this->method);
    }

    public function setPath(string $path):HttpService
    {
        $this->path = $path;
        return $this;
    }

    protected function getPath(){
        return $this->path;
    }

    public function setOption($option) :HttpService
    {
        if($this->getAuth()) $option['auth'] = $this->getAuth();
        $this->options = $option;
        return $this;
    }

    protected function getOption(){
        return $this->options;
    }

    public function setDebug($debug): HttpService
    {
        $this->debug = $debug;
        return $this;
    }

    protected function getDebug(){
        return $this->debug;
    }


    public function setBaseUrl($baseUrl): HttpService
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    protected function getBaseUrl(){
        return $this->baseUrl;
    }

    public function setChannel($channel):HttpService
    {
        $this->channel = $channel;
        return $this;
    }

    protected function getChannel(){
        return $this->channel;
    }

    public function setHeader($header):HttpService
    {
        $this->header = $header;
        return $this;
    }

    protected function getHeader(){
        return $this->header;
    }

    /**
     * 发起HTTP请求
     * @param $url
     * @param string $method
     * @param array $options
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public  function send()
    {
        $header = ['base_uri'=>$this->getBaseUrl(),];
        if($this->getHeader()) $header['headers'] = $this->getHeader();
        $client = new Client($header);
        $log = [
            'url'=>$this->getBaseUrl() . $this->getPath(),
            'method' =>$this->getMethod(),
            'params' => $this->getOption(),
            'response'=>[],
            'error' =>[],
            'headers'=>$header
        ];
        $channel = $this->getChannel();
        try {
            $request = $client->request($this->getMethod(), $this->getPath(), $this->getOption());
            $contents = $request->getBody()->getContents();
            $response = json_decode($contents, true);
            if(isset($response['message']) && !empty($response['message']) && is_null($response['datas'])){
                throw new \Exception($response['message'],$response['code']);
            }
            $log['response'] = $response;
            tplog($this->getPath(),$log,$channel);
            return $response;
        } catch (ClientException $e) {
            dd($e);
            $log['error']['code'] = $e->getCode();
            $log['error']['message'] = $e->getMessage();
             tplog($this->getPath(),$log,$channel);
            throw new \Exception($e->getMessage(),$e->getCode());
        }

    }
}