<?php

// ==========================================================
//
// Config

$karappo_common_config = [
  'breakpoint' => 1000,
];

/**
 * karappo-common のデフォルト設定を上書きする
 * テーマの functions.php 等で呼び出す:
 *   karappo_common_config(['breakpoint' => 700]);
 *
 * @param array $overrides 上書きする設定の連想配列
 */
function karappo_common_config($overrides) {
  global $karappo_common_config;
  $karappo_common_config = array_merge($karappo_common_config, $overrides);
}

// ==========================================================
//
// Helpers

function assets_image_path($path) {
  // $srcが絶対パス（http://や/からはじまる場合）ならそのまま使うが、それ以外はassets/image/から読み込む
  if (
    preg_match('/^https?:\/\//', $path) ||
    preg_match('/^\//', $path)
  ) {
    return $path;
  }

  return get_template_directory_uri() . "/assets/image/$path";
}

/**
 * 単純なimgタグ
 * @param string $src Path to image
 * @param string $attrs Attributes e.g. 'alt="description of image" data-value="hoge"'
 * @param bool $return Set true if you just want result without echo
 * @return string
 */
function image_tag($src, $attrs = '', $return = false) {
  $src = assets_image_path($src);

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

/**
 * PC/SP用の２つのimgタグ（それぞれpc,spクラスを付与）
 * @param string $src Path to image
 * @param string $attrs Attributes e.g. 'alt="description of image" data-value="hoge"'
 * @param bool $return Set true if you just want result without echo
 * @return string or null
 */
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

  $asset_src = assets_image_path($src);
  $asset_src_sp = assets_image_path($src_sp);

  $res =
    "<img class=\"pc$class_val\" $attrs src=\"$asset_src\">
    <img class=\"sp$class_val\" $attrs src=\"$asset_src_sp\">";
  if($return){
    return $res;
  }
  echo $res;
}

/**
 * $srcで指定したパスに自動で@2xをつけてsrcsetを設定する
 * @param string $src Path to image
 * @param string $attrs Attributes e.g. 'alt="description of image" data-value="hoge"'
 * @param bool $return Set true if you just want result without echo
 * @return string or null
 */
function img_tag($src, $attrs = '', $return = false) {
  $src_2x = preg_replace('/(\.\w+)$/', '@2x$1', $src);

  // 画像毎にhash値が違うので注意
  $asset_src = assets_image_path($src);
  $asset_src_2x = assets_image_path($src_2x);

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

/**
 * $srcで指定したパスに自動で@2xをつけてsrcsetを設定する
 * PC/SP用の２つのimgタグ（それぞれpc,spクラスを付与）
 * @param string $src Path to image
 * @param string $attrs Attributes e.g. 'alt="description of image" data-value="hoge"'
 * @param bool $return Set true if you just want result without echo
 * @return string or null
 */
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

  $asset_src = assets_image_path($src);
  $asset_src_2x = assets_image_path($src_2x);
  $asset_src_sp = assets_image_path($src_sp);
  $asset_src_sp_2x = assets_image_path($src_sp_2x);

  $res = "<img class=\"pc$class_val\" $attrs src=\"$asset_src\" srcset=\"$asset_src_2x 2x\"><img class=\"sp$class_val\" $attrs src=\"$asset_src_sp\" srcset=\"$asset_src_sp_2x 2x\">";
  if($return){
    return $res;
  }
  echo $res;
}

/**
 * 画像のローカルファイルパスを取得
 * @param string $src 相対パス（assets/image/以下）
 * @return string ローカルファイルパス
 */
function get_image_filepath($src) {
  return get_template_directory() . "/assets/image/$src";
}

/**
 * <picture>タグによるレスポンシブ画像（@2x対応あり）
 * 使用例: pict_tag('top/hero.png', 'alt=""');
 * 出力:
 *   <picture>
 *     <source media="(max-width: 1000px)" srcset="top/hero-sp.png, top/hero-sp@2x.png 2x">
 *     <img src="top/hero.png" srcset="top/hero@2x.png 2x" alt="">
 *   </picture>
 *
 * @param string $src 画像パス
 * @param string $attrs 属性文字列
 * @param int|null $bp ブレークポイント（省略時はグローバル設定値）
 * @param bool $return trueなら文字列を返す
 * @return string|null
 */
