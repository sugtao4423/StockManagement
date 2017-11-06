<?php
class Utils{

    public static function sqlEscape($str){
        return str_replace("'", "''", $str);
    }

    public static function getSuccessJson(){
        return array('success' => true);
    }

    public static function getErrorJson($message){
        return array('success' => false, 'message' => $message);
    }

}
