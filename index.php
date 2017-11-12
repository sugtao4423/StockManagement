<?php
require_once('./api/Config.php');
require_once('./api/Utils.php');

if(Config::$USE_AUTHORIZE){
    if (!isset($_SERVER['PHP_AUTH_USER'])){
        header('WWW-Authenticate: Basic realm="Enter username and password."');
        http_response_code(401);
        die('<html lang="ja"><meta charset="utf-8">ログインが必要です。</html>');
    }else{
        if ($_SERVER['PHP_AUTH_USER'] != Config::$USERNAME || $_SERVER['PHP_AUTH_PW'] != Config::$PASSWORD){
            header('WWW-Authenticate: Basic realm="Enter username and password."');
            http_response_code(401);
            die('<html lang="ja"><meta charset="utf-8">ログインが必要です。</html>');
        }
    }
}
?>
<!doctype html>
<html lang="ja">
  <head>
    <title>Stock Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link href="./assets/style.css" rel="stylesheet">
    <script src="./assets/js/utils.js" charset="UTF-8"></script>
    <?php
        $catName = isset($_GET['cat']) ? $_GET['cat'] : null;
        $groupName = isset($_GET['group']) ? $_GET['group'] : null;
        if(!$catName){
            echo '<script src="./assets/js/category.js" charset="UTF-8"></script>';
        }else{
            if(!$groupName)
                echo '<script src="./assets/js/stockGroup.js" charset="UTF-8"></script>';
            else
                echo '<script src="./assets/js/stock.js" charset="UTF-8"></script>';
        }
    ?>
  </head>
  <body>
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href=".">在庫管理</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php
            global $catName, $groupName;
            if(!$catName){
                echo '<h1>在庫管理</h1>';
                echo '<script>echoCategories();</script>';
            }else{
                $escCatName = str_replace("'", "\\'", $catName);
                if(!$groupName){
                    echo "<h1>${catName}</h1>";
                    echo '<script>';
                        echo "const CATEGORY_NAME = '${escCatName}';";
                        echo 'echoStockGroups();';
                    echo '</script>';
                }else{
                    $escGroupName = str_replace("'", "\\'", $groupName);
                    echo "<h1>${catName} > {$_GET['group']}</h1>";
                    echo '<script>';
                        echo "const CATEGORY_NAME = '${escCatName}';";
                        echo "const GROUP_NAME = '${escGroupName}';";
                        echo "echoStocks('${escGroupName}');";
                    echo '</script>';
                }
            }
        ?>
        <div id="content"></div>
        <?php
            global $catName, $groupName;
            if($catName and $groupName){
                $escName = str_replace("'", "\\'", $groupName);
                echo '<div style="float:right; margin-top:40px;">';
                echo '<button type="button" class="btn btn-warning" id="editBtn"';
                echo "onclick=\"clickEditStock('${escName}');\">編集</button>";
                echo '&emsp;';
                echo '<button type="button" class="btn btn-danger"';
                echo "onclick=\"delGroup('${escName}');\">グループ削除</button>";
                echo '</div>';
            }
        ?>
    </div>

  </body>
</html>
