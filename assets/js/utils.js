const TABLE_PARENT_ID = 'content';
const TABLE_ID = 'table';

function post(path, func){
    access(path, 'POST', func);
}

function get(path, func){
    access(path, 'GET', func);
}

function put(path, func){
    access(path, 'PUT', func);
}

function del(path, func){
    access(path, 'DELETE', func);
}

function access(path, method, func){
    $.ajax({
        url: './api/' + path,
        type: method,
        dataType: 'json',
        success: func
    });
}
