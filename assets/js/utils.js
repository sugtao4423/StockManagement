const TABLE_PARENT_ID = 'content';
const TABLE_ID = 'table';

function searchText(inputBox){
    var queries = inputBox.value.split(/\s+|　+/);
    var query = '';
    for(var i = 0; i < queries.length; i++){
        if(queries[i] !== ''){
            query += '(?=.*' + queries[i] + ')';
        }
    }
    var tr = document.querySelectorAll('table > tbody > tr');
    for(var i = 0; i < tr.length - 1; i++){
        var grpName = tr[i].cells[0].innerHTML;
        var m = grpName.match(new RegExp(query, 'i'));
        if(m !== null && m.length > 0){
            tr[i].style.display = '';
        }else{
            tr[i].style.display = 'none';
        }
    }
}

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
