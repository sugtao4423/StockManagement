# API
## Nginx Configuration
```
location /api {
    index api.php;
    try_files $uri $uri/ /api/api.php?$query_string;
}
```

## Resources
### /
**Methods**
* GET: `カテゴリー一覧` `カテゴリー内のグループ数` 返却

### /:categoryName
**Methods**
* POST: カテゴリーを登録し `/` のGETを返却
* GET: カテゴリー内にあるグループの `名前` `アイテムの合計` `所持しているアイテムの合計` 返却
* DELETE: カテゴリーを削除し `/` のGETを返却

### /:categoryName/:groupName
**Methods**
* POST: グループを登録し `/:categoryName` のGETを返却
* GET: グループ内にあるアイテムの `id` `名前` `所持の状態(boolean)` 返却
* DELETE: グループを削除し `/:categoryName` のGETを返却

### /:categoryName/:groupName/:stockId
* `stockId`: アイテムID

**Methods**
* DELETE: アイテムを削除し `/:categoryName/:groupName` のGETを返却

### /:categoryName/:groupName/:stockId/:have
* `stockId`: アイテムID
* `have`: 所持の状態。（boolean or 0/1）

**Methods**
* PUT: 所持の状態を更新し `/:categoryName/:groupName` のGETを返却
