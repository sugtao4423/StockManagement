<?php
require_once(dirname(__FILE__) . '/Utils.php');

class Stock{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createStock($groupName = null, $stockName = null, $have = false){
        if($groupName === null or $stockName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $escGroupName = Utils::sqlEscape($groupName);
        $stockName = Utils::sqlEscape($stockName);
        $have = Utils::getNumFromBool($have);
        if($this->db->exec("INSERT INTO '${escGroupName}' (name, have) values('${stockName}', ${have})")){
            return $this->getStocks($groupName);
        }else{
            return Utils::getErrorJson('SQLite3 error. could not add stock.');
        }
    }

    public function getStocks($groupName){
        if($groupName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        $query = $this->db->query("SELECT * FROM '${groupName}'");
        if(!$query){
            return Utils::getErrorJson('SQLite3 error. could not get stocks.');
        }
        $result = Utils::getSuccessJson('stocks', array());
        while ($q = $query->fetchArray(SQLITE3_ASSOC)){
            $have = Utils::getBoolFromNum($q['have']);
            array_push($result['stocks'],
                array('id' => $q['id'], 'name' => $q['name'], 'have' => $have));
        }
        return $result;
    }

    public function updateStock($groupName = null, $id = null, $have = false){
        if($groupName === null or $id === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $escGroupName = Utils::sqlEscape($groupName);
        $have = Utils::getNumFromBool($have);
        if($this->db->exec("UPDATE '${escGroupName}' SET have=${have} WHERE id=${id}")){
            return $this->getStocks($groupName);
        }else{
            return Utils::getErrorJson('SQLite3 error. could not update stock.');
        }
    }

    public function deleteStock($groupName = null, $id = null){
        if($groupName === null or $id === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $escGroupName = Utils::sqlEscape($groupName);
        if($this->db->exec("DELETE FROM '${escGroupName}' WHERE id=${id}")){
            return $this->getStocks($groupName);
        }else{
            return Utils::getErrorJson('SQLite3 error. could not delete stock.');
        }
    }

}
