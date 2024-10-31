<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JSONApiClient {
    private static $SERVICE_URL = 'http://suites.rollupmedia.com/wsp-web/api/articles/';
    
    private $SERVICE_ACCESS_KEY='';
    private $SERVICE_WEB_PROPERTY_ID ='';
    private $SERVICE_SECRET_KEY = '';
    private $ch = null;
    private $response = null;
    private $error_message = null;
    
    function JSONApiClient($accesskey,$webproperty,$secretkey){
        $this->SERVICE_ACCESS_KEY = $accesskey;
        $this->SERVICE_SECRET_KEY = $secretkey;
        $this->SERVICE_WEB_PROPERTY_ID = $webproperty;
    }
    
    private function getArticlesUri() {
        return JSONApiClient::$SERVICE_URL . $this->SERVICE_ACCESS_KEY . '.json';
    }

    private function getCheckSum() {
        return sha1($this->SERVICE_ACCESS_KEY . $this->SERVICE_WEB_PROPERTY_ID . $this->SERVICE_SECRET_KEY);
    }

    public function getArticles($rowsPerPage = 20) {
        $requestParams = array(
            'checkSum' => $this->getCheckSum(),
            'webPropertyId' => $this->SERVICE_WEB_PROPERTY_ID,
            'rowsPerPage' => $rowsPerPage
        );
        $this->ch = curl_init();
        $requestURI = $this->getArticlesUri() . '?' . http_build_query($requestParams,'','&');
        error_log($requestURI);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $requestURI,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_TIMEOUT => 30
        );
        curl_setopt_array( $this->ch, $options );
        $this->response =  curl_exec($this->ch); // Getting jSON result string
        return $this->getResponse();
    }

    public function updateArticles($articleIds=array()) {
        $this->ch = curl_init();
        $requestParams = array(
            'checkSum' => $this->getCheckSum(),
            'webPropertyId' => $this->SERVICE_WEB_PROPERTY_ID
        );

        $paramsString = http_build_query($requestParams);
        foreach($articleIds as $articleId) {
            $paramsString .= "&articleIds={$articleId}";
        }

        $requestURI = $this->getArticlesUri() . '?' . $paramsString;
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $requestURI,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => http_build_query($requestParams),
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_TIMEOUT => 30
        );
        curl_setopt_array( $this->ch, $options );
        $this->response =  curl_exec($this->ch); // Getting jSON result string
        return $this->getResponse();
    }

    private function responseStatus() {
        $responseInfo = curl_getinfo($this->ch);
        curl_close($this->ch);
        return isset($responseInfo['http_code']) ? $responseInfo['http_code'] : 0;
    }

    private function getResponse() {
        $status = $this->responseStatus();
        if($status == 200) {
            return json_decode($this->response);
        } else if($status == 403) {
            $this->error_message = $this->response;
            return "ACCESS_DENIED";
        } else if($status == 404) {
            $this->error_message = $this->response;
            return  "UNKNOWN_PUBLISHER";
        } else if($status == 400) {
            $this->error_message = $this->response;
            return  "MISSING_PARAMETERS";
        } else {
            $this->error_message = $this->response;
            return "UNKNOWN_ERROR";
        }
    }
    
    function getErrorMessage(){
        return $this->error_message;
    }

}
?>
