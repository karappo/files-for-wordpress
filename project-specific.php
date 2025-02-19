<?php

// [NOTICE!]
// こちらの関数は、直接このファイルを読み込まず、function.phpに直接コピペして個別のプロジェクトごとに調整してください。

// ==========================================================
//
// KarappAdmin以外のユーザーの時、サイドバーのメニューから不要なものを非表示

function is_karappo_admin() {
  $current_user = wp_get_current_user();
  return $current_user->user_login == 'KarappoAdmin';
}

function custom_admin_menu() {
  if(!is_karappo_admin()) {
    remove_submenu_page('index.php', 'update-core.php'); //更新
    remove_menu_page('edit-comments.php'); // コメント
    remove_menu_page('themes.php'); // 外観
    remove_menu_page('plugins.php'); // プラグイン
    remove_menu_page('tools.php'); // ツール
    remove_menu_page('options-general.php'); // 設定
    remove_menu_page('edit.php' ); // 投稿を非表示
    remove_menu_page('edit.php?post_type=page'); // 固定ページ
    //remove_menu_page('upload.php'); // メディア
    //remove_submenu_page( 'upload.php', 'media-new.php' ); //新規追加
    remove_menu_page('edit.php?post_type=acf-field-group'); // ACF
    remove_menu_page('wpcf7'); // Contact Form 7
  }
  echo '';
}
add_action( 'admin_menu', 'custom_admin_menu' );

// ==========================================================
//
// Gutenbergエディタから使用するブロックだけを表示
// ブロックタイプはこちらを参照
// https://wpqw.jp/wordpress/block-editor/remove-blocks58/

function my_plugin_allowed_block_types_all( $allowed_block_types, $block_editor_context ) {
  // 許可するブロックタイプ
  $allowed_block_types = array(
    'core/paragraph',
    'core/image',
    'core/gallery',
    'core/embed'
  );
  return $allowed_block_types;
}
add_filter( 'allowed_block_types_all', 'my_plugin_allowed_block_types_all', 10, 2 );

// embedの中身をjs側で削除
// 詳細は、remove-block.jsを参照
add_action( 'enqueue_block_editor_assets', function() {
  wp_enqueue_script( 'remove-block', get_template_directory_uri().'/karappo-common/remove-block.js', array(), false, true );
} );

// ==========================================================
//
// デバッグ用： 管理画面に現在登録されているブロックタイプを出力

// add_action('admin_init', function() {
//     $block_registry = WP_Block_Type_Registry::get_instance();
//     $all_blocks = $block_registry->get_all_registered();

//     echo '<pre>';
//     foreach ($all_blocks as $block_name => $block_type) {
//         echo esc_html($block_name) . "\n";
//     }
//     echo '</pre>';
// });

// ==========================================================
//
// 「投稿」をリネーム

function change_admin_menu() {
  global $menu;
  global $submenu;

  // 「投稿」をリネーム
  $menu[5][0] = 'HELLO LETTER';
  $submenu['edit.php'][5][0] = 'HELLO LETTER';        // "Post"
  $submenu['edit.php'][10][0] = 'HELLO LETTERを追加';  // "Add Post"
  // $submenu['edit.php'][15][0] = 'Status';      // "Categories"
  $submenu['edit.php'][16][0] = 'ジャンル';      // "Tags"
  echo '';
}
add_action( 'admin_menu', 'change_admin_menu' );
function change_post_object_label() {
  global $wp_post_types;
  $labels = &$wp_post_types['post']->labels;
  $labels->name = 'HELLO LETTER';
  $labels->singular_name = 'HELLO LETTER';
  $labels->add_new = '新規HELLO LETTERを追加';
  $labels->edit_item = 'HELLO LETTERを編集';
  $labels->new_item = 'HELLO LETTER';
  $labels->view_item = 'HELLO LETTERを編集';
  $labels->search_items = 'HELLO LETTERを探す';
  $labels->not_found = 'HELLO LETTERはありません';
  $labels->not_found_in_trash = 'ゴミ箱にHELLO LETTERはありません';
}
add_action( 'init', 'change_post_object_label' );

// ==========================================================
//
// アイキャッチ画像を有効にする。

add_theme_support('post-thumbnails');

// ==========================================================
//
// カスタムポストタイプで「revisions」を有効化

function my_custom_revision() {
  add_post_type_support( 'achievement', 'revisions' );
  add_post_type_support( 'news', 'revisions' );
}
add_action('init', 'my_custom_revision');


// ==========================================================
//
// カスタムポストタイプ

// src: https://gist.github.com/naokazuterada/5556068

// ------------------------
// CustomPostType: news
// ------------------------

