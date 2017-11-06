<?php
require_once('./StockGroup.php');
require_once('./Utils.php');

define('DB_LOCATION', './database.sqlite3');

$db = new SQLite3(DB_LOCATION);

$result = null;

switch($_SERVER['REQUEST_METHOD']){
    case 'POST':
        $result = doPost();
        break;
    case 'GET':
        $result = doGet();
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
echo json_encode($result, JSON_UNESCAPED_UNICODE);


function doPost(){
    global $db;
    if(isset($_POST['f'])){
        switch($_POST['f']){
        case 'create_group':
            return (new StockGroup($db))->createStockGroup($_POST['group_name']);
        }
    }
    return null;
}

function doGet(){
    global $db;
    if(isset($_GET['f'])){
        switch($_GET['f']){
        case 'get_groups':
            return (new StockGroup($db))->getStockGroups();
        }
    }
    return null;
}

function doDelete(){
    global $db;
    parse_str(file_get_contents('php://input'), $_DELETE);
    if(isset($_DELETE['f'])){
        switch($_DELETE['f']){
        case 'delete_group':
            return (new StockGroup($db))->deleteStockGroup($_DELETE['group_name']);
        }
    }
    return null;
}

