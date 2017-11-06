<?php
require_once('./Utils.php');

class StockGroup{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createStockGroup($groupName = null){
        if($groupName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        if($this->db->exec("CREATE TABLE '${groupName}' (id INTEGER PRIMARY KEY, name TEXT, 'exists' INTEGER)")){
            return Utils::getSuccessJson();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not create table.');
        }
    }

    public function getStockGroups(){
        $tablesquery = $this->db->query("SELECT name FROM sqlite_master WHERE type='table'");
        if(!$tablesquery){
            return Utils::getErrorJson('SQLite3 error. could not get stock groups.');
        }
        $result = array('success' => true, 'stock_groups' => array());
        while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC))
            array_push($result['stock_groups'], $table['name']);
        return $result;
    }

    public function deleteStockGroup($groupName = null){
        if($groupName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        if($this->db->exec("DROP TABLE '${groupName}'")){
            return Utils::getSuccessJson();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not drop table.');
        }
    }

}
