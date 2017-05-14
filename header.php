<?php
/**
 * Header.php is generally used on all the pages of your site and is called somewhere near the top
 * of your template files. It's a very important file that should never be deleted.
 *
 * @package dangopress
 */ ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head profile="http://gmpg.org/xfn/11" >
<title><?php dangopress_wp_title('-'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="header">
    <div class="container">
        <div class="site-logo left-part">
            <?php if (is_home()): ?>
            <h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
            <?php else: ?>
            <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a>
            <?php endif; ?>
        </div>
        <div class="header-menu right-part">
          <div class="toggle-button">
            <div id="toggle-menu"><i class="icon-list"></i></div>
            <div id="toggle-search"><i class="icon-search"></i></div>
          </div>
          <?php wp_nav_menu(array('theme_location' => 'primary', 'container_class' => 'nav-menu', 'menu_class' => "menu clearfix")); ?>
        </div>
    </div>
</div>

<div id="content">
<div class="container">
    <div id="primary" class="left-part"><?php dangopress_breadcrumb(); ?>
