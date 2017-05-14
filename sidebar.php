<?php
/**
 * Sidebar.php is used to show your sidebar widgets on pages/posts
 *
 * Learn more here: http://codex.wordpress.org/Customizing_Your_Sidebar
 *
 * @package dangopress
 */
?>

<aside id="sidebar" class="right">
    <div id="sidebar-main"><?php dynamic_sidebar('sidebar'); ?></div>
    <div id="sidebar-follow"><?php dynamic_sidebar('sidebar-follow'); ?></div>
</aside>
