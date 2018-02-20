<?php

class HtmlUtils{

    public static function getScriptTag($GET){
        $catName = HtmlUtils::getCatName($GET);
        $groupName = HtmlUtils::getGroupName($GET);
        if($catName and $groupName){
            return '<script src="./assets/js/stock.js" charset="UTF-8"></script>';
        }else if($catName){
            return '<script src="./assets/js/stockGroup.js" charset="UTF-8"></script>';
        }else{
            return '<script src="./assets/js/category.js" charset="UTF-8"></script>';
        }
    }

    public static function getShowTableScript($GET){
        $catName = HtmlUtils::getCatName($GET);
        $groupName = HtmlUtils::getGroupName($GET);
        $escCatName = str_replace("'", "\\'", $catName);
        $escGroupName = str_replace("'", "\\'", $groupName);
        if($catName and $groupName){
            return "<h1><a href=\"./?cat=${catName}\">${catName}</a> > {$GET['group']}</h1>
                <script>
                const CATEGORY_NAME = '${escCatName}';
                const GROUP_NAME = '${escGroupName}';
                echoStocks();
                </script>";
        }else if($catName){
            return "<h1>${catName}</h1>
                <script>
                const CATEGORY_NAME = '${escCatName}';
                echoStockGroups();
                </script>";
        }else{
            return '<h1>在庫管理</h1>
                <script>echoCategories();</script>';
        }
    }

    public static function getFootHtml($GET){
        $catName = HtmlUtils::getCatName($GET);
        $groupName = HtmlUtils::getGroupName($GET);
        if($catName and $groupName){
            $escName = str_replace("'", "\\'", $groupName);
            return '<div class="footButtons">
                <button type="button" class="btn btn-warning" id="editBtn"
                onclick="clickEditStock();">編集</button>
                &emsp;
                <button type="button" class="btn btn-danger"
                onclick="delGroup();">グループ削除</button>
                </div>';
        }else if($catName){
            return '<div class="footButtons">
                <button type="button" class="btn btn-danger"
                onclick="delCategory();">カテゴリー削除</button>
                </div>';
        }
    }

    private static function getCatName($GET){
        return (isset($GET['cat']) ? $GET['cat'] : null);
    }

    private static function getGroupName($GET){
        return (isset($GET['group']) ? $GET['group'] : null);
    }

}
