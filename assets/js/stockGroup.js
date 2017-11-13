const NEW_STOCK_GROUP_INPUT_ID = 'newStockGroupInput';

function echoStockGroups(){
    get({'f': 'get_stock_groups', 'category_name': CATEGORY_NAME}, function(data){
        stockGroup2Table(data);
    });
}

function clickAddStockGroup(){
    var input = document.getElementById(NEW_STOCK_GROUP_INPUT_ID);
    if(input.value.length > 0){
        post({'f': 'create_stock_group', 'category_name': CATEGORY_NAME, 'group_name': input.value}, function(data){
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
    var nameth = tr.appendChild(document.createElement('th'));
    nameth.innerHTML = 'グループ名';
    nameth.className = 'col-xs-10';
    var countth = tr.appendChild(document.createElement('th'));
    countth.innerHTML = '所持 / 全件数';
    countth.className = 'col-xs-2';

    var tbody = table.appendChild(document.createElement('tbody'));
    for(var i in json.stock_groups){
        var name = json.stock_groups[i].name;
        var totalItemCount = json.stock_groups[i].totalItemCount;
        var haveItemCount = json.stock_groups[i].haveItemCount;

        var tr = tbody.insertRow(-1);
        tr.setAttribute('data-href', '?cat=' + CATEGORY_NAME + '&group=' + name);
        tr.insertCell(-1).innerHTML = name;
        tr.insertCell(-1).innerHTML = `${haveItemCount} / ${totalItemCount}`;
    }
    setStockGroupLink();

    var addtr = tbody.insertRow(-1);
    var input = addtr.insertCell(-1).appendChild(document.createElement('input'));
    input.id = NEW_STOCK_GROUP_INPUT_ID;
    input.style.display = 'none';
    input.setAttribute('onkeydown', 'if(window.event.keyCode == 13) clickAddStockGroup();');
    input.placeholder = 'グループ名';
    var button = addtr.insertCell(-1).appendChild(document.createElement('button'));
    button.type = 'button';
    button.className = 'btn btn-info';
    button.setAttribute('onclick', 'clickAddStockGroup();');
    button.innerHTML = 'Add';
}

function delCategory(){
    if(confirm(CATEGORY_NAME + '\n削除してもよろしいですか？')){
        del({'f': 'delete_category', 'category_name': CATEGORY_NAME}, function(data){
            window.location = '.';
        });
    }
}

function setStockGroupLink(){
    var tr = document.querySelectorAll('tbody > tr');
    for(var i = 0; i < tr.length; i++){
        tr[i].addEventListener('click', function(){
            window.location = this.getAttribute('data-href');
        });
    }
}
