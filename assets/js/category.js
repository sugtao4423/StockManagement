const NEW_CATEGORY_INPUT_ID = 'newCategoryInput';

function echoCategories(){
    get({'f': 'get_categories'}, function(data){
        categories2Table(data);
    });
}

function clickAddCategory(){
    var input = document.getElementById(NEW_CATEGORY_INPUT_ID);
    if(input.value.length > 0){
        post({'f': 'create_category', 'category_name': input.value}, function(data){
            categories2Table(data);
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

function categories2Table(json){
    var tableParent = document.getElementById(TABLE_PARENT_ID);
    while(tableParent.firstChild)
        tableParent.removeChild(tableParent.firstChild);

    var table = tableParent.appendChild(document.createElement('table'));
    table.id = TABLE_ID;
    table.className = 'table table-hover';

    var thead = table.appendChild(document.createElement('thead'));
    var tr = thead.insertRow(-1);
    tr.appendChild(document.createElement('th')).innerHTML = 'カテゴリー';
    tr.appendChild(document.createElement('th')).innerHTML = '件数';

    var tbody = table.appendChild(document.createElement('tbody'));
    for(var i in json.categories){
        var name = json.categories[i].name;
        var itemCount = json.categories[i].itemCount;

        var tr = tbody.insertRow(-1);
        tr.setAttribute('data-href', '?cat=' + name);
        tr.insertCell(-1).innerHTML = name;
        tr.insertCell(-1).innerHTML = itemCount;
    }
    setCategoryLink();

    var addtr = tbody.insertRow(-1);
    var input = addtr.insertCell(-1).appendChild(document.createElement('input'));
    input.id = NEW_CATEGORY_INPUT_ID;
    input.style.display = 'none';
    input.setAttribute('onkeydown', 'if(window.event.keyCode == 13) clickAddCategory();');
    input.placeholder = 'カテゴリー名';
    var button = addtr.insertCell(-1).appendChild(document.createElement('button'));
    button.type = 'button';
    button.className = 'btn btn-info';
    button.setAttribute('onclick', 'clickAddCategory();');
    button.innerHTML = 'Add';
}

function setCategoryLink(){
    var tr = document.querySelectorAll('tbody > tr');
    for(var i = 0; i < tr.length; i++){
        tr[i].addEventListener('click', function(){
            window.location = this.getAttribute('data-href');
        });
    }
}
