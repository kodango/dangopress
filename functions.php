<?php
/**
 * Functions.php contains all the core functions for your theme to work properly.
 *
 * @package dangopress
 */

if (is_admin()) {
    require_once('theme-options.php');
}

/*
 * Check whether the user is visiting in mobile device
 */
define('IS_MOBILE', wp_is_mobile());

/*
 * Require widgets functions
 */
require_once('functions/widgets.php');

/*
 * Include custom function php file if exists.
 *
 * You should put your personal functions in the functions/custom.php.
 */
define('CUSTOM_FUNCTIONS', get_template_directory() . '/functions/custom.php');

if (file_exists(CUSTOM_FUNCTIONS)) {
    require_once(CUSTOM_FUNCTIONS);
}

/**
 * Set the content width based on the theme's design and stylesheet.
 */
$content_width = 640;

/*
 * Customize filter and actions
 */
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'locale_stylesheet');
remove_action('wp_head', 'noindex', 1);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

/*
 * Disable emojicons introduced with WP 4.2
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
//add_filter('emoji_svg_url', '__return_false');

/*
 * Disable xml rpc
 */
add_filter('xmlrpc_enabled', '__return_false');

/*
 * Hide admin bar
 */
add_filter('show_admin_bar', '__return_false');

/*
 * Remove the responsive image support in 4.4
 */
add_filter('max_srcset_image_width', create_function('', 'return 1;'));

/*
 * Disable Automatic Formatting
 */
remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('the_title', 'wptexturize');
remove_filter('comment_text', 'wptexturize');

/*
 * Disable wp-json API
 */

// Filters for WP-API version 1.x
add_filter('json_enabled', '__return_false');
add_filter('json_jsonp_enabled', '__return_false');

// Filters for WP-API version 2.x
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');

// Remove REST API info from head and headers
remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('wp_head', 'wp_oembed_add_discovery_links');

/*
 * Disable Embed function
 */
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);

remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10 );
remove_filter('oembed_response_data',   'get_oembed_response_data_rich',  10, 4);

remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');

// Define a debug log function (DEBUG use only)
if (!function_exists('write_log')) {
    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

/*
 * Customize wordpress title
 */
function dangopress_title()
{
    $blog_name = get_bloginfo('name');
    $sep = '-';

    if (is_single() || is_page()) { // singular page
        $title = sprintf('%1$s %2$s %3$s', single_post_title('', false), $sep, $blog_name);
    } else if (is_category()) { // category page
        $title = sprintf('%1$s %2$s %3$s', single_cat_title('', false), $sep, $blog_name);
    } else if(is_tag()) { // tag page
        $title = sprintf('%1$s %2$s %3$s', single_tag_title('', false), $sep, $blog_name);
    } else { // other page, like home page or front page
        $site_description = get_bloginfo('description');

        if ($site_description) {
            $title = "$blog_name $sep $site_description";
        } else {
            $title = "$blog_name";
        }
    }

    return $title;
}
add_filter('pre_get_document_title', dangopress_title);

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function dangopress_setup_theme()
{
    // Add theme support
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-background');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');

    // Register wordpress menu
    register_nav_menus(array('primary' => 'Primary Navigation'));

    // Register sidebars for use in this theme
    register_sidebar(array(
        'name' => 'Main Sidebar',
        'id' => 'sidebar',
        'description' => '该区域的小工具会显示在右方的侧栏中',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));

    // Register auto-followed sidebar
    register_sidebar(array(
        'name' => 'Sidebar Follow',
        'id' => 'sidebar-follow',
        'description' => '该区域的小工具会显示在右方侧栏的跟随部分中',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
}
add_action('after_setup_theme', 'dangopress_setup_theme');

/*
 * Get theme url prefix for styles or scripts
 */
function dangopress_get_url_prefix()
{
    $options = get_option('dangopress_options');

    if (!empty($options['cdn_prefix']))
        return $options['cdn_prefix'];
    else
        return get_template_directory_uri();
}

/*
 * Defer load javascript
 */
 function dangopress_defer_scripts($tag, $handle, $src) {
     $defer_scripts = array(
         'prettify-js',
         'theme-js',
         'jquery'
     );

     if (in_array($handle, $defer_scripts)) {
         return str_replace(' src', ' defer="defer" src', $tag);
     } else {
         return $tag;
     }
 }
 add_filter('script_loader_tag', 'dangopress_defer_scripts', 10, 2);

/*
 * Load css and javascript
 */
function dangopress_load_files()
{
    // URL prefix
    $url_prefix = dangopress_get_url_prefix();
    // Theme options
    $options = get_option('dangopress_options');

    // Whether to load compressed scripts/styles or not
    if ($options['using_compressed_files']) {
        $js_suffix = '.min.js';
        $css_suffix = '.min.css';
    } else {
        $js_suffix = '.js';
        $css_suffix = '.css';
    }

    // Theme css
    wp_enqueue_style('theme-main', $url_prefix . '/styles/theme' . $css_suffix, array(), '0.4.6');

    // Replace jQuery, use Baidu Public Library CDN
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', $url_prefix . '/scripts/jquery-2.1.1' . $js_suffix,
                           array(), '2.1.1', true);
    }

    // Register prettify.js
    wp_enqueue_script('prettify-js', $url_prefix . '/scripts/prettify' . $js_suffix,
                       array(), '0.4.6', true);

    // Theme script
    wp_enqueue_script('theme-js', $url_prefix . '/scripts/theme' . $js_suffix,
                      array('jquery'), '0.4.6', true);
}
add_action('wp_enqueue_scripts', 'dangopress_load_files');


