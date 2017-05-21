<?php
/*
 * Widgets for dangopress theme
 */

/*
 * Get a list of recent posts
 */

function dangopress_get_recent_posts($post_num = 10, $chars = 30)
{
    $recents = wp_get_recent_posts("numberposts=$post_num&offset=0&post_status=publish");
    $output = '';

    foreach ($recents as $post) {
        $permalink = get_permalink($post['ID']);
        $title = $post['post_title'];
        $title_attr = esc_attr(strip_tags($title));
        $human_time = show_human_time_diff($post['post_date_gmt']);

        $link = '<a href="' . $permalink. '" rel="bookmark" title="详细阅读《' . $title_attr . '》">';
        $link .= wp_trim_words($title, $chars) . '</a>';

        $output .= '<li>' . $link . '<small>发表于 ' . $human_time . '</small></li>';
    }

    return $output;
}

/*
 * Get a list of random posts
 */
function dangopress_get_rand_posts($post_num = 10, $chars = 30)
{
    $rands = get_posts("numberposts=$post_num&orderby=rand&post_status=publish");
    $output = '';

    foreach ($rands as $post) {
        $permalink = get_permalink($post->ID);
        $title = $post->post_title;
        $title_attr = esc_attr(strip_tags($title));
        $human_time = show_human_time_diff($post->post_date_gmt);

        $link = '<a href="' . $permalink. '" rel="bookmark" title="随机阅读《' . $title_attr . '》">';
        $link .= wp_trim_words($title, $chars) . '</a>';

        $output .= '<li>' . $link . '<small>发表于 ' . $human_time . '</small></li>';
    }

    return $output;
}

/*
 * Get a list of sticky posts
 */
function dangopress_get_sticky_posts($posts_num = 10, $chars = 30)
{
    $args = array(
        'numberposts' => $posts_num,
        'post__in' => get_option('sticky_posts'),
        'orderby' => 'modified'
    );

    $sticky_posts = get_posts($args);
    $output = '';

    foreach ($sticky_posts as $post) {
        $permalink = get_permalink($post->ID);
        $title = $post->post_title;
        $title_attr = esc_attr(strip_tags($title));
        $human_time = show_human_time_diff($post->post_modified_gmt);

        $link = '<a href="' . $permalink. '" rel="bookmark" title="推荐阅读《' . $title_attr . '》">';
        $link .= wp_trim_words($title, $chars) . '</a>';

        $output .= '<li>' . $link . '<small>最经更新于 ' . $human_time . '</small></li>';
    }

    return $output;
}

/*
 * Get the most commented posts
 */
function dangopress_get_most_commented($posts_num = 10, $chars = 30, $days = 180)
{
    global $wpdb;

    $sql = "SELECT ID , post_title , comment_count
           FROM $wpdb->posts
           WHERE post_type = 'post' AND post_status = 'publish'
           AND TO_DAYS(now()) - TO_DAYS(post_date) < $days
           ORDER BY comment_count DESC LIMIT 0 , $posts_num ";

    $posts = $wpdb->get_results($sql);
    $output = "";

    foreach ($posts as $post) {
        $permalink = get_permalink($post->ID);
        $title = $post->post_title;
        $title_attr = esc_attr(strip_tags($title));
        $comment_num = $post->comment_count;

        $link = '<a href="' . $permalink. '" rel="bookmark" title="详细阅读《' . $title_attr . '》">';
        $link .= wp_trim_words($title, $chars) . '</a>';

        $output .= '<li>' . $link . '<small>共 ' . $comment_num . ' 条评论</small></li>';
    }

    return $output;
}

/*
 * Widget: Posts Tabber
 */
class Dangopress_PostsTabber_Widget extends WP_Widget {
    /*
     * Instantiate the widget object
     */
    function __construct()
    {
        $widget_opts = array(
            'classname' => 'widget-tabber',     // widget classname
            'description' => '多个维度显示文章列表'   // widget description
        );

        parent::__construct(
            'dangopress_poststabber',    // widget base id
            '文章列表 [dangopress]',     // widget name
            $widget_opts                 // widget options
        );

        add_action('save_post', array($this, 'flush_widget_cache'));
        add_action('deleted_post', array($this, 'flush_widget_cache'));
    }

