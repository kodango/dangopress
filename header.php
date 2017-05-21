<?php
/**
 * Header.php is generally used on all the pages of your site and is called somewhere near the top
 * of your template files. It's a very important file that should never be deleted.
 *
 * @package dangopress
 */ ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns#" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header>
    <div class="container">
        <hgroup class="logo left">
            <?php if (is_home()): ?>
            <h1><a href="<?php echo esc_url(home_url()); ?>" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
            <?php else: ?>
            <a href="<?php echo esc_url(home_url()); ?>" title="<?php bloginfo('name'); ?>" rel="home"><?php bloginfo('name'); ?></a>
            <?php endif; ?>
        </hgroup>
        <nav class="menubar right" role="navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">
        <?php
            $nav_menu = wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => '',
                'menu_class' => "menu clearfix",
                "before" => '<span itemscope="itemscope" itemtype="http://www.schema.org/SiteNavigationElement">',
                "after" => '</span>',
                "link_before" => '<span itemprop="name">',
                "link_after" => '</span>',
                "echo" => false
            ));
            echo preg_replace('/<a /', '<a itemprop="url" ', $nav_menu);
        ?>
        </nav>
    </div>
</header>

<section id="content">
    <div class="container">
        <div id="primary" class="left">
            <?php dangopress_breadcrumb(); ?>
