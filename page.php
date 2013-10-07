<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package dangopress
 */

get_header();?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post-header">
        <h1 class="post-title"><?php the_title(); ?></h1>
    </div>

    <div class="post-meta clearfix">
        <span class="post-time"><i class="icon-calendar"></i><?php the_time('Y/m/d'); ?></span>
        <span class="post-author"><i class="icon-user"></i>by <?php the_author_link(); ?></span>
        <?php if (function_exists('the_user_views')): ?>
            <span class="post-view"><i class="icon-sun"></i><?php the_user_views(); ?></span>
        <?php endif; ?>
        <span class="post-comment">
            <i class="icon-comments"></i><?php comments_popup_link('抢沙发', '1 个评论', '% 个评论', 'comments-link' ); ?>
       </span>
    </div>

    <div <?php post_class(); ?>>
        <div class="post-content clearfix"><?php the_content(); ?></div>
    </div>

<?php endwhile; endif; ?>

<?php comments_template(); ?>

<?php get_footer(); ?>
