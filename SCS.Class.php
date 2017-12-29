<?php
class SCS{

    public function __construct($SCS = true, $https = false, $log = false){
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
        $LogFileData = fgets($LogFile);
        fputs($LogFile, $LogFileData.';'.base64_encode($_SERVER['REMOTE_ADDR']));
        fclose($LogFile);
    }

    public function LOG_OPEN(){
        /*
         * Wait please
         */
    }
}