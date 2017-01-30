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

<?php if (have_posts()): while (have_posts()): the_post(); ?>
    <?php if (!function_exists('yoast_breadcrumb')): ?>
    <div class="post-header">
        <h1 class="post-title"><?php the_title(); ?></h1>
    </div>
    <?php endif; ?>

    <div <?php post_class(); ?>>
        <div class="post-content clearfix"><?php the_content(); ?></div>
    </div>
<?php endwhile; endif; ?>

<?php comments_template(); ?>
<?php get_footer(); ?>
