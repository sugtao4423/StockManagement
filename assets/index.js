const router = new VueRouter({
    routes: [
        {name: 'index', path: '/'},
        {name: 'inCategory', path: '/:category'},
        {name: 'inGroup', path: '/:category/:group'}
    ],
    scrollBehavior: function(to, from, savedPosition){
        const positionY = savedPosition ? savedPosition.y : 0;
        sessionStorage.setItem('positionY', positionY);
    }
});

const stockManagement = new Vue({
    el: '#stockManagement',
    router: router,
    data: {
        params: [],
        data: [],
        fetching: false,
        nextPageChangeNoLoad: false,
        filterValue: '',
        addInput: {
            value: '',
            show: false
        },
        editing: false,
        modal: {
            show: false,
            deleteItemName: '',
            isDeleteStock: false,
            confirmInputValue: ''
        }
    },
    created: function(){
        this.pageChanged();
    },
    updated: function(){
        if(this.data !== undefined || this.data.length != 0){
            scrollTo(0, sessionStorage.getItem('positionY'));
        }
    },
    watch: {
        $route: function(to, from){
            this.pageChanged();
        }
    },
    methods: {
        pageChanged: function(){
            this.filterValue = '';
            this.addInput.show = false;
            this.editing = false;
            this.params = [];
            const param = this.$router.currentRoute.params;
            if(param['category'] !== undefined){
                this.params = [param['category']];
            }
            if(param['group'] !== undefined){
                this.params = this.params.concat(param['group']);
            }

            if(this.nextPageChangeNoLoad){
                this.nextPageChangeNoLoad = false;
                return;
            }
            this.access(this.params);
        },
        access: function(urlParams, method = 'GET', nextPageChangeNoLoad = false){
            this.nextPageChangeNoLoad = nextPageChangeNoLoad;
            let url = './api/';
            urlParams.forEach((val) => {
                url += `${val}/`;
            });

            const oldData = this.data;
            this.data = [];
            this.fetching = true;
            fetch(url, {method: method})
            .then((res) => {
                if(res.ok){
                    return res.json();
                }else{
                    return res.json().then((err) => {
                        throw Error(err.message);
                    });
                }
            })
            .then((res) => {
                this.data = res;
            })
            .catch((err) => {
                this.data = oldData;
                console.log(err);
            })
            .finally(() => {
                this.fetching = false;
            });
        },
        clickTr: function(itemName){
            switch(this.data.type){
            case 'categories':
                this.$router.push({name: 'inCategory', params: {category: itemName}});
                break;
            case 'groups':
                this.$router.push({name: 'inGroup', params: {group: itemName}});
                break;
            }
        },
        column2data: function(item){
            switch(this.data.type){
            case 'categories':
                return item.itemCount;
            case 'groups':
                const totalItemCount = item.totalItemCount;
                const haveItemCount = item.haveItemCount;
                if(totalItemCount == haveItemCount){
                    return '✔';
                }else{
                    return `${haveItemCount} / ${totalItemCount}`;
                }
            }
        },
        clickAdd: function(){
            this.addInput.show = !this.addInput.show;
            this.$nextTick(() => {
                this.$refs.addInput.focus();
            });
            if(this.addInput.value.length > 0){
                const params = this.params.concat(this.addInput.value);
                this.addInput.value = '';
                this.access(params, 'POST');
            }
        },
        clickCheckbox: function(itemName, checked){
            let params = this.params.concat(itemName);
            params = params.concat(checked);
            this.access(params, 'PUT');
        },
        closeModal: function(){
            this.modal.show = false;
            this.modal.confirmInputValue = '';
        },
        clickCategoryOrGroupDelete: function(){
            this.modal.deleteItemName = this.params.slice(-1)[0];
            this.modal.isDeleteStock = false;
            this.modal.show = true;
        },
        clickStockDelete: function(itemName){
            this.modal.deleteItemName = itemName;
            this.modal.isDeleteStock = true;
            this.modal.show = true;
        },
        clickDeleteOnModal: function(){
            if(this.modal.isDeleteStock){
                const param = this.params.concat(this.modal.deleteItemName);
                this.access(param, 'DELETE');
            }else{
                switch(this.data.type){
                case 'groups':
                    this.access(this.params, 'DELETE', true);
                    this.$router.push({name: 'index'});
                    break;
                case 'stocks':
                    this.access(this.params, 'DELETE', true);
                    this.$router.push({name: 'inCategory', params: {category: this.params[0]}});
                    break;
                }
            }
            this.closeModal();
        }
    },
    computed: {
        filteredItems: function(){
            if(this.data.data === undefined){
                return [];
            }
            return this.data.data.filter((item) => {
                return item.name.toLowerCase().includes(this.filterValue.toLowerCase());
            });
        },
        title: function(){
            switch(this.data.type){
            case 'categories':
                return '在庫管理';
            case 'groups':
                return this.params[0];
            case 'stocks':
                if(this.params[1] === undefined){
                    return this.params[0];
                }
                return `${this.params[0]} > ${this.params[1]}`;
            }
        },
        columnName1: function(){
            switch(this.data.type){
            case 'categories':
                return 'カテゴリー';
            case 'groups':
                return 'グループ名';
            case 'stocks':
                return '名前';
            }
        },
        columnName2: function(){
            switch(this.data.type){
            case 'categories':
                return '件数';
            case 'groups':
                return '所持 / 全件数';
            case 'stocks':
                return '所持';
            }
        },
        columnWidth: function(){
            switch(this.data.type){
            default:
                return 10;
            case 'categories':
                return 9;
            }
        },
        inputPlaceholder: function(){
            switch(this.data.type){
            case 'categories':
                return 'カテゴリー名';
            case 'groups':
                return 'グループ名';
            case 'stocks':
                return '名前';
            }
        },
        deleteText: function(){
            switch(this.data.type){
            case 'groups':
                return 'カテゴリー削除';
            case 'stocks':
                return 'グループ削除';
            }
        },
        editText: function(){
            if(this.editing){
                return '完了';
            }else{
                return '編集';
            }
        }
    }
});

