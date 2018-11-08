<?php
require_once(dirname(__FILE__) . '/Utils.php');

class Stock{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createStock($categoryName = null, $groupName = null, $stockName = null, $have = false){
        if($categoryName === null or $groupName === null or $stockName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        if(Utils::isOnlySpaces($stockName)){
            return Utils::getErrorJson('error. can not create stock of only space string.');
        }

        $have = Utils::getNumFromBool($have);
        $sql = 'INSERT INTO stocks(groupId, name, have)
                VALUES(
                    (SELECT groups.id FROM groups
                    INNER JOIN categories ON categories.id = groups.categoryId
                    WHERE categories.name = :categoryName
                    AND groups.name = :groupName),
                :stockName, :have)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':groupName', $groupName, SQLITE3_TEXT);
        $stmt->bindValue(':stockName', $stockName, SQLITE3_TEXT);
        $stmt->bindValue(':have', $have, SQLITE3_INTEGER);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not create stock.');
        }

        return $this->getStocks($categoryName, $groupName);
    }

    public function getStocks($categoryName = null, $groupName = null){
        if($categoryName === null or $groupName === null){
            return Utils::getErrorJson('invalid parameter.');
        }

        $sql = 'SELECT
                stocks.name, stocks.have
                FROM stocks
                INNER JOIN groups ON groups.id = stocks.groupId
                INNER JOIN categories ON categories.id = groups.categoryId
                WHERE categories.name = :categoryName
                AND groups.name = :groupName
                ORDER BY stocks.name ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':groupName', $groupName, SQLITE3_TEXT);
        $query = $stmt->execute();
        if($query === false){
            return Utils::getErrorJson('SQLite3 error. could not get stocks.');
        }

        $result = [];
        while($q = $query->fetchArray(SQLITE3_NUM)){
            $result[] = [
                'name' => $q[0],
                'have' => Utils::getBoolFromNum($q[1])
            ];
        }
        return Utils::getSuccessJson('stocks', $result);
    }

    public function updateStock($categoryName = null, $groupName = null, $stockName = null, $have = false){
        if($categoryName === null or $groupName === null or $stockName === null){
            return Utils::getErrorJson('invalid parameter.');
        }

        $have = Utils::getNumFromBool($have);
        $sql = 'UPDATE stocks SET have = :have
                WHERE groupId
                    = (SELECT groups.id FROM groups WHERE categoryId
                        = (SELECT categories.id FROM categories
                        WHERE categories.name = :categoryName)
                    AND groups.name = :groupName)
                AND stocks.name = :stockName';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':have', $have, SQLITE3_INTEGER);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':groupName', $groupName, SQLITE3_TEXT);
        $stmt->bindValue(':stockName', $stockName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not update stock.');
        }

        return $this->getStocks($categoryName, $groupName);
    }

    public function deleteStock($categoryName = null, $groupName = null, $stockName = null){
        if($categoryName === null or $groupName === null or $stockName === null){
            return Utils::getErrorJson('invalid parameter.');
        }

        $sql = 'DELETE FROM stocks
            WHERE groupId
                = (SELECT groups.id FROM groups
                WHERE groups.categoryId
                    = (SELECT categories.id FROM categories
                    WHERE categories.name = :categoryName)
                AND groups.name = :groupName)
            AND stocks.name = :stockName';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':groupName', $groupName, SQLITE3_TEXT);
        $stmt->bindValue(':stockName', $stockName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            Utils::getErrorJson('SQLite3 error. could not delete stock.');
        }

        return $this->getStocks($categoryName, $groupName);
    }

}