/*
 * Enable page excerpt support
 */
add_post_type_support('page', 'excerpt');

/*
 * Add meta description in the head
 * Reference:
 * https://cnzhx.net/blog/add-wordpress-meta-description-keyword-php/
 * https://www.davidtiong.com/how-to-add-noindex-follow-to-pages-in-wordpress-stop-duplicate-content/
 */
function dangopress_get_description() {
    if (is_home() || is_front_page()) {
        $options = get_option('dangopress_options');
        $description = $options['home_meta_descripton'];
    } elseif (is_singular() && !is_attachment()) {
        global $post;
        $description = ($post->post_excerpt != '') ? $post->post_excerpt : $post->post_content;
    } elseif (is_category()) {
        $description = category_description();
    } elseif (is_tag()) {
        $description = tag_description();
    }

    if ($description != '') {
        $description = preg_replace('#\[[^\]]+\]#', '', $description);
        $description = wp_html_excerpt(wp_strip_all_tags($description, true), 200);
    }

    return $description;
}

function dangopress_add_meta_description() {
    $description = dangopress_get_description();

    if ($description == '')
        return;

    echo '<meta name="description" content="' . $description . '" />';
}
add_action('wp_head', 'dangopress_add_meta_description');

/*
 * Add noindx,follow onto your date archives, tag archives, author archives,
 * and onto the subsequent pages of your individual category pages
 */
function dangopress_add_meta_robots() {
    global $paged;

    if ($paged > 1 || is_author() || is_tag() || is_date() || is_attachment() || is_page_template("page-archives.php")) {
        echo '<meta name="robots" content="noindex,follow" />';
    }
}
add_action('wp_head', 'dangopress_add_meta_robots');

/*
 * Add Social Media Meta information
 * Reference:
 * http://www.wpbeginner.com/wp-themes/how-to-add-facebook-open-graph-meta-data-in-wordpress-themes/
 * https://moz.com/blog/meta-data-templates-123
 */
 function dangopress_add_social_media_meta() {
    $options = get_option('dangopress_options');

    if (!$options['enable_social_meta'])
        return;

    // Only add open graph to below pages
    if (!is_singular())
        return;

    if ($options['fb_user_id'])
        echo '<meta property="fb:admins" content="' . $options['fb_user_id'] . '"/>';

    echo '<meta property="og:site_name" content="' . get_bloginfo('name'). '"/>';
    echo '<meta property="og:type" content="article"/>';
    echo '<meta property="og:title" content="' . get_the_title() . '"/>';
    echo '<meta property="og:url" content="' . get_permalink() . '"/>';
    echo '<meta name="twitter:card" value="summary">';

    $meta_description = dangopress_get_description();

    if ($meta_description)
        echo '<meta property="og:description" content="' . $meta_description . '"/>';

    global $post;
	if (has_post_thumbnail($post->ID)) { // If the post has featured image, use it
		$image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
        $image_url = strtok($image_attributes[0], '?');
		echo '<meta property="og:image" content="' . esc_attr($image_url) . '"/>';
	}

}
add_action('wp_head', 'dangopress_add_social_media_meta');

