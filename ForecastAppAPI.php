<?php
/**
 * Created by PhpStorm.
 * User: solohin
 * Date: 28/10/16
 * Time: 23:01
 */

namespace pro\data5;

use \Exception;

class ForecastAppAPI
{
    const BASE_PATH = 'https://api.forecastapp.com/';

    private $curl;
    private $token = null;
    private $accountID;

    public function __construct($login, $password, $accountId, $oldToken = null)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, '/dev/null'); //Saves cookies in memory

        if ($oldToken === null) {
            $this->token = $this->requestToken($login, $password, $accountId);
        } else {
            $this->token = $oldToken;
        }

        $this->accountID = $accountId;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function people()
    {
        $data = $this->APIGet('people')['people'];
        $result = [];
        foreach ($data as $item) {
            $item['updated_at'] = (new \DateTime($item['updated_at']))->format('d.m.Y');
            $result[] = $item;
        }
        return $result;
    }

    private function APIGet($path, $params = [])
    {
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Forecast-Account-ID: ' . $this->accountID,
        ];

        $url = self::BASE_PATH . $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $data = json_decode($this->GETRequest($url, false, $headers), 1);
        return $data;
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    private function requestToken($login, $password, $accountId)
    {
        //Get token for form
        $loginForm = $this->GETRequest("https://id.getharvest.com/forecast/sign_in");
        $matches = [];
        $regexp = '/<input type="hidden" name="authenticity_token" value="(.*)"/';
        preg_match_all($regexp, $loginForm, $matches);
        $CSRFToken = $matches[1][0];

        if (empty($CSRFToken)) {
            throw new Exception('CSRF token error - no token');
        }

        $data = [
            'authenticity_token' => $CSRFToken,
            'email' => $login,
            'password' => $password,
            'product' => 'forecast'
        ];

        $this->POSTRequest('https://id.getharvest.com/sessions', $data);
        $tokenRequest = $this->GETRequest('https://id.getharvest.com/accounts/' . $accountId, false);

        $matches = [];
        $regexp = '/access_token\/(.*)\?"/';
        preg_match_all($regexp, $tokenRequest, $matches);

        if (isset($matches[1][0]) && !empty($matches[1][0])) {
            return $matches[1][0];
        } else {
            throw new Exception('Could not find token. Check your login, password and APP ID');
        }
    }

    private function GETRequest($url, $followRedirects = true, $headers = [])
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 0);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $followRedirects);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($this->curl);
        return $output;
    }

    private function POSTRequest($url, $data, $followRedirects = true, $headers = [])
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $followRedirects);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($this->curl);
        return $output;
    }
}