    /*
     * Display the recent comments widget
     */
    function widget($args, $instance)
    {
        if (($instance['show_in_home'] && !is_home()))
            return;

        // Get the widget content from cache first
        $cache = wp_cache_get('widget_dangopress_poststabber', 'widget');

        if (!is_array($cache))
            $cache = array();

        if (!isset($args['widget_id']))
            $args['widget_id'] = $this->id;

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        extract($args, EXTR_SKIP);
        $output = "";

        // Get the number of posts
        $number = (!empty($instance['number'])) ? $instance['number'] : 6;

        if (!$number)
            $number = 6;

        // Get the maximum character number of post title
        $chars = (!empty($instance['chars'])) ? $instance['chars'] : 30;

        if (!$chars)
            $chars = 30;

        $output .= $before_widget;

        // Show posts tabber title
        $output .= '<div class="tabber-title"><ul class="tabnav four clearfix">';
        $output .= '<li class="selected">' . $before_title . '置顶' . "$after_title</li>";
        $output .= '<li class="">' . $before_title . '热评' . "$after_title</li>";
        $output .= '<li class="">' . $before_title . '随机' . "$after_title</li>";
        $output .= '<li class="">' . $before_title . '最新' . "$after_title</li>";
        $output .= '</ul></div>';

        // Show posts list in each tab
        $output .= '<div class="tabber-content">';
        $output .= '<ul class="">' . dangopress_get_sticky_posts($number, $chars) . '</ul>';
        $output .= '<ul class="hide">' . dangopress_get_most_commented($number, $chars) . '</ul>';
        $output .= '<ul class="hide">' . dangopress_get_rand_posts($number, $chars) . '</ul>';
        $output .= '<ul class="hide">' . dangopress_get_recent_posts($number, $chars) . '</ul>';
        $output .= '</div>';

        $output .= $after_widget;
        echo $output;

        // Set the cache in the end
        $cache[$args['widget_id']] = $output;
        wp_cache_set('widget_dangopress_poststabber', $cache, 'widget');
    }

    /*
     * Flush widget cache
     */
    function flush_widget_cache()
    {
        wp_cache_delete('widget_dangopress_poststabber', 'widget');
    }

    /*
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['number'] = absint($new_instance['number']);
        $instance['chars'] = absint($new_instance['chars']);
        $instance['show_in_home'] = $new_instance['show_in_home'] ? 1 : 0;

        $this->flush_widget_cache();

        return $instance;
    }

    /*
     * Display widget form in the backend admin page
     */
    function form($instance)
    {
        $number = isset($instance['number']) ? absint($instance['number']) : 6;
        $chars = isset($instance['chars']) ? absint($instance['chars']) : 30;
?>
        <p><label for="<?php echo $this->get_field_id('number'); ?>">显示文章数量: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('chars'); ?>">标题显示字数限制: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo $chars; ?>" /></p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_in_home'], true); ?> id="<?php echo $this->get_field_id('show_in_home'); ?>" name="<?php echo $this->get_field_name('show_in_home'); ?>" />
        <label for="<?php echo $this->get_field_id('show_in_home'); ?>">仅在首页显示</label>
        </p>
<?php
    }
}

/*
 * Widget: Recent comments
 */
class Dangopress_RecentComments_Widget extends WP_Widget {
    /*
     * Instantiate the widget object
     */
    function __construct()
    {
        $widget_opts = array(
            'classname' => 'widget-recent-comments',  // widget classname
            'description' => '显示最近 N 个评论'      // widget description
        );

        parent::__construct(
            'dangopress_recentcomments', // widget base id
            '最新评论 [dangopress]',     // widget name
            $widget_opts                 // widget options
        );

        add_action('comment_post', array($this, 'flush_widget_cache'));
        add_action('transition_comment_status', array($this, 'flush_widget_cache'));
    }

    /*
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['chars'] = absint($new_instance['chars']);
        $instance['show_in_home'] = $new_instance['show_in_home'] ? 1 : 0;

        $this->flush_widget_cache();

        return $instance;
    }

    /*
     * Flush widget cache
     */
    function flush_widget_cache()
    {
        wp_cache_delete('widget_dangopress_recentcomments', 'widget');
    }

