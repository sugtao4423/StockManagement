<?php
require_once(dirname(__FILE__) . '/Utils.php');
require_once(dirname(__FILE__) . '/Config.php');

class Category{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createCategory($categoryName = null){
        if($categoryName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        if(Utils::isOnlySpaces($categoryName)){
            return Utils::getErrorJson('error. can not create category of only space string.');
        }

        $sql = 'INSERT INTO categories(name) VALUES (:categoryName)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not create category.');
        }

        return $this->getCategories();
    }

    public function getCategories(){
        $result = Utils::getSuccessJson('categories', []);

        $sql = 'SELECT
                categories.name,
                    (SELECT COUNT(*) FROM groups
                    WHERE groups.categoryId = categories.id)
                FROM categories';
        $query = $this->db->query($sql);
        if($query === false){
            return Utils::getErrorJson('SQLite3 error. could not get categories.');
        }

        while($q = $query->fetchArray(SQLITE3_NUM)){
            $result['categories'][] = [
                'name' => $q[0],
                'itemCount' => $q[1]
            ];
        }
        return $result;
    }

    public function deleteCategory($categoryName){
        if($categoryName === null){
            return Utils::getErrorJson('invalid parameter.');
        }

        $sql = 'DELETE FROM stocks
                WHERE stocks.groupId
                IN
                    (SELECT groups.id FROM groups
                    INNER JOIN categories
                    ON categories.id = groups.categoryId
                    WHERE categories.name = :categoryName)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not delete stocks.');
        }

        $sql = 'DELETE FROM groups
                WHERE groups.categoryId
                IN
                    (SELECT categories.id FROM categories
                    WHERE categories.name = :categoryName)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not delete stock groups.');
        }

        $sql = 'DELETE FROM categories WHERE name = :categoryName';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoryName', $categoryName, SQLITE3_TEXT);
        $exec = $stmt->execute();
        if($exec === false){
            return Utils::getErrorJson('SQLite3 error. could not delete category.');
        }

        return $this->getCategories();
    }

}
