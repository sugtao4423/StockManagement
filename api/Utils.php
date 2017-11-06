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

    public static function getSuccessJson(){
        return array('success' => true);
    }

    public static function getErrorJson($message){
        return array('success' => false, 'message' => $message);
    }

}
