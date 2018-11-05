<?php

class DynamicHub_Client
{
    const HTTP_CODE_SUCCESS = 200;
    const CURL_TIMEOUT_SECS = 30;
    const CLIENT_ID = '';
    const TOKEN_REQUEST_ENDPOINT = '';
    const REQUEST_ENDPOINT = '';
    const CLIENT_SECRET = '';
        
    public function pushEntities($data, $httpMethod="POST")
    {
        $accessToken = $this->_getAccessToken();
        $result = $this->_transferEntity($accessToken, $data, $httpMethod);
        return $result;
    }

    protected function _getAccessToken()
    {
        $clientId = self::CLIENT_ID;
        $clientSecret = self::CLIENT_SECRET;
        $tokenRequestArgs = "grant_type=client_credentials&client_id={$clientId}&client_secret={$clientSecret}&scope=generic-entity-scope";
        
        $accessToken = null;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::TOKEN_REQUEST_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_TIMEOUT_SECS); 
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT_SECS); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenRequestArgs);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Force TLS1.2
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);

        $response = curl_exec($ch);
        
        if(!curl_errno($ch)) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            $result = json_decode($response, true);
            
            if(isset($result['access_token']) && $httpCode == self::HTTP_CODE_SUCCESS) {   
                $accessToken = $result['access_token'];
            } else {
                curl_close($ch);
                $errorMessage = isset($result['error_description']) ? $result['error_description'] : 'not given';
                throw new Exception("Dynamic Hub access token request failed. HTTP status was {$errorMessage} and error was {$errorMessage}");
            }
        } else {
            $errorReason = curl_error($ch);
            curl_close($ch);
            throw new Exception("Dynamic Hub access token curl request failed. Reason: {$errorReason}");
        }

        curl_close($ch);

        return $accessToken;
    }

    protected function _transferEntity($accessToken, $data, $httpMethod)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::REQUEST_ENDPOINT);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '.$accessToken,
            'Expect:'
        ));

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_TIMEOUT_SECS); 
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT_SECS); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Force TLS1.2
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        
        $response = curl_exec($ch);

        if(!curl_errno($ch)) {            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = json_decode($response, true);
            
            if((!isset($result['status'])) || ($result['status'] != 'success') || ($httpCode != self::HTTP_CODE_SUCCESS)) {
                curl_close($ch);
                $errorMessage = isset($result['error_description']) ? $result['error_description'] : 'not given';
                throw new Exception("Dynamic Hub entity send request failed. HTTP code was {$httpCode} and error was {$errorMessage}");
            }
        } else {
            $errorReason = curl_error($ch);
            curl_close($ch);
            throw new Exception("Dynamic Hub entity send request failed. Reason: {$errorReason}");
        }
        
        curl_close($ch);

        return $result['entity_results'];
    }
}