/*
 * Wrap the post image in div container
 */
if (is_admin()) {
    function dangopress_wrap_post_image($html, $id, $caption, $title, $align, $url, $size, $alt)
    {
        return '<div class="post-image">'.$html.'</div>';
    }
    add_filter('image_send_to_editor', 'dangopress_wrap_post_image', 10, 8);
}

/*
 * Add rel="nofollow" to read more link
 */
function dangopress_nofollow_link($link)
{
    return str_replace('<a', '<a rel="nofollow"', $link);
}
add_filter('the_content_more_link', 'dangopress_nofollow_link', 0);

/*
 * Disable self ping
 */
function dangopress_disable_self_ping(&$links)
{
    $home = get_option('home');

    foreach ($links as $l => $link)
        if (0 === strpos($link, $home))
            unset($links[$l]);
}
add_action('pre_ping', 'dangopress_disable_self_ping');

/*
 * Remove version number in the loading script or stylesheet
 */
function dangopress_remove_version($src)
{
    $parts = explode('?ver', $src);
    return $parts[0];
}
add_filter('script_loader_src', 'dangopress_remove_version', 15, 1);
add_filter('style_loader_src', 'dangopress_remove_version', 15, 1);

/*
 * Escape special characters in pre.prettyprint into their HTML entities
 */
function dangopress_esc_html($content)
{
    $patterns = array(
        '/(<pre\s+[^>]*?class\s*?=\s*?[",\'].*?prettyprint.*?[",\'].*?>)(.*?)(<\/pre>)/sim',
        '/(<pre>[\n\s]*<code>)(.*?)(<\/code>[\n\s]*<\/pre>)/sim',
    );

    return preg_replace_callback($patterns, dangopress_esc_callback, $content);
}

function dangopress_esc_callback($matches)
{
    $tag_open = $matches[1];
    $content = $matches[2];
    $tag_close = $matches[3];

    //$content = htmlspecialchars($content, ENT_NOQUOTES, get_bloginfo('charset'));
    $content = esc_html($content);
    $tag_open = preg_replace('/<pre>[\n\s]*<code>/', '<pre class="prettyprint"><code>', $tag_open);

    return $tag_open . $content . $tag_close;
}
add_filter('the_content', 'dangopress_esc_html', 2);
add_filter('comment_text', 'dangopress_esc_html', 2);

/*
 * Alter the main loop
 */
function dangopress_alter_main_loop($query)
{
    /* Only for main loop in home page */
    if (!$query->is_home() || !$query->is_main_query())
        return;

    // ignore sticky posts, don't show them in the start
    $query->set('ignore_sticky_posts', 1);
}
add_action('pre_get_posts', 'dangopress_alter_main_loop');

/*
 * Retrieve paginated link for archive post pages
 */
function dangopress_paginate_links()
{
    global $wp_query;

    $total = $wp_query->max_num_pages;
    $big = 999999999; // need an unlikely integer

    if ($total < 2)
        return;

    $output = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $total,
        'prev_text' => '<i class="icon-arrow-circle-left"></i>',
        'next_text' => '<i class="icon-arrow-circle-right"></i>',
    ));

    echo '<div id="pagenavi">' . $output . '</div>';
}

/*
 * Show humanable time delta
 */
function dangopress_human_time_diff($gmt_time)
{
    $from_timestamp = strtotime("$gmt_time" . ' UTC');
    $to_timestamp = current_time('timestamp', 1);

    if ($to_timestamp - $from_timestamp > 2592000) { // One month ago
        return date_i18n('Y-m-d G:i:s', $from_timestamp, true);
    } else {
        $diff = human_time_diff($from_timestamp, $to_timestamp);
        return preg_replace('/(\d+)/', "$1 ", "{$diff}前");
    }
}

