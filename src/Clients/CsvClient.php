<?php

namespace d2gPmPluginCarPartsFinder\Clients;

use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Log\Loggable;

class CsvClient
{
    use Loggable;

    /**
     * @var object
     */
    protected $cURL;

    public function __construct()
    {
        $this->cURL = curl_init();
    }

    public function call($url, $params = [])
    {

        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
        }
        $url = $url . '?'. $queryString;

        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($this->cURL);
        $httpCode = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);

        $this->getLogger("CsvClient")->error('d2gPmPluginCarPartsFinder::CsvClient.call', [
            $httpCode
        ]);

        if (curl_errno($this->cURL)) {

            $this->getLogger("CsvClient")->error('d2gPmPluginCarPartsFinder::CsvClient.call', [
                curl_error($this->cURL)
            ]);
        }

        curl_close($this->cURL);

        return ['result' => $result, 'http' => $httpCode];
    }

    public function get($url, $params = [])
    {
        return $this->call($url, $params);
    }

}
