<?php

// ==========================================================
//
// 一括操作に「公開」「非公開」を追加（KarappoAdmin専用）
//
// 使い方:
// ```functions.php
// require_once get_template_directory() . '/karappo-common/feature/bulk-status-actions.php';
// register_bulk_status_actions(['news', 'activity', 'classes']);
// ```

function register_bulk_status_actions($post_types) {
  if (!function_exists('is_karappo_admin') || !is_karappo_admin()) {
    return;
  }

  $add_actions = function($bulk_actions) {
    $bulk_actions['publish'] = '公開';
    $bulk_actions['make_private'] = '非公開';
    return $bulk_actions;
  };

  $handle_actions = function($redirect_to, $doaction, $post_ids) {
    $status_map = [
      'publish'      => 'publish',
      'make_private' => 'private',
    ];
    if (!isset($status_map[$doaction])) {
      return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
      wp_update_post([
        'ID'          => $post_id,
        'post_status' => $status_map[$doaction],
      ]);
    }
    return add_query_arg('bulk_status_changed', count($post_ids) . '_' . $doaction, $redirect_to);
  };

  foreach ($post_types as $cpt) {
    add_filter("bulk_actions-edit-{$cpt}", $add_actions);
    add_filter("handle_bulk_actions-edit-{$cpt}", $handle_actions, 10, 3);
  }

  add_action('admin_notices', function() {
    if (!empty($_REQUEST['bulk_status_changed'])) {
      list($count, $action) = explode('_', $_REQUEST['bulk_status_changed'], 2);
      $label = $action === 'publish' ? '公開' : '非公開に';
      printf('<div class="notice notice-success is-dismissible"><p>%d件の投稿を%sしました。</p></div>', intval($count), $label);
    }
  });
}
