const TABLE_PARENT_ID = 'content';
const TABLE_ID = 'table';

const NEW_STOCK_GROUP_INPUT_ID = 'newStockGroupInput';

function echoStockGroups(){
    get({'f': 'get_stock_groups'}, function(data){
        stockGroup2Table(data);
    });
}

function clickAddStockGroup(){
    var input = document.getElementById(NEW_STOCK_GROUP_INPUT_ID);
    if(input.value.length > 0){
        post({'f': 'create_stock_group', 'group_name': input.value}, function(data){
            stockGroup2Table(data);
        });
    }else{
        if(input.style.display == 'none'){
            input.style.display = 'block';
            input.focus();
        }else{
            input.style.display = 'none';
        }
    }
}

function stockGroup2Table(json){
    var tableParent = document.getElementById(TABLE_PARENT_ID);
    while(tableParent.firstChild)
        tableParent.removeChild(tableParent.firstChild);

    var table = tableParent.appendChild(document.createElement('table'));
    table.id = TABLE_ID;
    table.className = 'table table-hover';

    var thead = table.appendChild(document.createElement('thead'));
    var tr = thead.insertRow(-1);
    tr.appendChild(document.createElement('th')).innerHTML = 'グループ名';
    tr.appendChild(document.createElement('th')).innerHTML = '件数';

    var tbody = table.appendChild(document.createElement('tbody'));
    for(var i in json.stock_groups){
        var name = json.stock_groups[i].name;
        var itemCount = json.stock_groups[i].itemCount;

        var tr = tbody.insertRow(-1);
        tr.setAttribute('data-href', '?group=' + name);
        tr.insertCell(-1).innerHTML = name;
        tr.insertCell(-1).innerHTML = itemCount;
    }
    setStockGroupLink();

    var addtr = tbody.insertRow(-1);
    var input = addtr.insertCell(-1).appendChild(document.createElement('input'));
    input.id = NEW_STOCK_GROUP_INPUT_ID;
    input.style.display = 'none';
    input.setAttribute('onkeydown', 'if(window.event.keyCode == 13) clickAddStockGroup();');
    var button = addtr.insertCell(-1).appendChild(document.createElement('button'));
    button.type = 'button';
    button.className = 'btn btn-info';
    button.setAttribute('onclick', 'clickAddStockGroup();');
    button.innerHTML = 'Add';
}

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

function setStockGroupLink(){
    var tr = document.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for(var i = 0; i < tr.length; i++){
        tr[i].addEventListener('click', function(){
            window.location = this.getAttribute('data-href');
        });
    }
}
