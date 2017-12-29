<?php
class SCS{

    private $SCS_CONFIG;

    public function __construct($SCS = true, $https = false, $log = false, $country = false, $defcode = false, $config = false){
        /*
         * Currently in dev
         */
        if($SCS){

            if(session_status() == PHP_SESSION_ACTIVE){
                session_start();
            }

            if($https){
                $this->HTTPS_REDIRECT();
            }

            if($log){
                $this->LOG_REQUEST();
            }

            if($country){
                $this->CHECKING_COUNTRY();
            }

            if($defcode){
                $this->SYSTEM_DEFCODE();
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

    private function SYSTEM_DEFCODE($DEFCODE = 3){
        /*
         * 1 - CHECKING USER AGENT
         * 2 - SEND ALL REQUEST TO A API
         * 3 - TRACE CLIENT WITH ALL WEBSITE USING [SCS]
         *
         * Default on 3
         *
         * (dev in progress don't touch and don't set to true)
         */
        $DEFCODE_API    = 'https://drm.garryhost.com/stacktrace.php';
        $DEFCODE_CLIENT = array(
            'IP'            => $this->LOOKUP_PROXY(),
            'USER_AGENT'    => $_SERVER['HTTP_USER_AGENT'],
            'SERVER' => array(
                'HOSTNAME' => $_SERVER['SERVER_NAME'],
                'IP'       => $_SERVER['SERVER_ADDR'],
                'SELF'     => $_SERVER['PHP_SELF']
            ),
        );

        if($DEFCODE == 1){
            if(strpos(strtolower($DEFCODE_CLIENT['USER_AGENT']), 'bot') !== false){
                die('<center><b>[SCS] - Country Blocked</b></center>');
            }
        } elseif($DEFCODE == 2) {
            /*
             * HERE ALL THE DATA ARE SEND TO A API
             */
            $DEFCODE_PREPARE_DATA = urlencode(base64_encode(serialize($DEFCODE_CLIENT)));
        } elseif($DEFCODE == 3) {
            /*
             * HERE ALL THE DATA ARE SEND TO A API
             *
             */
            $DEFCODE_PREPARE_DATA = urlencode(base64_encode(serialize($DEFCODE_CLIENT)));
        }
    }

    private function LOOKUP_PROXY(){
        if(isset($_SERVER['HTTP_CLIENT_IP'])){
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function CHECKING_COUNTRY(){
        /*
         * ADJUST IN THE XML CONFIG FILE AND I USE FREEGEOIP THANKS ALOT
         */
        if(file_exists('config.xml')){
            $xml_freegeoip  = simplexml_load_string(file_get_contents('https://freegeoip.net/xml/'.$_SERVER['REMOTE_ADDR']));
            $xml_config     = simplexml_load_file('config.xml');

            $extract = explode(',', $xml_config->block_country);

            if(in_array($xml_freegeoip->CountryCode, $extract)){
               die('<center><b>[SCS] - Country Blocked</b></center>');
            }
        } else {
            $this->utils_json_reply(array('error' => 'SCS CONFIG FILE YAML'));
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

    private function utils_json_reply($arr){
        echo json_encode($arr);
    }
}