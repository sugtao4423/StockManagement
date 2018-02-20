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

require_once('./htmlUtils.php');
?>
<!DOCTYPE HTML>
<html lang="ja">
  <head>
    <title>Stock Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link href="./assets/style.css" rel="stylesheet">
    <script src="./assets/js/utils.js" charset="UTF-8"></script>
    <?= HtmlUtils::getScriptTag($_GET); ?>
  </head>
  <body>
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href=".">在庫管理</a>
            </div>
            <div class="navbar-form navbar-right">
                <input class="form-control navbar-right" id="searchBox" type="text" onKeyUp="searchText(this)" />
            </div>
        </div>
    </nav>

    <div class="container">
        <?= HtmlUtils::getShowTableScript($_GET); ?>
        <div id="content"></div>
        <?= HtmlUtils::getFootHtml($_GET); ?>
    </div>

  </body>
</html>
