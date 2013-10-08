<?php
/**
 * Sidebar.php is used to show your sidebar widgets on pages/posts
 * 
 * Learn more here: http://codex.wordpress.org/Customizing_Your_Sidebar
 *
 * @package dangopress
 */
?>

<div id="sidebar">

<div class="widget widget-tabber">
    <div class="tabber-title">
        <ul class="tabnav clearfix">
            <li class="selected""><h3>置顶</h3></li>
            <li class=""><h3>最新</h3></li>
            <li class=""><h3>热评</h3></li>
            <li class=""><h3>随机</h3></li>
       </ul>
    </div>

    <div class="tabber-content">
        <ul class="list"><?php dangopress_get_sticky_posts(8, 30); ?></ul>
        <ul class="list hide"><?php dangopress_get_recent_posts(8, 30); ?></ul>
        <ul class="list hide"><?php dangopress_get_most_commented(8, 180, 30); ?></ul>
        <ul class="list hide"><?php dangopress_get_rand_posts(8, 30); ?></ul>
     </div>
</div>

<div class="widget recent-comments">
    <h3>最新评论</h3>
    <ul><?php dangopress_recent_comments($admin='kodango', $limit=5); ?></ul>
</div>

<?php dynamic_sidebar('sidebar'); ?>

<?php if (!is_single()): ?>

<div class="widget clearfix friend-links">
    <h3>友情链接</h3>
    <ul><?php wp_list_bookmarks('title_li=&categorize=0'); ?><ul>
</div>

<?php endif; ?>

<div id="sidebar-follow">
<?php if (function_exists('get_timespan_most_viewed')): ?>
    <div class="widget widget-popular-posts">
        <h3>本月热门文章</h3>
        <ul class="list">
            <?php get_timespan_most_viewed('post', 5, 45, true, false, 30);?>
        </ul>
    </div>
<?php endif; ?>
</div>

</div>
