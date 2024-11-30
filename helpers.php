<?php

// ==========================================================
//
// Helpers

function asset_path($path) {
  return get_template_directory_uri() . "/assets/$path";
}

function image_tag($src, $attrs = '', $return = false) {
  $src = asset_path("image/$src");

  // altが未設定の場合は attrs="" を追加
  if(!preg_match('/alt=".*"/', $attrs)) {
    $attrs .= ' alt=""';
  }

  $res = "<img $attrs src=\"$src\">";
  if($return){
    return $res;
  }
  echo $res;
}

function image_tag_sp($src, $attrs = '', $return = false) {

  // attrの中からclassを抜き出す
  $class_val = '';
  if(preg_match('/class="(.*)"/', $attrs, $match)){
    $class_val = " $match[1]";
    $attrs = preg_replace('/(class="\w+")/', '', $attrs);
  }

  // altが未設定の場合は attrs="" を追加
  if(!preg_match('/alt=".*"/', $attrs)) {
    $attrs .= ' alt=""';
  }

  $src_sp = preg_replace('/\.(\w+)$/', '-sp.$1', $src);

  $asset_src = asset_path("image/$src");
  $asset_src_sp = asset_path("image/$src_sp");

  $res =
    "<img class=\"pc$class_val\" $attrs src=\"$asset_src\">
    <img class=\"sp$class_val\" $attrs src=\"$asset_src_sp\">";
  if($return){
    return $res;
  }
  echo $res;
}

function img_tag($src, $attrs = '', $return = false) {
  $src_2x = preg_replace('/(\.\w+)$/', '@2x$1', $src);

  // 画像毎にhash値が違うので注意
  $asset_src = asset_path("image/$src");
  $asset_src_2x = asset_path("image/$src_2x");

  // altが未設定の場合は attrs="" を追加
  if(!preg_match('/alt=".*"/', $attrs)) {
    $attrs .= ' alt=""';
  }

  $res = "<img $attrs src=\"$asset_src\" srcset=\"$asset_src_2x 2x\">";
  if($return){
    return $res;
  }
  echo $res;
}

function img_tag_sp($src, $attrs = '', $return = false) {
  $src_2x = preg_replace('/(\.\w+)$/', '@2x$1', $src);

  // attrの中からclassを抜き出す
  $class_val = '';
  if(preg_match('/class="(.+)"/', $attrs, $match)){
    $class_val = " $match[1]";
    $attrs = preg_replace('/(class="\w+")/', '', $attrs);
  }

  // altが未設定の場合は attrs="" を追加
  if(!preg_match('/alt=".*"/', $attrs)) {
    $attrs .= ' alt=""';
  }

  $src_sp = preg_replace('/\.(\w+)$/', '-sp.$1', $src);
  $src_sp_2x = preg_replace('/(\.\w+)$/', '@2x$1', $src_sp);

  $asset_src = asset_path("image/$src");
  $asset_src_2x = asset_path("image/$src_2x");
  $asset_src_sp = asset_path("image/$src_sp");
  $asset_src_sp_2x = asset_path("image/$src_sp_2x");

  $res = "<img class=\"pc$class_val\" $attrs src=\"$asset_src\" srcset=\"$asset_src_2x 2x\"><img class=\"sp$class_val\" $attrs src=\"$asset_src_sp\" srcset=\"$asset_src_sp_2x 2x\">";
  if($return){
    return $res;
  }
  echo $res;
}