    /*
     * Display the recent comments widget
     */
    function widget($args, $instance)
    {
        global $wpdb;

        if (($instance['show_in_home'] && !is_home()))
            return;

        // Get the widget content from cache first
        $cache = wp_cache_get('widget_dangopress_recentcomments', 'widget');

        if (!is_array($cache))
            $cache = array();

        if (!isset($args['widget_id']))
            $args['widget_id'] = $this->id;

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        extract($args, EXTR_SKIP);
        $output = "";

        // Get the widget title
        $title = (!empty($instance['title'])) ? $instance['title'] : '最新评论';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        // Get the number of comments
        $number = (!empty($instance['number'])) ? $instance['number'] : 8;

        if (!$number)
            $number = 8;

        // Get the maximum character number of comment content
        $chars = (!empty($instance['chars'])) ? $instance['chars'] : 25;

        if (!$chars)
            $chars = 25;

        $output .= $before_widget;

        if ($title)
            $output .= $before_title . $title . $after_title;

        $output .= '<ul class="recent-comments">';

        // Get the recent comments
        global $table_prefix;
        $sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
            comment_author, comment_date_gmt, comment_date, comment_post_ID,
            comment_approved, comment_type, comment_author_url, comment_author_email,
            SUBSTRING(comment_content,1,$chars) AS com_excerpt
            FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts
            ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID)
            WHERE comment_approved ='1' AND comment_type='' AND post_password=''
            AND comment_author_email NOT IN (
                SELECT A1.user_email FROM $wpdb->users A1, $wpdb->usermeta A2
                WHERE A1.ID = A2.user_id AND A2.meta_key = '" . $table_prefix . "capabilities'
                AND A2.meta_value LIKE '%administrator%'
            ) ORDER BY comment_date_gmt DESC LIMIT $number";

        $comments = $wpdb->get_results($sql);

        if ($comments) {
            $post_ids = array_unique(wp_list_pluck($comments, 'comment_post_ID'));
            _prime_post_caches($post_ids, strpos(get_option('permalink_structure'), '%category%'), false);

            foreach ($comments as $comment) {
                $avatar = get_avatar($comment, 32, '', "$comment->comment_author's avatar");
                $time_diff = show_human_time_diff($comment->comment_date_gmt);
                $comment_link = get_comment_link($comment);

                $output .= '<li class="clearfix rc_item">' . $avatar;
                $output .= '<div class="rc_info">';
                $output .= '<a href="' . $comment_link . '" title="《' . $comment->post_title . '》上的评论">';
                $output .= '<span class="rc_name">' . strip_tags($comment->comment_author) . '</span></a>';
                $output .= '<span class="rc_time">' . $time_diff . '</span>';
                $output .= '<p class="rc_com">' . strip_tags($comment->com_excerpt) . '</p>';
                $output .= '</div></li>';
            }
        }

        $output .= '</ul>';
        $output .= $after_widget;
        echo $output;

        // Set the cache in the end
        $cache[$args['widget_id']] = $output;
        wp_cache_set('widget_dangopress_recentcomments', $cache, 'widget');
    }

    /*
     * Display widget form in the backend admin page
     */
    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 8;
        $chars = isset($instance['chars']) ? absint($instance['chars']) : 25;
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">标题: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('number'); ?>">显示评论数量: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('chars'); ?>">评论显示字数限制: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo $chars; ?>" /></p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_in_home'], true); ?> id="<?php echo $this->get_field_id('show_in_home'); ?>" name="<?php echo $this->get_field_name('show_in_home'); ?>" />
        <label for="<?php echo $this->get_field_id('show_in_home'); ?>">仅在首页显示</label>
        </p>
<?php
    }
}

/*
 * Widget: custom links
 */
class Dangopress_Links_Widget extends WP_Widget {
    /*
     * Instantiate the widget object
     */
    function __construct()
    {
        $widget_opts = array(
            'classname' => 'widget-links',              // widget classname
            'description' => '显示自定义链接菜单'       // widget description
        );

        parent::__construct(
            'dangopress_links',              // widget base id
            '自定义链接菜单 [dangopress]',   // widget name
            $widget_opts                     // widget options
        );
    }

    /*
     * Display the links widget
     */
    function widget($args, $instance) {
        // Get menu
        $nav_menu = !empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object($instance['nav_menu']) : false;

        // Only show the widget in the home page if show_in_home options is set
        if (($instance['show_in_home'] && !is_home()) || !$nav_menu)
            return;

        $title = empty($instance['title']) ? '' : $instance['title'];
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        echo $args['before_widget'];

        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        wp_nav_menu(array(
            'container' => false,
            'fallback_cb' => '',
            'menu' => $nav_menu,
            'menu_class' => 'clearfix menu'
        ));

        echo $args['after_widget'];
    }