/*
/*
 * Display comment lists
 */
function dangopress_comments_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;

    global $commentcount;

    /* Initialize the comment count */
    if (!$commentcount) {
        $page = get_query_var('cpage') - 1;

        if ($page > 0) {
            $cpp = get_option('comments_per_page');
            $commentcount = $cpp * $page;
        } else {
            $commentcount = 0;
        }
    }

    $comment_id = $comment->comment_ID; ?>

    <li <?php comment_class(); ?> id="li-comment-<?php echo $comment_id; ?>">
        <div id="comment-<?php echo $comment_id; ?>" class="comment-body <?php if ($comment->comment_approved == '0') echo 'pending-comment'; ?>">
            <div class="comment-avatar">
                <?php echo get_avatar($comment, $depth==1 ? 48: 32, '', "$comment->comment_author's avatar"); ?>
            </div>
            <div class="comment-meta">
                <span class="comment-author<?php echo user_can($comment->user_id, 'administrator') ? ' postauthor': ''?>"><?php comment_author_link(); ?></span>
                <span class="comment-date">发表于 <?php echo dangopress_human_time_diff($comment->comment_date_gmt); ?></span>
                <span class="comment-reply">
                <?php
                    comment_reply_link(array_merge($args, array(
                        'reply_text' => '回复',
                        'depth' => $depth,
                        'max_depth' => $args['max_depth']
                    )));

                    if ($depth == 1) { // Show the floor number
                         printf(' #%1$s', ++$commentcount);
                    }
                ?>

                </span>

            </div>

            <div class="comment-text"><?php comment_text(); ?></div>
        </div>
<?php
}

/*
 * Add at user before comment text
 */
function dangopress_add_at_author($comment_text, $comment)
{
    if ($comment->comment_parent) { // Show reply to somebody
        $parent = get_comment($comment->comment_parent);
        $parent_href = htmlspecialchars(get_comment_link($comment->comment_parent));

        $parent_author = $parent->comment_author;
        $parent_title = mb_strimwidth(strip_tags($parent->comment_content), 0, 100, '...');

        $parent_link = "<a href=\"$parent_href\" title=\"$parent_title\">@$parent_author</a>";
        $comment_text = '<span class="at-author">' . $parent_link . ':</span>' . $comment_text;
    }

    return $comment_text;
}
add_filter('comment_text', 'dangopress_add_at_author', 10, 2);

/*
 * Check whether a gravatar exists for a given user email
 *
 * Taken from gist: https://gist.github.com/justinph/5197810
 */
function dangopress_validate_gravatar($email)
{
    if (empty($email))
        return false;

    $hashkey = md5(strtolower(trim($email)));
    $uri = 'http://www.gravatar.com/avatar/' . $hashkey . '?d=404';

    $data = wp_cache_get($hashkey);

    if ($data === false) {
        $response = wp_remote_head($uri);

        if (is_wp_error($response)) {
            $data = 'not200';
        } else {
            $data = $response['response']['code'];
        }

        wp_cache_set($hashkey, $data, $group = '', $expire = 60*60*24);
    }

    if ($data == '200') {
        return true;
    } else {
        return false;
    }
}

/*
 * Open the link in the new tab
 */
function dangopress_new_tab($link)
{
    return str_replace('<a', '<a target="_blank"', $link);
}
add_filter('get_comment_author_link', 'dangopress_new_tab');

/*
 * Send an email when recieved a reply
 */
