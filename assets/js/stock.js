const NEW_STOCK_INPUT_ID = 'newStockInput';

function echoStocks(groupName){
    get({'f': 'get_stocks', 'group_name': groupName}, function(data){
        stocks2Table(data, groupName);
    });
}

function clickAddStock(groupName){
    var input = document.getElementById(NEW_STOCK_INPUT_ID);
    if(input.value.length > 0){
        post({'f': 'create_stock', 'group_name': groupName, 'stock_name': input.value}, function(data){
            stocks2Table(data, groupName);
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

function clickCheckbox(checkbox, groupName){
    put({'f': 'update_stock', 'group_name': groupName, 'id': checkbox.getAttribute('data-id'), 'exists': checkbox.checked}, function(data){
        stocks2Table(data, groupName);
    });
}

function stocks2Table(json, groupName){
    var tableParent = document.getElementById(TABLE_PARENT_ID);
    while(tableParent.firstChild)
        tableParent.removeChild(tableParent.firstChild);

    var escGroupName = groupName.replace(/'/g, "\\'");

    var table = tableParent.appendChild(document.createElement('table'));
    table.id = TABLE_ID;
    table.className = 'table table-hover';

    var thead = table.appendChild(document.createElement('thead'));
    var tr = thead.insertRow(-1);
    var nametd = tr.appendChild(document.createElement('th'));
    nametd.innerHTML = '名前';
    nametd.className = 'col-xs-10';
    var existstd = tr.appendChild(document.createElement('th'));
    existstd.innerHTML = '所持';
    existstd.className = 'col-xs-2';

    var tbody = table.appendChild(document.createElement('tbody'));
    for(var i in json.stocks){
        var id = json.stocks[i].id;
        var name = json.stocks[i].name;
        var exists = json.stocks[i].exists;

        var tr = tbody.insertRow(-1);
        tr.insertCell(-1).innerHTML = name;
        var checkbox = tr.insertCell(-1).appendChild(document.createElement('input'));
        checkbox.type = 'checkbox';
        checkbox.checked = exists;
        checkbox.setAttribute('data-id', id);
        checkbox.setAttribute('onclick', `clickCheckbox(this, '${escGroupName}');`);
    }

    var addtr = tbody.insertRow(-1);
    var input = addtr.insertCell(-1).appendChild(document.createElement('input'));
    input.id = NEW_STOCK_INPUT_ID;
    input.style.display = 'none';
    input.setAttribute('onkeydown', `if(window.event.keyCode == 13) clickAddStock('${escGroupName}');`);
    var button = addtr.insertCell(-1).appendChild(document.createElement('button'));
    button.type = 'button';
    button.className = 'btn btn-info';
    button.setAttribute('onclick', `clickAddStock('${escGroupName}');`);
    button.innerHTML = 'Add';
}

function delGroup(groupName){
    if(confirm(groupName + '\n削除してもよろしいですか？')){
        del({'f': 'delete_stock_group', 'group_name': groupName}, function(data){
            window.location = window.location.href.split('?')[0];
        });
    }
}
