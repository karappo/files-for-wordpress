# Karappo Common files for WordPress

- WordPressのプロジェクトで共通で使えるヘルパーなどをまとめて、共通化する目的です。

## Getting started

1. WordPressのプロジェクトをセットアップした状態から、themeディレクトリ直下にサブモジュールとして追加
  ```
  git submodule add git@github.com:karappo/files-for-wordpress.git wp/wp-content/themes/<project>/karappo-common
  ```
2. <project>/functions.php で外部ファイルの読み込み設定を追加
  ```
  require_once('karappo-common/common.php');
  require_once('karappo-common/helpers.php');
  ```
