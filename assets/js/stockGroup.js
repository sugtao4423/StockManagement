const NEW_STOCK_GROUP_INPUT_ID = 'newStockGroupInput';

function echoStockGroups(){
    var uri = '/' + encodeURIComponent(CATEGORY_NAME);
    get(uri, function(data){
        stockGroup2Table(data);
    });
}

function clickAddStockGroup(){
    var input = document.getElementById(NEW_STOCK_GROUP_INPUT_ID);
    if(input.value.length > 0){
        var uri = '/' + encodeURIComponent(CATEGORY_NAME) + '/' + encodeURIComponent(input.value);
        post(uri, function(data){
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

        var isComplete = (haveItemCount === totalItemCount);

        var tr = tbody.insertRow(-1);
        tr.setAttribute('data-href', '?cat=' + CATEGORY_NAME + '&group=' + name);
        tr.insertCell(-1).innerHTML = name;
        if(isComplete){
            tr.insertCell(-1).innerHTML = '✔';
        }else{
            tr.insertCell(-1).innerHTML = `${haveItemCount} / ${totalItemCount}`;
        }
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
        if(confirm('ほんとに ' + CATEGORY_NAME + ' を削除してもよろしいですか？')){
            var uri = '/' + encodeURIComponent(CATEGORY_NAME);
            del(uri, function(data){
                window.location = '.';
            });
        }
    }
}

function setStockGroupLink(){
    var tr = document.querySelectorAll('tbody > tr');
    for(var i = 0; i < tr.length; i++){
        tr[i].style.cursor = 'pointer';
        tr[i].addEventListener('click', function(){
            window.location = this.getAttribute('data-href');
        });
    }
}
