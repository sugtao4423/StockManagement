<?php
declare(strict_types=1);

require_once(dirname(__FILE__) . '/Utils.php');

class StockGroup{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createStockGroup(string $categoryName, string $groupName): array{
        if(Utils::isOnlySpaces($categoryName) or Utils::isOnlySpaces($groupName)){
            return Utils::getErrorJson('error. can not create stock group of only space string.');
        }

        $sql = 'INSERT INTO groups(categoryId, name)
                VALUES ((SELECT id FROM categories WHERE name = :categoryName),
                :name)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':name', $groupName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not create stock group.');
        }

        return $this->getStockGroups($categoryName);
    }

    public function getStockGroups(string $categoryName): array{
        $result = [];

        $sql = 'SELECT
                groups.name,
                    (SELECT COUNT(*) FROM stocks WHERE stocks.groupId = groups.id),
                    (SELECT COUNT(*) FROM stocks WHERE stocks.groupId = groups.id AND have > 0)
                FROM groups
                INNER JOIN categories ON categories.id = groups.categoryId
                WHERE categories.name = :categoryName
                ORDER BY groups.name ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $query = $stmt->execute();
        if($query === false){
            return Utils::getErrorJson('SQLite3 error. could not get stock groups.');
        }

        while($q = $query->fetchArray(SQLITE3_NUM)){
            $result[] = [
                'name' => $q[0],
                'totalItemCount' => $q[1],
                'haveItemCount' => $q[2]
            ];
        }
        return Utils::getSuccessJson('groups', $result);
    }

    public function deleteStockGroup(string $categoryName, string $groupName): array{
        $sql = 'DELETE FROM stocks
                WHERE stocks.groupId
                IN (SELECT groups.id FROM groups
                    INNER JOIN categories
                    ON categories.id = groups.categoryId
                    WHERE categories.name = :categoryName
                    AND groups.name = :groupName)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':groupName', $groupName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not delete stocks.');
        }

        $sql = 'DELETE FROM groups
                WHERE groups.categoryId
                = (SELECT categories.id FROM categories
                    WHERE categories.name = :categoryName)
                AND groups.name = :groupName';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $stmt->bindValue(':groupName', $groupName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not delete stock group.');
        }

        return $this->getStockGroups($categoryName);
    }

}