function dangopress_email_nodify($comment_id)
{
    global $wpdb;

    $admin_email = get_bloginfo('admin_email');

    $comment = get_comment($comment_id);
    $comment_author_email = trim($comment->comment_author_email);

    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';

    /*
     * Add comment_mail_notify column when first run
     */
    if ($wpdb->query("Describe $wpdb->comments comment_mail_notify") == '')
        $wpdb->query("ALTER TABLE $wpdb->comments ADD COLUMN comment_mail_notify TINYINT NOT NULL DEFAULT 0;");

    /*
     * Set notify value to 1 if the checkbox is checked in the comment form
     */
    if (isset($_POST['comment_mail_notify']))
        $wpdb->query("UPDATE $wpdb->comments SET comment_mail_notify='1' WHERE comment_ID='$comment_id'");

    $notify = $parent_id ? get_comment($parent_id)->comment_mail_notify : '0';
    $spam_confirmed = $comment->comment_approved;

    /*
     * Don't send email if:
     * 1. the comment is a spam
     * 2. the notify checkbox isn't checked
     */
    if ($notify != '1' || $spam_confirmed == 'spam')
        return;

    // Prepare the email
    $sender = 'no-reply@' . preg_replace('#^www.#', '', strtolower($_SERVER['SERVER_NAME']));

    $to = trim(get_comment($parent_id)->comment_author_email);
    $subject = '您在 [' . get_option('blogname') . '] 的留言有了回复';

    $from = 'From: "' . get_option('blogname') . '" <' . $sender . '>';
    $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";

    $message .= '<div style="background-color:#eef2fa;border:1px solid #d8e3e8;padding:0 15px;">';
    $message .= '<p style="color:#000">您好, <strong>' . trim(get_comment($parent_id)->comment_author) . '</strong>:</p>';
    $message .= '<p style="color:#000">您曾在《' . get_the_title($comment->comment_post_ID) . '》的留言: </p>';
    $message .= '<blockquote style="background:#fafafa;border-left:1px solid #ddd;padding:10px;margin:15px 0;">';
    $message .= trim(get_comment($parent_id)->comment_content) . '</blockquote>';
    $message .= '<p style="color:#000">收到来自 <strong>' . trim($comment->comment_author) . '</strong> 给您的回复:</p>';
    $message .= '<blockquote style="background:#fafafa;border-left:1px solid #ddd;padding:10px;margin:15px 0;">';
    $message .= trim($comment->comment_content) . '</blockquote>';
    $message .= '<p style="color:#000">您可以点击以下链接（或者复制链接到地址栏访问）查看回复的完整内容:</p>';
    $message .= '<blockquote style="background:#fafafa;border-left:1px solid #ddd;padding:10px;margin:15px 0;">';
    $message .= get_comment_link($comment) . '</blockquote>';
    $message .= '<p style="color:#000">欢迎再次光临 <a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a></p>';
    $message .= '<p style="color:#888;">友情提醒: 此邮件由系统自动发送，请勿回复。</p></div>';

    wp_mail($to, $subject, $message, $headers);
}
add_action('comment_post', 'dangopress_email_nodify');

/*
 * Show breadcrumb by Dimox
 * URL: http://dimox.net/wordpress-breadcrumbs-without-a-plugin/
 * Version: 2017.21.01
 * License: MIT
 */
