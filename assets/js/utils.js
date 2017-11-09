const TABLE_PARENT_ID = 'content';
const TABLE_ID = 'table';

function get(params, func){
    access('GET', params, func);
}

function post(params, func){
    access('POST', params, func);
}

function access(method, params, func){
    $.ajax({
        url: './api/api.php',
        type: method,
        data: params,
        dataType: 'json',
        success: func
    });
}
