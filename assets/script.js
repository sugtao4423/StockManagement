function echoStockGroups(elementId){
    get({'f': 'get_stock_groups'}, function(data){
        var table = document.getElementById(elementId).appendChild(document.createElement('table'));
        table.className = 'table table-hover';

        var thead = table.appendChild(document.createElement('thead'));
        var tr = thead.insertRow(-1);
        tr.appendChild(document.createElement('th')).innerHTML = 'グループ名';
        tr.appendChild(document.createElement('th')).innerHTML = '件数';

        var tbody = table.appendChild(document.createElement('tbody'));
        for(var i in data.stock_groups){
            var name = data.stock_groups[i].name;
            var itemCount = data.stock_groups[i].itemCount;

            var tr = tbody.insertRow(-1);
            tr.setAttribute('data-href', '?group=' + name);
            tr.insertCell(-1).innerHTML = name;
            tr.insertCell(-1).innerHTML = itemCount;
        }
    });
}

function get(params, func){
    access('GET', params, func);
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

jQuery(function($) {
    $('tbody tr[data-href]').addClass('clickable').click(function(){
        window.location = $(this).attr('data-href');
    }).find('a').hover(function(){
        $(this).parents('tr').unbind('click');
    }, function(){
        $(this).parents('tr').click( function(){
            window.location = $(this).attr('data-href');
        });
    });
});