function dangopress_breadcrumb()
{
    /* === OPTIONS === */
    $text['home']     = '首页'; // text for the 'Home' link
    $text['category'] = '%s'; // text for a category page
    $text['search']   = '"%s" 的搜索结果'; // text for a search results page
    $text['tag']      = '含标签 "%s" 的文章'; // text for a tag page
    $text['404']      = '页面未找到'; // text for the 404 page
    $text['page']     = '第 %s 页'; // text 'Page N'
    $text['cpage']    = '第 %s 页'; // text 'Comment Page N'

    $prefix         = '<i class="icon-windows"></i>'; // Prefix the breadcrumb
    $wrap_before    = '<div id="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // the opening wrapper tag
    $wrap_after     = '</div><!-- .breadcrumbs -->'; // the closing wrapper tag
    $sep            = '<i class="icon-caret-right"></i>'; // separator between crumbs
    $sep_before     = '<span class="sep">'; // tag before separator
    $sep_after      = '</span>'; // tag after separator
    $show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
    $show_current   = 1; // 1 - show current page title, 0 - don't show
    $before         = '<h1 class="current-crumb">'; // tag before the current crumb
    $after          = '</h1>'; // tag after the current crumb
    /* === END OF OPTIONS === */

    global $post;
    $home_url       = home_url('/');

    $link_before    = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    $link_after     = '</span>';
    $link_attr      = ' itemprop="item"';
    $link_in_before = '<span itemprop="name">';
    $link_in_after  = '</span>';
    $link           = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $link_after;

    $frontpage_id   = get_option('page_on_front');
    $parent_id      = ($post) ? $post->post_parent : '';
    $sep            = ' ' . $sep_before . $sep . $sep_after . ' ';
    $home_link      = $link_before . '<a rel="nofollow" href="' . $home_url . '"' . $link_attr . ' class="home">' . $link_in_before . $text['home'] . $link_in_after . '</a>' . $link_after;

    if (is_home() || is_front_page()) {
        return;
    } else {
        echo $wrap_before . $prefix;
        if ($show_home_link) echo $home_link;

        if (is_category()) {
            $cat = get_category(get_query_var('cat'), false);
            if ($cat->parent != 0) {
                $cats = get_category_parents($cat->parent, TRUE, $sep);
                $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
                $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
                if ($show_home_link) echo $sep;
                echo $cats;
            }

            if ($show_current) echo $sep . $before . sprintf($text['category'], single_cat_title('', false)) . $after;
        } elseif (is_search()) {
            if (have_posts()) {
                if ($show_home_link && $show_current) echo $sep;
                if ($show_current) echo $before . sprintf($text['search'], get_search_query()) . $after;
            } else {
                if ($show_home_link) echo $sep;
                echo $before . sprintf($text['search'], get_search_query()) . $after;
            }
        } elseif (is_day()) {
            if ($show_home_link) echo $sep;
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $sep;
            echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('m'));
            if ($show_current) echo $sep . $before . get_the_time('d') . $after;
        } elseif (is_month()) {
            if ($show_home_link) echo $sep;
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'));
            if ($show_current) echo $sep . $before . get_the_time('m') . $after;
        } elseif (is_year()) {
            if ($show_home_link && $show_current) echo $sep;
            if ($show_current) echo $before . get_the_time('Y') . $after;
        } elseif (is_single() && !is_attachment()) {
            if ($show_home_link) echo $sep;
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                printf($link, $home_url . $slug['slug'] . '/', $post_type->labels->singular_name);
                if ($show_current) echo $sep . $before . get_the_title() . $after;
            } else {
                $cat = get_the_category(); $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, $sep);
                if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
                $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
                echo $cats;

                if (get_query_var('cpage')) {
                    echo $sep . sprintf($link, get_permalink(), get_the_title()) . $sep . $before . sprintf($text['cpage'], get_query_var('cpage')) . $after;
                } else {
                    if ($show_current) echo $before . get_the_title() . $after;
                }
            }
        // custom post type
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            if ( get_query_var('paged') ) {
                echo $sep . sprintf($link, get_post_type_archive_link($post_type->name), $post_type->label) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
            } else {
                if ($show_current) echo $sep . $before . $post_type->label . $after;
            }
        } elseif (is_attachment()) {
            if ($show_home_link) echo $sep;
            $parent = get_post($parent_id);
            $cat = get_the_category($parent->ID); $cat = $cat[0];
            if ($cat) {
                $cats = get_category_parents($cat, TRUE, $sep);
                $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
                echo $cats;
            }
            printf($link, get_permalink($parent), $parent->post_title);
            if ($show_current) echo $sep . $before . get_the_title() . $after;
        } elseif (is_page() && !$parent_id) {
            if ($show_current) echo $sep . $before . get_the_title() . $after;
        } elseif (is_page() && $parent_id) {
            if ($show_home_link) echo $sep;
            if ($parent_id != $frontpage_id) {
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    if ($parent_id != $frontpage_id) {
                        $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                    }
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo $breadcrumbs[$i];
                    if ($i != count($breadcrumbs)-1) echo $sep;
                }
            }
            if ($show_current) echo $sep . $before . get_the_title() . $after;
        } elseif (is_tag()) {
            if ( get_query_var('paged') ) {
                $tag_id = get_queried_object_id();
                $tag = get_tag($tag_id);
                echo $sep . sprintf($link, get_tag_link($tag_id), $tag->name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
            } else {
                if ($show_current) echo $sep . $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
            }
        } elseif (is_404()) {
            if ($show_home_link && $show_current) echo $sep;
            if ($show_current) echo $before . $text['404'] . $after;
        } elseif (has_post_format() && !is_singular()) {
            if ($show_home_link) echo $sep;
            echo get_post_format_string( get_post_format());
        }
        echo $wrap_after;
    }
}

