const NEW_STOCK_INPUT_ID = 'newStockInput';
const EDIT_BTN_ID = 'editBtn';

function echoStocks(){
    var uri = '/' + encodeURIComponent(CATEGORY_NAME) + '/' + encodeURIComponent(GROUP_NAME);
    get(uri, function(data){
        stocks2Table(data);
    });
}

function clickAddStock(){
    var input = document.getElementById(NEW_STOCK_INPUT_ID);
    if(input.value.length > 0){
        var uri = '/' + encodeURIComponent(CATEGORY_NAME) + '/' + encodeURIComponent(GROUP_NAME) + '/' + encodeURIComponent(input.value);
        post(uri, function(data){
            stocks2Table(data);
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

function clickCheckbox(checkbox){
    var uri = '/' + encodeURIComponent(CATEGORY_NAME) + '/' + encodeURIComponent(GROUP_NAME) + '/' + encodeURIComponent(checkbox.getAttribute('data-name')) + '/' + checkbox.checked;
    put(uri, function(data){
        stocks2Table(data);
    });
}

function stocks2Table(json){
    var tableParent = document.getElementById(TABLE_PARENT_ID);
    while(tableParent.firstChild)
        tableParent.removeChild(tableParent.firstChild);

    var table = tableParent.appendChild(document.createElement('table'));
    table.id = TABLE_ID;
    table.className = 'table table-hover';

    var thead = table.appendChild(document.createElement('thead'));
    var tr = thead.insertRow(-1);
    var nameth = tr.appendChild(document.createElement('th'));
    nameth.innerHTML = '名前';
    nameth.className = 'col-xs-10';
    var haveth = tr.appendChild(document.createElement('th'));
    haveth.innerHTML = '所持';
    haveth.className = 'col-xs-2';

    var tbody = table.appendChild(document.createElement('tbody'));
    for(var i in json.stocks){
        var name = json.stocks[i].name;
        var have = json.stocks[i].have;

        var tr = tbody.insertRow(-1);
        tr.insertCell(-1).innerHTML = name;
        var checkbox = tr.insertCell(-1).appendChild(document.createElement('input'));
        checkbox.type = 'checkbox';
        checkbox.checked = have;
        checkbox.setAttribute('data-name', name);
        checkbox.setAttribute('onclick', 'clickCheckbox(this);');
    }

    var addtr = tbody.insertRow(-1);
    var input = addtr.insertCell(-1).appendChild(document.createElement('input'));
    input.id = NEW_STOCK_INPUT_ID;
    input.style.display = 'none';
    input.setAttribute('onkeydown', 'if(window.event.keyCode == 13) clickAddStock();');
    input.placeholder = '名前';
    var button = addtr.insertCell(-1).appendChild(document.createElement('button'));
    button.type = 'button';
    button.className = 'btn btn-info';
    button.setAttribute('onclick', 'clickAddStock();');
    button.innerHTML = 'Add';

    var isEditing = document.getElementById(EDIT_BTN_ID).getAttribute('data-editing');
    if(isEditing == 'true'){
        setStockDelBtn();
    }
}

function clickEditStock(){
    var editBtn = document.getElementById(EDIT_BTN_ID);
    var isEditing = editBtn.getAttribute('data-editing');
    if(isEditing == null || isEditing == 'false'){
        editBtn.setAttribute('data-editing', true);
        editBtn.innerHTML = '完了';
        setStockDelBtn();
    }else{
        editBtn.setAttribute('data-editing', false);
        editBtn.innerHTML = '編集';
        clearStockDelBtn();
    }
}

function setStockDelBtn(){
    var checkboxs = document.querySelectorAll('input[type=checkbox]');
    for(var i = 0; i < checkboxs.length; i++){
        checkboxs[i].style.display = 'none';
        var stockDelBtn = checkboxs[i].parentNode.appendChild(document.createElement('button'));
        stockDelBtn.className = 'btn btn-danger btn-sm';
        stockDelBtn.setAttribute('data-name', checkboxs[i].getAttribute('data-name'));
        stockDelBtn.innerHTML = '削除';
        var escStockName = checkboxs[i].getAttribute('data-name').replace(/'/g, "\\'");
        stockDelBtn.setAttribute('onclick', `delStock('${escStockName}');`);
    }
}

function clearStockDelBtn(){
    var stockDelBtns = document.querySelectorAll('button[class="btn btn-danger btn-sm"]');
    var checkboxs = document.querySelectorAll('input[type=checkbox]');
    for(var i = 0; i < stockDelBtns.length; i++)
        stockDelBtns[i].parentNode.removeChild(stockDelBtns[i]);
    for(var i = 0; i < checkboxs.length; i++)
        checkboxs[i].style.display = 'block';
}

function delStock(stockName){
    if(confirm(stockName + '\n削除してもよろしいですか？')){
        var uri = '/' + encodeURIComponent(CATEGORY_NAME) + '/' + encodeURIComponent(GROUP_NAME) + '/' + encodeURIComponent(stockName);
        del(uri, function(data){
            stocks2Table(data);
        });
    }
}

function delGroup(){
    if(confirm(GROUP_NAME + '\n削除してもよろしいですか？')){
        var uri = '/' + encodeURIComponent(CATEGORY_NAME) + '/' + encodeURIComponent(GROUP_NAME);
        del(uri, function(data){
            window.location = './?cat=' + CATEGORY_NAME;
        });
    }
}
