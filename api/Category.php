<?php
require_once(dirname(__FILE__) . '/Utils.php');
require_once(dirname(__FILE__) . '/Config.php');

class Category{

    public function createCategory($catName = null){
        if($catName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        if(strpos($catName, '/') !== false){
            return Utils::getErrorJson('error. can not use \'/\' in category name.');
        }
        if(Utils::isOnlySpaces($catName)){
            return Utils::getErrorJson('error. can not create category of only space string.');
        }
        if(touch(DB_DIR . $catName . '.sqlite3')){
            return $this->getCategories();
        }else{
            return Utils::getErrorJson('touch error. could not create category.');
        }
    }

    public function getCategories(){
        $result = Utils::getSuccessJson('categories', array());
        foreach(scandir(DB_DIR) as $f){
            if(preg_match('/(.*)\.sqlite3/', $f, $m) === 1){
                $groupDB = new SQLite3(Utils::getDBpath($m[1]));
                $itemCount = $groupDB->querySingle("SELECT COUNT(*) FROM sqlite_master WHERE type='table'");
                array_push($result['categories'], array('name' => $m[1], 'itemCount' => $itemCount));
            }
        }
        return $result;
    }

    public function deleteCategory($catName){
        if($catName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        if(unlink(Utils::getDBpath($catName))){
            return $this->getCategories();
        }else{
            return Utils::getErrorJson('error. could not delete database file.');
        }
    }

}