    /*
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['nav_menu'] = (int) $new_instance['nav_menu'];
        $instance['show_in_home'] = $new_instance['show_in_home'] ? 1 : 0;

        return $instance;
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $nav_menu = isset($instance['nav_menu']) ? $instance['nav_menu'] : '';

        // Get menus
        $menus = get_terms('nav_menu', array( 'hide_empty' => false));

        // If no menus exists, direct the user to go and create some.
        if (!$menus) {
            printf('<p>请先<a href="%s">创建自定义菜单</a>, 菜单由链接组成</p>', admin_url('nav-menus.php'));
            return;
        }
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
            <select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
        <?php
            foreach ( $menus as $menu ) {
                echo '<option value="' . $menu->term_id . '"'
                    . selected( $nav_menu, $menu->term_id, false )
                    . '>'. $menu->name . '</option>';
            }
        ?>
            </select>
        </p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_in_home'], true); ?> id="<?php echo $this->get_field_id('show_in_home'); ?>" name="<?php echo $this->get_field_name('show_in_home'); ?>" />
        <label for="<?php echo $this->get_field_id('show_in_home'); ?>">仅在首页显示</label>
        </p>
<?php
    }
}

/*
 * Widget: Show Most viewed posts recently
 */
class Dangopress_MostViewedPosts_Widget extends WP_Widget {
    /*
     * Instantiate the widget object
     */
    function __construct()
    {
        $widget_opts = array(
            'classname' => 'widget-most-viewed-posts',  // widget classname
            'description' => '基于 WP PostViews Plus 插件, 显示最近一段时间内最热门的文章' // widget description
        );

        parent::__construct(
            'dangopress_most_viewed_posts', // widget base id
            '热门文章 [dangopress]',       // widget name
            $widget_opts                   // widget options
        );

        add_action('save_post', array($this, 'flush_widget_cache'));
        add_action('deleted_post', array($this, 'flush_widget_cache'));
    }

    /*
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['days'] = absint($new_instance['days']);
        $instance['chars'] = absint($new_instance['chars']);

        $this->flush_widget_cache();
        return $instance;
    }

    /*
     * Display widget form in the backend admin page
     */
    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $days = isset($instance['days']) ? absint($instance['days']) : 30;
        $chars = isset($instance['chars']) ? absint($instance['chars']) : 45;
?>

        <p><label for="<?php echo $this->get_field_id('title'); ?>">标题: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('number'); ?>">显示文章数量: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('days'); ?>">最近天数: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="text" value="<?php echo $days; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('chars'); ?>">标题显示字数限制: </label>
        <input class="widefat" id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo $chars; ?>" /></p>
<?php
    }

    /*
     * Flush widget cache
     */
    function flush_widget_cache()
    {
        wp_cache_delete('widget_dangopress_most_viewed_posts', 'widget');
    }

    /*
     * Display the recent comments widget
     */
    function widget($args, $instance)
    {
        global $wpdb;

        // Get the widget content from cache first
        $cache = wp_cache_get('widget_dangopress_most_viewed_posts', 'widget');

        if (!is_array($cache))
            $cache = array();

        if (!isset($args['widget_id']))
            $args['widget_id'] = $this->id;

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        extract($args, EXTR_SKIP);

        // Get the widget title
        $title = (!empty($instance['title'])) ? $instance['title'] : '最新评论';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        // Get the post number to be showed
        $number = (!empty($instance['number'])) ? $instance['number'] : 5;

        if (!$number)
            $number = 5;

        // Get the recent days to be showed
        $days = (!empty($instance['days'])) ? $instance['days'] : 30;

        if (!$days)
            $days = 30;

        // Get the maximum character number of post title
        $chars = (!empty($instance['chars'])) ? $instance['chars'] : 45;

        if (!$chars)
            $chars = 45;

        $output = "";
        $output .= $before_widget;

        if ($title)
            $output .= $before_title . $title . $after_title;

        $output .= '<ul class="">';
        $output .= get_timespan_most_viewed('post', $number, $chars, false, false, $days);
        $output .= '</ul>';
        $output .= $after_widget;

        echo $output;

        // Set the cache in the end
        $cache[$args['widget_id']] = $output;
        wp_cache_set('widget_dangopress_most_viewed_posts', $cache, 'widget');
    }
}

/*
 * Register all the widgets
 */
function dangopress_widget_init()
{
    register_widget('Dangopress_PostsTabber_Widget');
    register_widget('Dangopress_RecentComments_Widget');
    register_widget('Dangopress_Links_Widget');

    if (function_exists('get_timespan_most_viewed'))
        register_widget('Dangopress_MostViewedPosts_Widget');
}

add_action('widgets_init', 'dangopress_widget_init');
?>
