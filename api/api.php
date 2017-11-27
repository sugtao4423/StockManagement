<?php
require_once('./Config.php');
require_once('./Utils.php');

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

require_once('./Category.php');
require_once('./StockGroup.php');
require_once('./Stock.php');

if(!file_exists(DB_DIR))
    mkdir(DB_DIR);

$result = null;

switch($_SERVER['REQUEST_METHOD']){
    case 'POST':
        $result = doPost();
        break;
    case 'GET':
        $result = doGet();
        break;
    case 'PUT':
        $result = doPut();
        break;
    case 'DELETE':
        $result = doDelete();
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


function doPost(){
    if(!isset($_POST['f']))
        return null;

    if($_POST['f'] == 'create_category'){
        return (new Category())->createCategory($_POST['category_name']);
    }else{
        if(!isset($_POST['category_name']))
            return null;
        $dbPath = Utils::getDBpath($_POST['category_name']);
        if(file_exists($dbPath))
            $db = new SQLite3($dbPath);
        else
            return Utils::getErrorJson('error. there are no database.');
    }

    switch($_POST['f']){
    case 'create_stock_group':
        return (new StockGroup($db))->createStockGroup($_POST['group_name']);
    case 'create_stock':
        return (new Stock($db))->createStock($_POST['group_name'], $_POST['stock_name'], $_POST['have']);
    }
    return null;
}

function doGet(){
    if(!isset($_GET['f']))
        return null;

    if($_GET['f'] == 'get_categories'){
        return (new Category())->getCategories();
    }else{
        if(!isset($_GET['category_name']))
            return null;
        $dbPath = Utils::getDBpath($_GET['category_name']);
        if(file_exists($dbPath))
            $db = new SQLite3($dbPath);
        else
            return Utils::getErrorJson('error. there are no database.');
    }

    switch($_GET['f']){
    case 'get_stock_groups':
        return (new StockGroup($db))->getStockGroups();
    case 'get_stocks':
        return (new Stock($db))->getStocks($_GET['group_name']);
    }
    return null;
}

function doPut(){
    parse_str(file_get_contents('php://input'), $_PUT);
    if(!isset($_PUT['f']) or !isset($_PUT['category_name']))
        return null;

    $dbPath = Utils::getDBpath($_PUT['category_name']);
    if(file_exists($dbPath))
        $db = new SQLite3($dbPath);
    else
        return Utils::getErrorJson('error. there are no database.');

    switch($_PUT['f']){
    case 'update_stock':
        return (new Stock($db))->updateStock($_PUT['group_name'], $_PUT['id'], $_PUT['have']);
    }
    return null;
}

function doDelete(){
    parse_str(file_get_contents('php://input'), $_DELETE);
    if(!isset($_DELETE['f']))
        return null;

    if($_DELETE['f'] == 'delete_category'){
        return (new Category())->deleteCategory($_DELETE['category_name']);
    }else{
        if(!isset($_DELETE['category_name']))
            return null;
        $dbPath = Utils::getDBpath($_DELETE['category_name']);
        if(file_exists($dbPath))
            $db = new SQLite3($dbPath);
        else
            return Utils::getErrorJson('error. there are no database.');
    }

    switch($_DELETE['f']){
    case 'delete_stock_group':
        return (new StockGroup($db))->deleteStockGroup($_DELETE['group_name']);
    case 'delete_stock':
        return (new Stock($db))->deleteStock($_DELETE['group_name'], $_DELETE['id']);
    }
    return null;
}

