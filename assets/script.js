const TABLE_PARENT_ID = 'content';
const TABLE_ID = 'table';

const NEW_STOCK_GROUP_INPUT_ID = 'newStockGroupInput';
const NEW_STOCK_GROUP_INPUT_ROW_ID = 'newStockGroupRow';

function echoStockGroups(){
    get({'f': 'get_stock_groups'}, function(data){
        stockGroup2Table(data);
    });
}

function clickAddStockGroup(){
    var input = document.getElementById(NEW_STOCK_GROUP_INPUT_ID);
    if(input === null){
        var table = document.getElementById(TABLE_ID);
        var tr = table.insertRow(-1);
        tr.id = NEW_STOCK_GROUP_INPUT_ROW_ID;
        var text = tr.insertCell(-1).appendChild(document.createElement('input'));
        text.id = NEW_STOCK_GROUP_INPUT_ID;
        tr.insertCell(-1);
    }else{
        if(input.value.length > 0){
            post({'f': 'create_stock_group', 'group_name': input.value}, function(data){
                stockGroup2Table(data);
            });
        }else{
            var inputRow = document.getElementById(NEW_STOCK_GROUP_INPUT_ROW_ID);
            inputRow.parentNode.removeChild(inputRow);
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