/*
 * Place baidu share icons
 */
function dangopress_place_bdshare()
{
    $options = get_option('dangopress_options');

    if (empty($options['bdshare_uid']))
        return;
?>

<div id="bdshare" class="bdshare_t bds_tools_24 get-codes-bdshare">
    <a class="bds_tsina"></a>
    <a class="bds_tqq"></a>
    <a class="bds_twi"></a>
    <a class="bds_hi"></a>
    <a class="bds_douban"></a>
    <a class="bds_tieba"></a>
    <a class="bds_youdao"></a>
    <a class="bds_copy"></a>
    <span class="bds_more"></span>
</div>

<?php

    add_action('wp_footer', 'dangopress_load_bdshare');
}

/*
 * Load baidu share scripts
 */
function dangopress_load_bdshare()
{
    $options = get_option('dangopress_options');
    $bdshare_uid = $options['bdshare_uid'];
?>

<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=<?php echo $bdshare_uid; ?>" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
</script>

<?php
}

/*
 * Insert Google Adsense scripts
 */
function dangopress_insert_adsense_scripts()
{
    $options = get_option('dangopress_options');
    $publisher_id = $options["adsense_publisher_id"];

    if (empty($publisher_id))
        return;

?>

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
    (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "<?php echo $publisher_id; ?>",
        enable_page_level_ads: true
    });
</script>
<?php
}
add_action('wp_head', 'dangopress_insert_adsense_scripts');

/*
 * Insert analytics code snippets into head
 */
function dangopress_insert_analytics_snippets()
{
    /* Do not track administrator */
    if (current_user_can('manage_options'))
        return;

    $options = get_option('dangopress_options');

    if (!empty($options['google_webid'])) {
?>

<!-- Google Analytics -->
<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $options['google_webid']; ?>', 'auto');
  ga('send', 'pageview');
</script>

<?php
    }

    if (!empty($options['bing_webmaster_user'])) {
?>

<!-- Bing Webmaster authentication -->
<meta name="msvalidate.01" content="<?php echo $options['bing_webmaster_user']; ?>" />

<?php
    }

    if (!empty($options['bdtj_siteid'])) {
?>

<!-- Baidu Tongji -->
<script>
var _hmt = _hmt || [];
(function() {
    var hm = document.createElement("script");
    hm.src = "//hm.baidu.com/hm.js?<?php echo $options['bdtj_siteid']; ?>";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hm, s);
})();
</script>

<?php
    }
}
add_action('wp_head', 'dangopress_insert_analytics_snippets');

/*
 * Show description box in category page
 */
function dangopress_category_description()
{
    $description = category_description();

    // Don't show the box if description is empty
    if (empty($description))
        return;

    $cat_name = single_cat_title('', false);
    $cat_ID = get_cat_ID($cat_name);

    // List the sub categories if have
    $sub_cats = wp_list_categories("child_of=$cat_ID&style=none&echo=0&show_option_none=");

    if (!empty($sub_cats)) {
        $sub_cats = str_replace("<br />", "", $sub_cats);  // strip the <br /> tag
        $sub_cats = str_replace("\n\t", ", ", $sub_cats);  // separated by comma

        $sub_cats = '<div class="sub-categories">包含子目录: ' . $sub_cats . '</div>';
    }
?>

    <div id="category-panel" class="">
        <h2><?php echo $cat_name; ?> 类目</h2>
        <?php echo $description; ?>
        <?php echo $sub_cats; ?>
    </div>

<?php
}

/*
 * Add additional button in the wordpress editor
 */
