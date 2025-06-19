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

  // サイドバーに特定の固定ページ（例としてid:8）の編集画面へのリンクを追加
  add_menu_page(
    '特定の固定ページ編集',
    '○○ページ',
    'edit_pages', // 固定ページの編集権限を持つユーザーにのみ表示
    'post.php?post=8&action=edit', // 編集画面へのリンク
    '',
    'dashicons-edit',
    20
  );

  echo '';
}
add_action( 'admin_menu', 'custom_admin_menu' );

// ==========================================================
//
// KarappAdmin以外のユーザーの時、管理バーの上部に表示される項目を非表示

function my_remove_adminbar_menu($wp_admin_bar) {
  if(!is_karappo_admin()) {
    $wp_admin_bar->remove_menu('wp-logo');      // WPロゴ
    // $wp_admin_bar->remove_menu('site-name');    // サイト名
    $wp_admin_bar->remove_menu('view-site');    // サイト名 -> サイトを表示
    $wp_admin_bar->remove_menu('plugins');      // サイト名 -> プラグイン
    $wp_admin_bar->remove_menu('dashboard');    // サイト名 -> ダッシュボード (公開側)
    $wp_admin_bar->remove_menu('themes');       // サイト名 -> テーマ (公開側)
    $wp_admin_bar->remove_menu('customize');    // サイト名 -> カスタマイズ (公開側)
    $wp_admin_bar->remove_menu('comments');     // コメント
    $wp_admin_bar->remove_menu('updates');      // 更新
    $wp_admin_bar->remove_menu('view');         // 投稿を表示
    $wp_admin_bar->remove_menu('new-content');  // 新規
    $wp_admin_bar->remove_menu('new-post');     // 新規 -> 投稿
    $wp_admin_bar->remove_menu('new-media');    // 新規 -> メディア
    $wp_admin_bar->remove_menu('new-link');     // 新規 -> リンク
    $wp_admin_bar->remove_menu('new-page');     // 新規 -> 固定ページ
    $wp_admin_bar->remove_menu('new-user');     // 新規 -> ユーザー
    // $wp_admin_bar->remove_menu('my-account');   // マイアカウント
    // $wp_admin_bar->remove_menu('user-info');    // マイアカウント -> プロフィール
    // $wp_admin_bar->remove_menu('edit-profile'); // マイアカウント -> プロフィール編集
    // $wp_admin_bar->remove_menu('logout');       // マイアカウント -> ログアウト
    $wp_admin_bar->remove_menu('search');       // 検索 (公開側)
  }
}
add_action('admin_bar_menu', 'my_remove_adminbar_menu', 201);

// ==========================================================
//
// 固定ページの一覧ページにきたとしたら、特定の固定ページ（例としてid:8）へリダイレクト

function redirect_pages_list_for_specific_users() {
  if (!is_karappo_admin() && is_admin()) {
    global $pagenow;
    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'page') {
      wp_redirect(admin_url('post.php?post=8&action=edit'));
      exit;
    }
  }
}
add_action('admin_init', 'redirect_pages_list_for_specific_users');


// ==========================================================
//
// Gutenbergエディタから使用するブロックだけを表示
// ブロックタイプはこちらを参照
// https://wpqw.jp/wordpress/block-editor/remove-blocks58/

function my_plugin_allowed_block_types_all( $allowed_block_types, $block_editor_context ) {
  // 許可するブロックタイプ
  $allowed_block_types = [
    'core/paragraph',
    'core/image',
    'core/gallery',
    'core/embed'
  ];
  return $allowed_block_types;
}
add_filter( 'allowed_block_types_all', 'my_plugin_allowed_block_types_all', 10, 2 );

// embedの中身をjs側で削除
// 詳細は、remove-block.jsを参照
add_action( 'enqueue_block_editor_assets', function() {
  wp_enqueue_script( 'remove-block', get_template_directory_uri().'/karappo-common/remove-block.js', [], false, true );
} );

// ==========================================================
//
// Gutenbergエディタから特定の見出しレベルを削除
// https://ja.wordpress.org/team/handbook/block-editor/how-to-guides/curating-the-editor-experience/disable-editor-functionality/

function modify_heading_levels_globally($args, $block_type) {
  if ('core/heading' !== $block_type) {
    return $args;
  }
  // H1, H5, H6 を削除
  $args['attributes']['levelOptions']['default'] = [2, 3, 4];
  return $args;
}
add_filter('register_block_type_args', 'modify_heading_levels_globally', 10, 2);


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
  add_post_type_support('__CPT_NAME__', 'revisions');
}
add_action('init', 'my_custom_revision');


// ==========================================================
//
// 管理画面からメタボックスを削除

