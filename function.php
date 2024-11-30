<?php

// ==========================================================
//
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

// ==========================================================
//
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

?>
