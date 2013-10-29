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

<?php dynamic_sidebar('sidebar'); ?>

<div id="sidebar-follow">
    <?php dynamic_sidebar('sidebar-follow'); ?>
</div>
</div>