function remove_metabox() {
  if(!is_karappo_admin()) {
    // リビジョン
    remove_meta_box('revisionsdiv', '__CPT_NAME__', 'normal');
    // ページ属性
    remove_meta_box('pageparentdiv', '__CPT_NAME__', 'side');
    // スラッグ
    // 【注意】 これをしてしまうと、スラッグの変更ができなくなるのでNG！やらないこと！
    // remove_meta_box('slugdiv', '__CPT_NAME__', 'normal');
  }
}
add_action( 'add_meta_boxes', 'remove_metabox', 20 );

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
  $labels = [
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
  ];
  $args = [
    'labels' => $labels,
    'public' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'has_archive' => true,
    'show_in_rest' => true,
    'supports' => [
      'title',
      'editor',
      'thumbnail'
    ]
  ];
  register_post_type($cpt_name, $args);

  // [E]分類
  register_taxonomy(
    $cpt_name.'-category', // [D]
    $cpt_name,
    [
      'label' => 'カテゴリー', // [D]
      'show_ui' => true,
      'hierarchical' => true,
      'rewrite'  => [
        'slug' => "$cpt_name"
      ],
      'show_in_rest' => true,
    ]
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
// CPT:xxxのページタイトル下かつパーマリンクの上に注釈を追加

function add_works_title_note_script() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'xxx') {
        ?>
        <script>
        jQuery(document).ready(function ($) {
            const $slugBox = $('#edit-slug-box');
            if ($slugBox.length) {
                $('<div>', {
                    text: 'タイトルはなるべく英数字にしてください。',
                    css: {
                        fontSize: '13px',
                        color: '#666',
                        marginTop: '6px',
                        marginBottom: '6px'
                    }
                }).insertBefore($slugBox);
            }
        });
        </script>
        <?php
    }
}
add_action('admin_footer-post.php', 'add_works_title_note_script');
add_action('admin_footer-post-new.php', 'add_works_title_note_script');

// ==========================================================
//
// CPT:xxx のパーマリンクに注釈を追加
// （新規作成時には、#edit-slug-boxの中身が空なので、その時は表示せず、パーマリンクが表示された時（表示されている時）にのみ注意書きを表示する）

function add_permalink_note_on_slug_box_update() {
  $screen = get_current_screen();
  if ($screen && $screen->post_type === 'xxx') {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      const slugBox = document.getElementById('edit-slug-box');
      if (!slugBox) return;
      function insertNoteIfNeeded() {
        const text = slugBox.innerText || slugBox.textContent;
        if (text.includes('パーマリンク') && !document.getElementById('custom-slug-note')) {
          const note = document.createElement('div');
          note.innerHTML =
              '↑デフォルトでタイトルそのままになってしまいますが、日本語が含まれている状態では詳細ページが正しく表示されません。<br>' +
              '必ず「編集」を押して、<strong>半角の英数字、ハイフン</strong>に変更をお願いします。';
          note.id = 'custom-slug-note';
          note.style.fontSize = '13px';
          note.style.color = '#666';
          note.style.marginBottom = '8px';
          slugBox.parentNode.insertBefore(note, slugBox.nextSibling);
        }
      }
      insertNoteIfNeeded();
      const observer = new MutationObserver(insertNoteIfNeeded);
      observer.observe(slugBox, { childList: true, subtree: true });
    });
    </script>
    <?php
  }
}
add_action('admin_footer-post.php', 'add_permalink_note_on_slug_box_update');
add_action('admin_footer-post-new.php', 'add_permalink_note_on_slug_box_update');

// ==========================================================
//
// 記事編集画面の右サイドバーの「カテゴリーの追加」を非表示に（旧エディタ用）

function hide_add_category_button() {
  $style = '<style>';

  // 非表示に
  $taxonomies = [
    'visit-condition',
    'visit-duration',
    'visit-tag',
    'visit-category',
    'places-category',
    'magazine-category'
  ];
  $ids = [];
  foreach ($taxonomies as $taxonomy) {
    $ids[] = '#'.$taxonomy.'-tabs';
    $ids[] = '#'.$taxonomy.'-adder';
  }
  $style .= implode(',', $ids) . ' { display: none; }';

  // 線を消す
  $ids = [];
  foreach ($taxonomies as $taxonomy) {
    $ids[] = '#'.$taxonomy.'-all';
  }
  $style .= implode(',', $ids) . ' { border: none; }';

  $style .= '</style>';
  echo $style;
}
add_action('admin_head', 'hide_add_category_button');

// ==========================================================
//
// 記事編集画面の右サイドバーの「カテゴリーの追加」を非表示に（Gutenberg用）

function hide_add_category_button() {
    echo '<style>
        button.editor-post-taxonomies__hierarchical-terms-add {
            display: none !important;
        }
    </style>';
}
add_action('admin_head', 'hide_add_category_button');

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


// ==========================================================
//
// タイトルの改行： [br] を <br>に変換

add_filter('the_title', function($title, $post_id = null) {
  // 投稿IDが指定されていない場合は現在の投稿IDを使用
  if ($post_id === null) {
    $post_id = get_the_ID();
  }

  // 投稿タイプがprojectsの場合のみ変換
  if (get_post_type($post_id) === 'projects') {
    return str_replace('[br]', '<br>', $title);
  }

  return $title;
}, 10, 2);

// 注意書きを追加

function add_works_title_note_script() {
  $screen = get_current_screen();
  if ($screen && $screen->post_type === 'xxx') {
    ?>
    <script>
    jQuery(document).ready(function ($) {
      const $slugBox = $('#edit-slug-box');
      if ($slugBox.length) {
        $('<div>', {
          html: '改行したい箇所には<code>[br]</code>を入力してください。',
          css: {
            fontSize: '13px',
            color: '#666',
            marginTop: '6px',
            marginBottom: '6px'
          }
        }).insertBefore($slugBox);
      }
    });
    </script>
    <?php
  }
}
add_action('admin_footer-post.php', 'add_works_title_note_script');
add_action('admin_footer-post-new.php', 'add_works_title_note_script');
