<?php

/*
使い方
```function.php
require_once('karappo-common/common.php');
require_once('karappo-common/helpers.php');
```
*/

// ==========================================================
// セキュリティ系

// ----------------------------------------------------------
// ファイル編集禁止

define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true); // 管理画面からのプラグイン/テーマ/コアの追加・更新・削除を禁止

// ----------------------------------------------------------
// 自動更新はコアのみ許可する
//
// DISALLOW_FILE_MODS は管理画面からのファイル改変を禁じるためのものだが、副作用で
// コアのセキュリティ自動更新まで止まってしまう。
// 2026-07 の wp2shell (CVE-2026-63030) では、WordPress が配信した緊急の強制自動更新を
// これが原因で受け取れず、脆弱なまま放置されて侵害された事例が出た。
// 自動更新のコンテキストに限りファイル変更を許可し、セキュリティ修正を受け取れるようにする。
//
// コアの自動更新はデフォルトでマイナー（セキュリティ・メンテナンス）リリースのみが対象。
// サーバ側で更新が入るとリポジトリと乖離するが、github-deploy がデプロイ前にバージョンを
// 照合して中断するため、rsync --delete による巻き戻りは起きない。
//
// 一方プラグイン・テーマ・翻訳は、git 管理していてもデプロイで無言のうちに巻き戻るため
// （照合の対象外）、自動更新から明示的に外す。

add_filter('file_mod_allowed', function ($allowed, $context) {
  return in_array($context, ['automatic_updater', 'wp_auto_update_core'], true) ? true : $allowed;
}, 10, 2);

add_filter('auto_update_plugin', '__return_false');
add_filter('auto_update_theme', '__return_false');
add_filter('auto_update_translation', '__return_false');

// ----------------------------------------------------------
// xmlrpc.phpを無効化

add_filter('xmlrpc_enabled', '__return_false', 10);

// デフォルトで無効化しておくが、各プロジェクトごとに再有効かしたい場合は下記をそれぞのれのfunction.phpに追記して上書きすること。
// add_filter('xmlrpc_enabled', '__return_false', 20);


// ==========================================================
// その他

// ----------------------------------------------------------
// グローバル変数を定義
// helpers.phpなどで使用する

$GLOBALS['is_test_environment'] = preg_match('/\.test$/', $_SERVER['HTTP_HOST'] ?? '');

/*
判定方法を変えたい場合は、各プロジェクトのfunctions.phpで調整
```function.php
require_once('karappo-common/common.php');
$GLOBALS['is_test_environment'] = preg_match('/\.test$/', $_SERVER['HTTP_HOST'] ?? '');
```
*/

// ----------------------------------------------------------
// 日本語ファイル名のアップロードを禁止
function restrict_japanese_filenames($file) {
    $filename = $file['name'];
    // 日本語の文字が含まれているか確認
    if (preg_match('/[ぁ-んァ-ヶ一-龠]/u', $filename)) {
        $file['error'] = '日本語ファイル名のファイルはアップロードできません。英数字のみにしてください。';
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'restrict_japanese_filenames');

// ----------------------------------------------------------
// アップロード画像の拡張子を正規化（小文字化 + jpeg → jpg）
// 一部の SNS（X 等）の OGP クローラは .jpeg や大文字拡張子だとカードを生成しないことがあるため、
// アップロード時にファイル名の拡張子を小文字に統一し、jpeg/jpe は jpg に変換する。
function normalize_upload_file_extension($file) {
    $dot = strrpos($file['name'], '.');
    if ($dot === false) return $file;

    $base = substr($file['name'], 0, $dot);
    $ext  = strtolower(substr($file['name'], $dot + 1));

    if ($ext === 'jpeg' || $ext === 'jpe') $ext = 'jpg';

    $file['name'] = $base . '.' . $ext;
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'normalize_upload_file_extension');

// ----------------------------------------------------------
// 「ブログのトップに固定」を非表示
// これをしておかないと、不用意にチェックされて、posts_per_page が機能しなくなるため

//「投稿一覧」の「クイック編集」で表示される「この投稿を先頭に固定表示」を非表示
function hide_quick_page_sticky() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $(".inline-edit-col-right .inline-edit-group:eq(1) label:eq(1)").css("display","none");
        });
    </script>
    <?php
}
add_action( 'admin_head-edit.php', 'hide_quick_page_sticky' );

//「投稿の編集」で表示される「ブログのトップに固定」を非表示
function hide_post_page_sticky() {
    ?>
    <style type="text/css">
        .edit-post-post-status .components-panel__row:nth-of-type(3) {display:none !important;}
    </style>
    <?php
}
add_action( 'admin_print_styles-post.php', 'hide_post_page_sticky' );

//「新規投稿の追加」で表示される「ブログのトップに固定」「レビュー待ち」を非表示
function hide_postnew_page_sticky() {
    ?>
    <style type="text/css">
        .edit-post-post-status .components-panel__row:nth-of-type(n+3) {display:none !important;}
    </style>
    <?php
}
add_action( 'admin_print_styles-post-new.php', 'hide_postnew_page_sticky' );