function pict_tag($src, $attrs = '', $bp = null, $return = false) {
  global $karappo_common_config;
  if ($bp === null) $bp = $karappo_common_config['breakpoint'];
  $sp_src = preg_replace('/(\.\w+)$/', '-sp$1', $src);
  $sp_retina_src = preg_replace('/(\.\w+)$/', '@2x$1', $sp_src);
  $source_srcset = assets_image_path($sp_src) . ', ' . assets_image_path($sp_retina_src) . ' 2x';

  $sp_attrs = '';
  $sp_filepath = get_image_filepath($sp_src);
  if (file_exists($sp_filepath)) {
    $sp_size = getimagesize($sp_filepath);
    if ($sp_size) {
      $sp_attrs = ' width="' . $sp_size[0] . '" height="' . $sp_size[1] . '"';
    }
  }

  $fallback = img_tag($src, $attrs, true);
  $res = '<picture><source media="(max-width: ' . $bp . 'px)" srcset="' . $source_srcset . '"' . $sp_attrs . '>' . $fallback . '</picture>';
  if ($return) {
    return $res;
  }
  echo $res;
}

/**
 * <picture>タグによるレスポンシブ画像（@2xなし）
 * .webp指定時はフォールバック用のPNG/JPGパスも自動導出
 * 使用例: picture_tag('top/hero.png', 'alt=""');
 * 出力:
 *   <picture>
 *     <source media="(max-width: 1000px)" srcset="top/hero-sp.png">
 *     <img src="top/hero.png" alt="">
 *   </picture>
 *
 * @param string $src 画像パス
 * @param string $attrs 属性文字列
 * @param int|null $bp ブレークポイント（省略時はグローバル設定値）
 * @param bool $return trueなら文字列を返す
 * @return string|null
 */
function picture_tag($src, $attrs = '', $bp = null, $return = false) {
  global $karappo_common_config;
  if ($bp === null) $bp = $karappo_common_config['breakpoint'];
  $is_webp = preg_match('/\.webp$/', $src);

  // .webp指定時はフォールバック用のPNG/JPGパスを導出
  $fallback_src = null;
  if ($is_webp) {
    foreach (['.png', '.jpg'] as $ext) {
      $candidate = preg_replace('/\.webp$/', $ext, $src);
      if (file_exists(get_image_filepath($candidate))) {
        $fallback_src = $candidate;
        break;
      }
    }
  }

  $sp_src = preg_replace('/(\.\w+)$/', '-sp$1', $src);

  $sp_attrs = '';
  $sp_filepath = get_image_filepath($sp_src);
  if (file_exists($sp_filepath)) {
    $sp_size = getimagesize($sp_filepath);
    if ($sp_size) {
      $sp_attrs = ' width="' . $sp_size[0] . '" height="' . $sp_size[1] . '"';
    }
  }

  if ($is_webp && $fallback_src) {
    $fallback_sp_src = preg_replace('/(\.\w+)$/', '-sp$1', $fallback_src);
    $fallback = image_tag($fallback_src, $attrs, true);
    $sources = '';
    // WebP SP
    if (file_exists($sp_filepath)) {
      $sources .= '<source media="(max-width: ' . $bp . 'px)" srcset="' . assets_image_path($sp_src) . '" type="image/webp"' . $sp_attrs . '>';
    }
    // WebP PC
    $sources .= '<source srcset="' . assets_image_path($src) . '" type="image/webp">';
    // Fallback SP
    if (file_exists(get_image_filepath($fallback_sp_src))) {
      $sources .= '<source media="(max-width: ' . $bp . 'px)" srcset="' . assets_image_path($fallback_sp_src) . '"' . $sp_attrs . '>';
    }
    $res = '<picture>' . $sources . $fallback . '</picture>';
  } else {
    $source_srcset = assets_image_path($sp_src);
    $fallback = image_tag($src, $attrs, true);
    $res = '<picture><source media="(max-width: ' . $bp . 'px)" srcset="' . $source_srcset . '"' . $sp_attrs . '>' . $fallback . '</picture>';
  }

  if ($return) {
    return $res;
  }
  echo $res;
}

