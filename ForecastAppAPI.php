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
    private $curl;
    private $token;

    public function __construct($login, $password, $accountId)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, '/dev/null'); //Saves cookies in memory


        $this->token = $this->getToken($login, $password, $accountId);
    }

    public function assignments($startDate, $endDate, $active)
    {

    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    private function getToken($login, $password, $accountId)
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

    private function GETRequest($url, $followRedirects = true)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 0);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $followRedirects);

        $output = curl_exec($this->curl);
        return $output;
    }

    private function POSTRequest($url, $data, $followRedirects = true)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $followRedirects);

        $output = curl_exec($this->curl);
        return $output;
    }
}