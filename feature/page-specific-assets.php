<?php

// ==========================================================
//
// ページごとのスタイルとスクリプトを読み込む

function enqueue_page_scripts() {
  $page_data = get_query_var('page_data');
  if (!$page_data) {
    $page_data = [];
  }

  if (isset($page_data['css']) && is_array($page_data['css'])) {
    array_unshift($page_data['css'], 'common.css'); // 先頭にcommon.css
  } else {
    $page_data['css'] = ['common.css'];
  }
  foreach ($page_data['css'] as $style) {
    // スタイルシートを読み込む
    wp_enqueue_style(
      $style,
      get_template_directory_uri() . '/assets/css/' . $style,
      [], // 依存関係があれば指定
      null // バージョン番号（必要に応じて指定）
    );
  }
  if (isset($page_data['js']) && is_array($page_data['js'])) {
    array_unshift($page_data['js'], 'common.js'); // 先頭にcommon.jsを追加
  } else {
    $page_data['js'] = ['common.js'];
  }
  foreach ($page_data['js'] as $script) {
    // スクリプトを結合して読み込む
    wp_enqueue_script(
        $script,
        get_template_directory_uri() . '/assets/js/dist/' . $script,
        ['jquery'], // jQueryに依存
        null,
        true // フッターで読み込む
    );
  }
}
add_action('wp_enqueue_scripts', 'enqueue_page_scripts');