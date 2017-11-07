<?php
class Utils{

    public static function sqlEscape($str){
        return str_replace("'", "''", $str);
    }

    public static function getNumFromBool($bool){
        return ($bool == true or $bool > 0) ? 1 : 0;
    }

    public static function getBoolFromNum($num){
        return ($num == true or $num > 0);
    }

    public static function getSuccessJson($dataName = null, $data = null){
        if($dataName === null or $data === null){
            return array('success' => true);
        }else{
            return array('success' => true, $dataName => $data);
        }
    }

    public static function getErrorJson($message){
        return array('success' => false, 'message' => $message);
    }

}
