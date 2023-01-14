<?php
declare(strict_types=1);

require_once(dirname(__FILE__) . '/Config.php');

class Utils{

    public static function getNumFromBool(bool $bool): int{
        return $bool ? 1 : 0;
    }

    public static function getBoolFromString(string $str): bool{
        return ($str === '1' or $str === 'true');
    }

    public static function isOnlySpaces(string $subject): bool{
        if(preg_match('/^(\s|　)+$/', $subject) === 1){
            return true;
        }else{
            return false;
        }
    }

    public static function getSuccessJson(string $type, array $data): array{
        $sort = [];
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

    public static function getErrorJson(string $message): array{
        return [
            'success' => false,
            'message' => $message
        ];
    }

}