function dangopress_add_quicktags()
{
    /* Check whether the quicktags.js is registered */
    if (!wp_script_is('quicktags'))
        return;
?>

    <script type="text/javascript">
    QTags.addButton('eg_h3', 'h3', '<h3>', '</h3>', '', '三级标题', 101);
    QTags.addButton('eg_h4', 'h4', '<h4>', '</h4>', '', '四级标题', 102);
    QTags.addButton('eg_pre', 'pre', '<pre>', '</pre>', '', '', 111);
    QTags.addButton('eg_prettify', 'prettify', '<pre class="prettyprint">', '</pre>', '', '代码高亮', 112);
    </script>

<?php
}
add_action('admin_print_footer_scripts', 'dangopress_add_quicktags');

/*
 * Disable google fonts
 */
function dangopress_disable_google_fonts($translations, $text, $context, $domain )
{
    $google_fonts_contexts = array(
        'Open Sans font: on or off',
        'Lato font: on or off',
        'Source Sans Pro font: on or off',
        'Bitter font: on or off');

    if ($text == 'on' && in_array($context, $google_fonts_contexts)) {
        $translations = 'off';
    }

    return $translations;
}
add_filter('gettext_with_context', 'dangopress_disable_google_fonts', 888, 4);

/*
 * Embed gists with a URL in post article
 */
function dangopress_embed_gist($matches, $attr, $url, $rawattr)
{
    $embed = sprintf(
        '<script src="https://gist.github.com/%1$s.js%2$s"></script>',
        esc_attr($matches[1]),
        esc_attr($matches[2])
    );

    return apply_filters('dangopress_embed_gist', $embed, $matches, $attr, $url, $rawattr);
}

wp_embed_register_handler(
    'gist',
    '#https?://gist\.github\.com(?:/[a-z0-9-]+)?/([a-z0-9]+)(\?file=.*)?#i',
    'dangopress_embed_gist'
);

/*
 * Use V2EX avatar service
 */
function dangopress_get_v2ex_avatar($avatar) {
    return str_replace(
        array(
            "secure.gravatar.com/avatar",
            "0.gravatar.com/avatar",
            "1.gravatar.com/avatar",
            "2.gravatar.com/avatar"
        ), "cdn.v2ex.com/gravatar", $avatar);
}
add_filter('get_avatar', 'dangopress_get_v2ex_avatar');

/*
 * Disable dns prefetch
 */
function dangopress_disable_dns_prefetch($hints, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        return array_diff(wp_dependencies_unique_hosts(), $hints);
    }

    return $hints;
}
add_filter('wp_resource_hints', 'remove_dns_prefetch', 10, 2);

/*
 * Show dynamic copyright information
 */
function dangopress_show_copyright() {
    global $wpdb;

    $copyright_dates = $wpdb->get_results("
        SELECT
        YEAR(min(post_date_gmt)) AS firstdate,
        YEAR(max(post_date_gmt)) AS lastdate
        FROM
        $wpdb->posts
        WHERE
        post_status = 'publish'
    ");

    if ($copyright_dates) {
        $copyright = "Copyright &copy; " . $copyright_dates[0]->firstdate;

        if ($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {
            $copyright .= '-' . $copyright_dates[0]->lastdate;
        }
    } else {
        $copyright = "Copyright &copy; " . date('Y');
    }

    $copyright .= ' ' . get_bloginfo('name') . '.';
    $copyright .= ' <span class="theme-declare"><a href="http://kodango.com/dangopress-theme">dangopress Theme</a> powered by <a href="http://wordpress.org/">WordPress</a></span>';

    $copyright = '<span class="copyright">' . $copyright . '</span>';
    echo $copyright;
}

/*
 * Show sitexmap link
 */
function dangopress_show_sitemap() {
   $options = get_option('dangopress_options');
   $sitemap = $options['sitemap_xml'];

   if (!empty($sitemap)) {
       $link = '<span class="sitemap"><a href="' . get_bloginfo('url') . '/' . $sitemap . '">站点地图<i class="icon-sitemap"></i></a></span>';

       if (!is_home()) {
           $link = dangopress_nofollow_link($link);
       }

       echo $link;
    }
}
?>
