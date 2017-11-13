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
        if($this->db->exec("CREATE TABLE '${groupName}' (id INTEGER PRIMARY KEY, name TEXT, have INTEGER)")){
            return $this->getStockGroups();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not create table.');
        }
    }

    public function getStockGroups(){
        $tablesquery = $this->db->query("SELECT name FROM sqlite_master WHERE type='table'");
        if(!$tablesquery){
            return Utils::getErrorJson('SQLite3 error. could not get stock groups.');
        }
        $result = Utils::getSuccessJson('stock_groups', array());
        while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC)){
            $groupName = Utils::sqlEscape($table['name']);
            $totalItemCount = $this->db->querySingle("SELECT COUNT(*) FROM '${groupName}'");
            $haveItemCount = $this->db->querySingle("SELECT COUNT(*) FROM '${groupName}' WHERE have > 0");
            array_push($result['stock_groups'], array('name' => $table['name'], 'totalItemCount' => $totalItemCount, 'haveItemCount' => $haveItemCount));
        }
        return $result;
    }

    public function deleteStockGroup($groupName = null){
        if($groupName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        if($this->db->exec("DROP TABLE '${groupName}'")){
            return $this->getStockGroups();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not drop table.');
        }
    }

}
