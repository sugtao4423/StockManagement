<?php
require_once('./Utils.php');

class Stock{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createStock($groupName = null, $stockName = null, $exists = false){
        if($groupName === null or $stockName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        $stockName = Utils::sqlEscape($stockName);
        if($exists == true or $exists > 0){
            $exists = 1;
        }else{
            $exists = 0;
        }
        if($this->db->exec("INSERT INTO '${groupName}' (name, 'exists') values('${stockName}', ${exists})")){
            return Utils::getSuccessJson();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not add stock.');
        }
    }

    public function getStocks($groupName){
        if($groupName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        $query = $this->db->query("SELECT * FROM ${groupName}");
        if(!$query){
            return Utils::getErrorJson('SQLite3 error. could not get stocks.');
        }
        $result = array('success' => true, 'stocks' => array());
        while ($q = $query->fetchArray(SQLITE3_ASSOC)){
            $exists = $q['exists'] === 1;
            array_push($result['stocks'],
                array('id' => $q['id'], 'name' => $q['name'], 'exists' => $exists));
        }
        return $result;
    }

    public function deleteStock($groupName = null, $id = null){
        if($groupName === null or $id === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $groupName = Utils::sqlEscape($groupName);
        if($this->db->exec("DELETE FROM '${groupName}' WHERE id=${id}")){
            return Utils::getSuccessJson();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not delete stock.');
        }
    }

}