/**
 * SVGファイルをinline出力
 * @param string $src Path to image
 * @param string $attrs Attributes e.g. 'alt="description of image" data-value="hoge"'
 * @param bool $return Set true if you just want result without echo
 * @return string
 */
function inline_svg($src, $attrs = '', $return = false) {
  $src = assets_image_path($src);

  // デバッグ情報を出力
  // echo 'SVG Source URL: ' . $src;

  // まずローカルファイルパスとして試行
  $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $src);
  // echo 'Local Path: ' . $local_path;
  // echo 'File exists: ' . (file_exists($local_path) ? 'Yes' : 'No');

  if (file_exists($local_path)) {
    $res = file_get_contents($local_path);
  } else {
    // ホスト名が.testで終わる場合にSSL検証を無効にする
    $options = [];
    if (preg_match('/\.test$/', $_SERVER['HTTP_HOST'])) {
      echo '.test';
      $options = [
        "ssl" => [
          "verify_peer" => false,
          "verify_peer_name" => false,
        ],
      ];
    }

    $context = stream_context_create($options);
    $res = file_get_contents($src, false, $context);
  }

  if ($attrs != '') {
    // attrをもともとのattributesとマージして置換
    preg_match('/<svg ([^\>]*)>/', $res, $_matches);
    $attr_array = array_merge_recursive(parseAttributes($_matches[1]), parseAttributes($attrs));
    $attrs = '';
    foreach ($attr_array as $key => $value) {
      if (is_array($value)) {
        $value = implode(' ', $value);
      }
      $attrs .= " $key=\"$value\"";
    }
    $res = str_replace($_matches[1], $attrs, $res);
  }
  if($return){
    return $res;
  }
  echo $res;
}

/**
 * $srcで指定したパスに自動で@2xをつけて２つのSVGファイルをinline出力（それぞれpc,spクラスを付与）
 * @param string $src Path to image
 * @param string $attrs Attributes e.g. 'alt="description of image" data-value="hoge"'
 * @param bool $return Set true if you just want result without echo
 * @return string
 */
function inline_svg_sp($src, $attrs = '', $return = false) {
  // attrの中からclassを抜き出す
  $class_val = '';
  if(preg_match('/class="(.*)"/', $attrs, $match)){
    $class_val = " $match[1]";
    $attrs = preg_replace('/(class="\w+")/', '', $attrs);
  }

  $src_sp = preg_replace('/\.(\w+)$/', '-sp.$1', $src);

  inline_svg($src, "class=\"pc$class_val\" $attrs", $return);
  inline_svg($src_sp, "class=\"sp$class_val\" $attrs", $return);
}

/**
 * HTMLのattributesを配列化
 * @param string $str : 'attr1="hoge" attr2="moge"'
 * @return array : ["attr1"=> "hoge", "attr2"=>"moge"]
 */
function parseAttributes($str){
  preg_match_all('/(\w+)=[\'"]([^\'"]*)/', $str, $matches, PREG_SET_ORDER);
  $res = [];
  foreach($matches as $match){
      $attrName = $match[1];
      //parse the string value into an integer if it's numeric,
      // leave it as a string if it's not numeric,
      $attrValue = is_numeric($match[2])? (int)$match[2]: trim($match[2]);
      $res[$attrName] = $attrValue; //add match to results
  }
  return $res;
}

/**
 * $linkに応じてhrefやtargetなどのattrを設定して返す
 * @param string | array $link arrayはACFproのリンクオブジェクトの連想配列
 * @return string
 */

function get_attr_for_link($link) {
  $attr = '';
  // $linkが文字列だったら
  if (is_string($link) && $link) {
    $link_url = $link;
    $attr = 'href="'.esc_url( $link_url ).'"';
  }
  // $linkが連想配列だったら
  else if (is_array($link)) {
    $link_url = $link['url'];
    if ($link_url) {
      $link_target = $link['target'] ? $link['target'] : '_self';
      $attr = 'href="'.esc_url( $link_url ).'" target="'.esc_attr( $link_target ).'"';
    }
  }
  return $attr;
}