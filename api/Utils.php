<?php
require_once(dirname(__FILE__) . '/Config.php');

class Utils{

    public static function getNumFromBool($bool){
        if($bool === true or $bool === 'true'){
            return 1;
        }else{
            if(is_int($bool) and $bool > 0){
                return 1;
            }
        }
        return 0;
    }

    public static function getBoolFromNum($num){
        return ($num == true or $num > 0);
    }

    public static function isOnlySpaces($subject){
        if(preg_match('/^(\s|　)+$/', $subject) === 1){
            return true;
        }else{
            return false;
        }
    }

    public static function getSuccessJson($dataName = null, $data = null){
        if($dataName === null or $data === null){
            return ['success' => true];
        }else{
            return ['success' => true, $dataName => $data];
        }
    }

    public static function getErrorJson($message){
        return ['success' => false, 'message' => $message];
    }

}
