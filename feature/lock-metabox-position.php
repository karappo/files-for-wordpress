<?php

// ==========================================================
//
// メタボックス（ACFフィールドグループ等）の配置を固定する
//
// 編集画面でメタボックスをドラッグしたり、見出し右の上下矢印ボタンで動かすと、
// その並び順が user_meta 'meta-box-order_{画面ID}' に保存され、以後 ACF 等の
// 位置設定（サイドバー/本文下）より優先される。管理画面から元に戻す手段がなく、
// 意図せず動かしてしまう事故が起きやすいため、操作UIを隠して固定する。
//
// 使い方:
// ```functions.php
// require_once get_template_directory() . '/karappo-common/feature/lock-metabox-position.php';
// lock_metabox_position();
//
// // 既に動かされてしまった環境を元の配置に戻す場合
// lock_metabox_position(['ignore_saved_order' => true]);
// ```

function lock_metabox_position($args = array()) {
  $args = array_merge(array(
    // 保存済みの並び順（user_meta）を無視し、常に ACF 等の設定どおりの位置で描画する。
    // 既にユーザーが動かしてしまった画面を元に戻したい場合に true にする。
    // なお user_meta 自体は残るので、false に戻すとズレも戻る点に注意。
    'ignore_saved_order' => false,
  ), $args);

  // 並び替えボタン（見出し右の上下矢印）を非表示に。開閉トグルは残す
  // あわせて、ドラッグできる見た目（cursor: move）も打ち消す
  $hide_order_buttons = function () {
    ?>
    <style>
      .postbox .handle-order-higher,
      .postbox .handle-order-lower {
        display: none;
      }
      /* .hndle は .postbox-header の子なので子孫セレクタで指定する（cursor: move を打ち消す） */
      .meta-box-sortables .postbox-header,
      .meta-box-sortables .postbox .hndle {
        cursor: pointer; /* 開閉はできるので default ではなく pointer */
      }
    </style>
    <?php
  };
  add_action('admin_head-post.php', $hide_order_buttons);
  add_action('admin_head-post-new.php', $hide_order_buttons);

  // ドラッグ操作を無効化（開閉トグルは従来どおり使える）
  //
  // postboxes.js が .meta-box-sortables に張った jQuery UI sortable の mousedown を
  // キャプチャ段階で止める。ブロックエディタではメタボックス領域がエディタの描画後に
  // 生成され sortable の初期化タイミングが読めないため、sortable('disable') のように
  // 初期化済みであることを前提とする方法は使わない。
  // 開閉は click イベントなので、mousedown を止めても従来どおり動く。
  $disable_sorting = function () {
    ?>
    <script>
    document.addEventListener('mousedown', function (e) {
      if (!e.target || !e.target.closest) {
        return;
      }
      if (e.target.closest('.meta-box-sortables .postbox-header, .meta-box-sortables .postbox .hndle')) {
        e.stopPropagation();
      }
    }, true);
    </script>
    <?php
  };
  add_action('admin_footer-post.php', $disable_sorting);
  add_action('admin_footer-post-new.php', $disable_sorting);

  if (!$args['ignore_saved_order']) {
    return;
  }

  add_action('admin_init', function () {
    foreach (get_post_types(array('show_ui' => true)) as $post_type) {
      add_filter("get_user_option_meta-box-order_{$post_type}", '__return_false');
    }
  });
}
