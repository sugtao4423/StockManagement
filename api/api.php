<?php
require_once(dirname(__FILE__) . '/Config.php');
require_once(dirname(__FILE__) . '/Utils.php');

if(Config::$USE_AUTHORIZE){
    if (!isset($_SERVER['PHP_AUTH_USER'])){
        header('WWW-Authenticate: Basic realm="Enter username and password."');
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        die(json_encode(Utils::getErrorJson('Unauthorized'), JSON_UNESCAPED_UNICODE));
    }else{
        if ($_SERVER['PHP_AUTH_USER'] != Config::$USERNAME || $_SERVER['PHP_AUTH_PW'] != Config::$PASSWORD){
            header('WWW-Authenticate: Basic realm="Enter username and password."');
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            die(json_encode(Utils::getErrorJson('Unauthorized'), JSON_UNESCAPED_UNICODE));
        }
    }
}

require_once(dirname(__FILE__) . '/Category.php');
require_once(dirname(__FILE__) . '/StockGroup.php');
require_once(dirname(__FILE__) . '/Stock.php');

$result = null;

preg_match('|' . dirname($_SERVER['SCRIPT_NAME']) . '/(.*)|', $_SERVER['REQUEST_URI'], $m);
$query = explode('/', $m[1]);
$query = array_filter($query, 'strlen');
$query = array_values($query);
$query = array_map('urldecode', $query);

$db = new SQLite3(Config::$DB_FILE);
$db->exec('CREATE TABLE IF NOT EXISTS categories(id INTEGER PRIMARY KEY, name TEXT UNIQUE)');
$db->exec('CREATE TABLE IF NOT EXISTS groups(id INTEGER PRIMARY KEY, categoryId INTEGER, name TEXT, UNIQUE(categoryId, name))');
$db->exec('CREATE TABLE IF NOT EXISTS stocks(groupId INTEGER, name TEXT, have INTEGER, UNIQUE(groupId, name))');

switch(strtoupper($_SERVER['REQUEST_METHOD'])){
    case 'POST':
        $result = doPost($query);
        break;
    case 'GET':
        $result = doGet($query);
        break;
    case 'PUT':
        $result = doPut($query);
        break;
    case 'DELETE':
        $result = doDelete($query);
        break;
}

if($result === null){
    $result = Utils::getErrorJson('invalid parameter.');
    http_response_code(400);
}else{
    if($result['success'] == true){
        http_response_code(200);
    }else{
        http_response_code(400);
    }
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_UNESCAPED_UNICODE);


function doPost($query){
    global $db;
    switch(count($query)){
    case 1:
        return (new Category($db))->createCategory($query[0]);
    case 2:
        return (new StockGroup($db))->createStockGroup($query[0], $query[1]);
    case 3:
        return (new Stock($db))->createStock($query[0], $query[1], $query[2]);
    default:
        return null;
    }
}

function doGet($query){
    global $db;
    switch(count($query)){
    case 0:
        return (new Category($db))->getCategories();
    case 1:
        return (new StockGroup($db))->getStockGroups($query[0]);
    case 2:
        return (new Stock($db))->getStocks($query[0], $query[1]);
    default:
        return null;
    }
}

function doPut($query){
    if(count($query) !== 4){
        return null;
    }
    global $db;
    return (new Stock($db))->updateStock($query[0], $query[1], $query[2], $query[3]);
}

function doDelete($query){
    global $db;
    switch(count($query)){
    case 1:
        return (new Category($db))->deleteCategory($query[0]);
    case 2:
        return (new StockGroup($db))->deleteStockGroup($query[0], $query[1]);
    case 3:
        return (new Stock($db))->deleteStock($query[0], $query[1], $query[2]);
    default:
        return null;
    }
}

