<?php
/**
 * Header.php is generally used on all the pages of your site and is called somewhere near the top
 * of your template files. It's a very important file that should never be deleted.
 *
 * @package dangopress
 */ ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns#" dir="ltr" lang="zh-CN">
<head profile="http://gmpg.org/xfn/11" >
<title><?php dangopress_wp_title('-'); ?></title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header>
    <div class="container">
        <hgroup class="logo left">
            <?php if (is_home()): ?>
            <h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
            <?php else: ?>
            <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a>
            <?php endif; ?>
        </hgroup>
        <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => 'nav',
                'container_class' => 'menubar right',
                'menu_class' => "menu clearfix"
            ));
        ?>
    </div>
</header>

<section id="content">
    <div class="container">
        <div id="primary" class="left">
            <?php dangopress_breadcrumb(); ?>
