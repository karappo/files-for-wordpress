# Karappo Common files for WordPress

- WordPressのプロジェクトで共通で使えるヘルパーなどをまとめて、共通化する目的です。
- common.php と helpers.php は、全プロジェクト共通、project-specific.php はプロジェクト毎に調整して使用してください。

## Getting started

1. WordPressのプロジェクトをセットアップした状態から、themeディレクトリ直下にサブモジュールとして追加
  ```
  git submodule add git@github.com:karappo/files-for-wordpress.git wp/wp-content/themes/{project}/karappo-common
  ```
2. {project}/functions.php で外部ファイルの読み込み設定を追加
  ```php
  require_once('karappo-common/common.php');
  require_once('karappo-common/helpers.php');

  // デフォルト設定の上書き（必要に応じて）
  karappo_common_config(['breakpoint' => 700]);
  ```
3. その他、各プロジェクトごとにカスタマイズが必要なものは、 project-specific.php にまとめているので、目を通して必要に応じて設定

## CLI tools

### `bin/db` — DB バックアップ / リストア / ホスト置換

タイムスタンプ付きでDBをバックアップし、一覧から選んでリストアできるシェルスクリプト。
karappoの標準的なWPプロジェクト構成（`wp/` 配下にWP本体、`../_assets/database/` にダンプ置き場）でゼロコンフィグ動作します。

#### セットアップ

`package.json` の `scripts` に追加するだけ（`config` 不要）。
WP ディレクトリは cwd から `wp-config.php` を上方向探索して自動検出するので、どこから実行してもOKです。

テーマの `package.json`（`wp/wp-content/themes/{theme}/package.json`）に書く例:

```json
{
  "scripts": {
    "db:export":  "karappo-common/bin/db export",
    "db:import":  "karappo-common/bin/db import",
    "db:replace": "karappo-common/bin/db replace",
    "db:ls":      "karappo-common/bin/db ls"
  }
}
```

プロジェクトルートの `package.json` に書く場合はパスを `wp/wp-content/themes/{theme}/karappo-common/bin/db` に置き換えてください。

#### 使い方

```sh
pnpm db:export                              # ../_assets/database/dev-YYYYMMDD-HHMMSS.sql に書き出し
pnpm db:import                              # 一覧から選択して復元 (fzf推奨)
pnpm db:import latest                       # 最新を復元
pnpm db:import path/to/file.sql             # ファイル指定で復元
pnpm db:replace example.test example.com    # wp search-replace のラッパー (host はリテラル指定)
pnpm db:ls                                  # バックアップ一覧
```

#### 設定のカスタマイズ

デフォルトと違うパスを使いたい場合は env か `package.json` の `config` で上書き:

| 設定 | 環境変数 | package.json | デフォルト |
|------|---------|--------------|----------|
| WP ディレクトリ | `KARAPPO_DB_WP_PATH` | `config.wp_path` | cwd から `wp-config.php` を上方向探索 |
| バックアップ先 (WPからの相対) | `KARAPPO_DB_BACKUP_DIR` | `config.backup_dir` | `../_assets/database` |

#### fzf のインストール（推奨）

`db import` の選択UIが劇的に快適になります:

```sh
# macOS
brew install fzf

# Ubuntu/Debian
sudo apt install fzf
```

未インストールでも標準の `select` プロンプトで動作します。
