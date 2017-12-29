<?php
class SCS{

    private $SCS_CONFIG;

    public function __construct($SCS = true, $https = false, $log = false, $config = false){
        /*
         * Currently in dev
         */
        if($SCS){

            if($https){
                $this->HTTPS_REDIRECT();
            }

            if($log){
                $this->LOG_REQUEST();
            }

            if($config != false && is_array($config)){
                $this->SCS_CONFIG = $config;
            }

        }
    }

    private function HTTPS_REDIRECT(){
        if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
            header('Location: https://'.$_SERVER['SERVER_NAME']);
        }
    }

    private function LOG_REQUEST(){
        /*
         * Wait please
         * (Change permission for the txt)
         */
        $LogFile = fopen('log/request_log.txt', 'r+');
        fgets($LogFile);
        fputs($LogFile, ';'.base64_encode($_SERVER['REMOTE_ADDR']));
        fclose($LogFile);
    }

    public function LOG_OPEN($listing_json = false){
        /*
         * Wait please
         */
        $LogFile = fopen('log/request_log.txt', 'r+');
        $LogFileData = fgets($LogFile);

        $extract = explode(';', $LogFileData);

        if(!$listing_json){
            echo 'TOTAL IP : '.count($extract).'<br><br>';

            foreach($extract as $value){
                if(!empty($value)){
                    echo base64_decode($value).'<br>';
                }
            }
        } else {
            $json = [];

            foreach($extract as $value){
                if(!empty($value)){
                    array_push($json, base64_decode($value));
                }
            }

            echo json_encode($json);
        }

        fclose($LogFile);
    }
}