add_action('init', 'addCPT_news');
function addCPT_news(){
  $cpt_name = 'news';
  // [A]ラベル
  $labels = array(
    'name' => 'News',
    'singular_name' => 'News',
    'add_new' => 'Newsを追加',
    'add_new_item' => '新しいNewsを追加',
    'edit_item' => 'Newsを編集',
    'new_item' => '新しいNews',
    'view_item' => 'Newsを表示',
    'search_items' => 'Newsを探す',
    'not_found' => 'Newsはありません',
    'not_found_in_trash' => 'ゴミ箱にNewsはありません',
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'has_archive' => true,
    'show_in_rest' => true,
    'supports' => array(
      'title',
      'editor',
      'thumbnail'
    ),
  );
  register_post_type($cpt_name, $args);

  // [E]分類
  register_taxonomy(
    $cpt_name.'-category', // [D]
    $cpt_name,
    array(
      'label' => 'カテゴリー', // [D]
      'show_ui' => true,
      'hierarchical' => true,
      'rewrite'  => array(
        'slug' => "$cpt_name"
      ),
      'show_in_rest' => true,
    )
  );
}

// ==========================================================
//
// 管理画面の特定のページに説明文を表示

function displayCategoryNotice() {
  $screen = get_current_screen();
  if ($screen->id === 'edit-category') {
    echo '<div class="notice notice-info" style="padding: 10px; margin-top: 15px;">
            <p>
              <strong>サイト上での表示</strong><br>
              <ul style="list-style: disc; margin-left: 2em;"">
              <li><a href="/" target="_blank">トップページ</a>の「HELLO LETTER」セクションと<a href="/hello-letter/" target="_blank">HELLO LETTERページ</a>の「PICK UP CATEGORY」セクションには、このページの一覧の上位4つまでが表示されます。</li>
              <li><a href="/hello-letter/categories/" target="_blank">HELLO LETTERのカテゴリー一覧ページ</a>での表示順は作成日順で、このページの並び順とは無関係です。</li>
              </ul>
              <strong>並び替えの方法</strong><br>
              <ul style="list-style: disc; margin-left: 2em;">
                <li>各行をドラッグ&ドロップで並び替え可能です。</li>
              </ul>
            </p>
          </div>';
  } else if ($screen->id === 'edit-post') {
    echo '<div class="notice notice-info" style="padding: 10px; margin-top: 15px;">
            <p>
              HELLO LETTERのトップページで先頭に大きく表示したい記事は、<a href="/wp/wp-admin/post.php?post=186&action=edit">こちら</a>の「PICK UP 投稿」で設定してください。
            </p>
          </div>';
  }
}
add_action('admin_notices', 'displayCategoryNotice');

// ==========================================================
//
// wp_headでhead>titleを出力しない（head.blade.phpで一元管理する）

remove_action( 'wp_head', '_wp_render_title_tag', 1 );

// ==========================================================
//
// デフォルトでは2560px以上の画像は画像名に"-scaled"が付与されて縮小保存される機能に関して…
// 1. 閾値を変更したい場合
function change_big_image_size_threshold( $threshold ) {
  return 2048;
}
add_filter('big_image_size_threshold', 'change_big_image_size_threshold', 999, 1);
// 2. 機能自体を無効化したい場合
add_filter( 'big_image_size_threshold', '__return_false' );

// ==========================================================
//
// サムネイルサイズを削除

function remove_thumbnail_sizes( $new_sizes ) {

  // この二つは管理画面でも使うのでそのまま残すのが無難
  // unset( $new_sizes['thumbnail'] ); // 150x150 ピクセル（切り取ってサイズにフィット）
  // unset( $new_sizes['medium'] ); // 300x300 ピクセル（比率を維持したまま指定サイズにおさめる）

  // これ以降は要判断
  unset( $new_sizes['medium_large'] ); //　768x0 ピクセル（比率を維持したまま指定サイズにおさめる）
  unset( $new_sizes['large'] ); // 1024x1024 ピクセル（比率を維持したまま指定サイズにおさめる）
  unset( $new_sizes['1536x1536'] ); // 1536x1536 ピクセル（比率を維持したまま指定サイズにおさめる）
  unset( $new_sizes['2048x2048'] ); // 2048x2048 ピクセル（比率を維持したまま指定サイズにおさめる）

  return $new_sizes;
}
add_filter('intermediate_image_sizes_advanced', 'remove_thumbnail_sizes');

// ==========================================================
//
// サムネイルサイズを追加

add_image_size('picture', 163, 163, true);
add_image_size('picture-2x', 326, 326, true);

// ==========================================================
//
// 大きすぎる画像は容量削減のために自動削除
// （big_image_size_thresholdでscaleされていない画像は、リサイズ後に削除）

function txt_domain_delete_fullsize_image($metadata) {
  // for debug
  // ob_start();
  // var_dump( $metadata );
  // $test = ob_get_contents();
  // ob_end_clean();
  // error_log( $test );
  $upload_dir = wp_upload_dir();
  $full_image_path = trailingslashit($upload_dir['basedir']) . $metadata['file'];
  $original_image_path = preg_replace('/\-scaled\./', '.', $full_image_path);
  if ($full_image_path !== $original_image_path) {
    // 縮小されていたらもとの画像はより大きいサイズなので削除
    unlink($original_image_path);
    unset( $metadata['original_image'] );
  }
  return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'txt_domain_delete_fullsize_image');

