<?php

define('DB_LOCATION', './database.sqlite3');

$db = new SQLite3(DB_LOCATION);

$result = null;

switch($_SERVER['REQUEST_METHOD']){
    case 'POST':
        $result = doPost();
        break;
    case 'DELETE':
        $result = doDelete();
        break;
}

if($result === null){
    $result = getErrorJson('invalid parameter.');
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
    if(isset($_POST['f'])){
        switch($_POST['f']){
        case 'create_group':
            return createStockGroup($_POST['group_name']);
        }
    }
    return null;
}

function doDelete(){
    parse_str(file_get_contents('php://input'), $_DELETE);
    if(isset($_DELETE['f'])){
        switch($_DELETE['f']){
        case 'delete_group':
            return deleteStockGroup($_DELETE['group_name']);
        }
    }
    return null;
}

function createStockGroup($groupName = null){
    if($groupName === null){
        return getErrorJson('invalid parameter.');
    }
    global $db;
    $groupName = str_replace("'", "''", $groupName);
    if($db->exec("CREATE TABLE '${groupName}' (id INTEGER PRIMARY KEY, name TEXT, 'exists' INTEGER)")){
        return getSuccessJson();
    }else{
        return getErrorJson('SQLite3 error. could not create table.');
    }
}

function deleteStockGroup($groupName = null){
    if($groupName === null){
        return getErrorJson('invalid parameter.');
    }
    global $db;
    $groupName = str_replace("'", "''", $groupName);
    if($db->exec("DROP TABLE '${groupName}'")){
        return getSuccessJson();
    }else{
        return getErrorJson('SQLite3 error. could not drop table');
    }
}

function getSuccessJson(){
    return array('success' => true);
}
function getErrorJson($message){
    return array('success' => false, 'message' => $message);
}
