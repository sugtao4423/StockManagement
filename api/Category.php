<?php
require_once('./Utils.php');

class Category{

    private $db;

    public function __construct($db){
        $this->db = $db;
        if(!$this->db->exec('CREATE TABLE IF NOT EXISTS category(name TEXT UNIQUE)')){
            return Utils::getErrorJson('SQLite3 error. could not create category table.');
        }
    }

    public function createCategory($catName = null){
        if($catName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $catName = Utils::sqlEscape($catName);
        if($this->db->exec("INSERT INTO category values('${catName}')")){
            return $this->getCategories();
        }else{
            return Utils::getErrorJson('SQLite3 error. could not create category.');
        }
    }

    public function getCategories(){
        $query = $this->db->query('SELECT name FROM category');
        if(!$query){
            return Utils::getErrorJson('SQLite3 error. could not get categories');
        }
        $result = Utils::getSuccessJson('categories', array());
        while ($q = $query->fetchArray(SQLITE3_ASSOC)){
            $groupDB = new SQLite3(Utils::getDBpath($q['name']));
            $itemCount = $groupDB->querySingle("SELECT COUNT(*) FROM sqlite_master WHERE type='table'");
            array_push($result['categories'], array('name' => $q['name'], 'itemCount' => $itemCount));
        }
        return $result;
    }

    public function deleteCategory($catName){
        if($catName === null){
            return Utils::getErrorJson('invalid parameter.');
        }
        $escCatName = Utils::sqlEscape($catName);
        if($this->db->exec("DELETE FROM category WHERE NAME='${escCatName}'")){
            if(unlink(Utils::getDBpath($catName))){
                return $this->getCategories();
            }else{
                return Utils::getErrorJson('error. could not delete database file.');
            }
        }else{
            return Utils::getErrorJson('SQLite3 error. could not delete category.');
        }
    }

}
