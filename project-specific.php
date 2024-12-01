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

