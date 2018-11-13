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

    public static function getSuccessJson($type = null, $data = null){
        if($type === null or $data === null){
            return ['success' => true];
        }else{
            foreach($data as $d){
                $sort[] = $d['name'];
            }
            array_multisort($sort, SORT_ASC, SORT_NATURAL, $data);
            return [
                'success' => true,
                'type' => $type,
                'data' => $data
            ];
        }
    }

    public static function getErrorJson($message){
        return [
            'success' => false,
            'message' => $message
        ];
    }

}
