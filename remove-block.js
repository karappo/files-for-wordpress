wp.domReady( () => {
	// // テキスト
	// wp.blocks.unregisterBlockType( 'core/paragraph' );                   // 段落
	// wp.blocks.unregisterBlockType( 'core/heading' );                     // 見出し
	// wp.blocks.unregisterBlockType( 'core/list' );                        // リスト
	// wp.blocks.unregisterBlockType( 'core/quote' );                       // 引用
	// wp.blocks.unregisterBlockType( 'core/code' );                        // コード
	// wp.blocks.unregisterBlockType( 'core/freeform' );                    // クラシック
	// wp.blocks.unregisterBlockType( 'core/preformatted' );                // 整形済みテキスト
	// wp.blocks.unregisterBlockType( 'core/pullquote' );                   // プルクオート
	// wp.blocks.unregisterBlockType( 'core/table' );                       // テーブル
	// wp.blocks.unregisterBlockType( 'core/verse' );                       // 詩

	// // メディア
	// wp.blocks.unregisterBlockType( 'core/image' );                       // 画像
	// wp.blocks.unregisterBlockType( 'core/gallery' );                     // ギャラリー
	// wp.blocks.unregisterBlockType( 'core/audio' );                       // 音声
	// wp.blocks.unregisterBlockType( 'core/cover' );                       // カバー
	// wp.blocks.unregisterBlockType( 'core/file' );                        // ファイル
	// wp.blocks.unregisterBlockType( 'core/media-text' );                  // メディアとテキスト
	// wp.blocks.unregisterBlockType( 'core/video' );                       // 動画

	// // デザイン
	// wp.blocks.unregisterBlockType( 'core/buttons' );                     // ボタン
	// wp.blocks.unregisterBlockType( 'core/columns' );                     // カラム
	// wp.blocks.unregisterBlockType( 'core/group' );                       // グループ
	// wp.blocks.unregisterBlockType( 'core/more' );                        // 続きを読む
	// wp.blocks.unregisterBlockType( 'core/nextpage' );                    // ページ区切り
	// wp.blocks.unregisterBlockType( 'core/separator' );                   // 区切り
	// wp.blocks.unregisterBlockType( 'core/spacer' );                      // スペーサー
	// wp.blocks.unregisterBlockType( 'core/site-logo' );                   // サイトロゴ
	// wp.blocks.unregisterBlockType( 'core/site-tagline' );                // サイトのキャッチフレーズ
	// wp.blocks.unregisterBlockType( 'core/site-title' );                  // サイトのタイトル
	// wp.blocks.unregisterBlockType( 'core/query-title' );                 // アーカイブタイトル
	// wp.blocks.unregisterBlockType( 'core/post-terms' );                  // 投稿カテゴリー & 投稿タグ

	// // ウィジェット
	// wp.blocks.unregisterBlockType( 'core/shortcode' );                   // ショートコード
	// wp.blocks.unregisterBlockType( 'core/archives' );                    // アーカイブ
	// wp.blocks.unregisterBlockType( 'core/calendar' );                    // カレンダー
	// wp.blocks.unregisterBlockType( 'core/categories' );                  // カテゴリー
	// wp.blocks.unregisterBlockType( 'core/html' );                        // カスタムHTML
	// wp.blocks.unregisterBlockType( 'core/latest-comments' );             // 最新のコメント
	// wp.blocks.unregisterBlockType( 'core/latest-posts' );                // 最新の投稿
	// wp.blocks.unregisterBlockType( 'core/page-list' );                   // 固定ページリスト
	// wp.blocks.unregisterBlockType( 'core/rss' );                         // RSS
	// wp.blocks.unregisterBlockType( 'core/social-links' );                // ソーシャルアイコン
	// wp.blocks.unregisterBlockType( 'core/tag-cloud' );                   // タグクラウド
	// wp.blocks.unregisterBlockType( 'core/search' );                      // 検索

	// // テーマ
	// wp.blocks.unregisterBlockType( 'core/query' );                       // クエリーループ & 投稿一覧
	// wp.blocks.unregisterBlockType( 'core/post-title' );                  // 投稿タイトル
	// wp.blocks.unregisterBlockType( 'core/post-content' );                // 投稿コンテンツ
	// wp.blocks.unregisterBlockType( 'core/post-date' );                   // 投稿日
	// wp.blocks.unregisterBlockType( 'core/post-excerpt' );                // 投稿の抜粋
	// wp.blocks.unregisterBlockType( 'core/post-featured-image' );         // 投稿のアイキャッチ
	// wp.blocks.unregisterBlockType( 'core/loginout' );                    // ログイン / ログアウト

	// 埋め込み
	wp.blocks.unregisterBlockVariation( 'core/embed', 'twitter' );       // Twitter
	// wp.blocks.unregisterBlockVariation( 'core/embed', 'youtube' );       // YouTube
	wp.blocks.unregisterBlockVariation( 'core/embed', 'wordpress' );     // WordPress
	wp.blocks.unregisterBlockVariation( 'core/embed', 'soundcloud' );    // SoundCloud
	wp.blocks.unregisterBlockVariation( 'core/embed', 'spotify' );       // Spotify
	wp.blocks.unregisterBlockVariation( 'core/embed', 'flickr' );        // Flickr
	wp.blocks.unregisterBlockVariation( 'core/embed', 'vimeo' );         // Vimeo
	wp.blocks.unregisterBlockVariation( 'core/embed', 'animoto' );       // Animoto
	wp.blocks.unregisterBlockVariation( 'core/embed', 'cloudup' );       // Cloudup
	wp.blocks.unregisterBlockVariation( 'core/embed', 'crowdsignal' );   // Crowdsignal
	wp.blocks.unregisterBlockVariation( 'core/embed', 'dailymotion' );   // Dailymotion
	wp.blocks.unregisterBlockVariation( 'core/embed', 'imgur' );         // Imgur
	wp.blocks.unregisterBlockVariation( 'core/embed', 'issuu' );         // Issuu
	wp.blocks.unregisterBlockVariation( 'core/embed', 'kickstarter' );   // Kickstarter
	wp.blocks.unregisterBlockVariation( 'core/embed', 'meetup-com' );    // Meetup.com
	wp.blocks.unregisterBlockVariation( 'core/embed', 'mixcloud' );      // Mixcloud
	wp.blocks.unregisterBlockVariation( 'core/embed', 'reddit' );        // Reddit
	wp.blocks.unregisterBlockVariation( 'core/embed', 'reverbnation' );  // ReverbNation
	wp.blocks.unregisterBlockVariation( 'core/embed', 'screencast' );    // Screencast
	wp.blocks.unregisterBlockVariation( 'core/embed', 'scribd' );        // Scribd
	wp.blocks.unregisterBlockVariation( 'core/embed', 'slideshare' );    // Slideshare
	wp.blocks.unregisterBlockVariation( 'core/embed', 'smugmug' );       // SmugMug
	wp.blocks.unregisterBlockVariation( 'core/embed', 'speaker-deck' );  // Speaker Deck
	wp.blocks.unregisterBlockVariation( 'core/embed', 'tiktok' );        // TikTok
	wp.blocks.unregisterBlockVariation( 'core/embed', 'ted' );           // TED
	wp.blocks.unregisterBlockVariation( 'core/embed', 'tumblr' );        // Tumblr
	wp.blocks.unregisterBlockVariation( 'core/embed', 'videopress' );    // VideoPress
	wp.blocks.unregisterBlockVariation( 'core/embed', 'wordpress-tv' );  // WordPress.tv
	wp.blocks.unregisterBlockVariation( 'core/embed', 'amazon-kindle' ); // Amazon Kindle
	wp.blocks.unregisterBlockVariation( 'core/embed', 'embed' );
	wp.blocks.unregisterBlockVariation( 'core/embed', 'pinterest' );
	wp.blocks.unregisterBlockVariation( 'core/embed', 'pocket-casts' );
	wp.blocks.unregisterBlockVariation( 'core/embed', 'wolfram-cloud' );
	wp.blocks.unregisterBlockVariation( 'core/embed', 'bluesky' );
} );
