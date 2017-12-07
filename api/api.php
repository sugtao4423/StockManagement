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

if(!file_exists(DB_DIR))
    mkdir(DB_DIR);

$result = null;

preg_match('|' . dirname($_SERVER['SCRIPT_NAME']) . '/(.*)|', $_SERVER['REQUEST_URI'], $m);
$query = explode('/', $m[1]);
$query = array_filter($query, 'strlen');
$query = array_values($query);
for($i = 0; $i < count($query); $i++){
    $query[$i] = urldecode($query[$i]);
}

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
    http_response_code(501);
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
    switch(count($query)){
    case 1:
        return (new Category())->createCategory($query[0]);
    case 2:
    case 3:
        $dbPath = Utils::getDBpath($query[0]);
        if(file_exists($dbPath))
            $db = new SQLite3($dbPath);
        else
            return Utils::getErrorJson('error. there are no database.');
        break;
    }

    switch(count($query)){
    case 2:
        return (new StockGroup($db))->createStockGroup($query[1]);
    case 3:
        return (new Stock($db))->createStock($query[1], $query[2]);
    }
    return null;
}

function doGet($query){
    switch(count($query)){
    case 0:
        return (new Category())->getCategories();
    case 1:
    case 2:
        $dbPath = Utils::getDBpath($query[0]);
        if(file_exists($dbPath))
            $db = new SQLite3($dbPath);
        else
            return Utils::getErrorJson('error. there are no database.');
        break;
    }

    switch(count($query)){
    case 1:
        return (new StockGroup($db))->getStockGroups();
    case 2:
        return (new Stock($db))->getStocks($query[1]);
    }
    return null;
}

function doPut($query){
    if(count($query) !== 4)
        return null;

    $dbPath = Utils::getDBpath($query[0]);
    if(file_exists($dbPath))
        $db = new SQLite3($dbPath);
    else
        return Utils::getErrorJson('error. there are no database.');

    return (new Stock($db))->updateStock($query[1], $query[2], $query[3]);
}

function doDelete($query){
    switch(count($query)){
    case 1:
        return (new Category())->deleteCategory($query[0]);
    case 2:
    case 3:
        $dbPath = Utils::getDBpath($query[0]);
        if(file_exists($dbPath))
            $db = new SQLite3($dbPath);
        else
            return Utils::getErrorJson('error. there are no database.');
        break;
    }

    switch(count($query)){
    case 2:
        return (new StockGroup($db))->deleteStockGroup($query[1]);
    case 3:
        return (new Stock($db))->deleteStock($query[1], $query[2]);
    }
    return null;
}

