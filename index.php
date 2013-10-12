<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme and one of the
 * two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package dangopress
 */

get_header(); ?>

<?php if (is_category()) dangopress_category_description(); ?>

<div id="articlelist">
    <?php if ( have_posts( )) : while ( have_posts() ) : the_post(); ?>
    <div <?php post_class(); ?>>
        <div class="post-header">
            <h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
        </div>
        <div class="post-meta clearfix">
            <span class="post-time"><i class="icon-calendar"></i><?php the_time('Y/m/d'); ?></span>
            <span class="post-cat"><i class="icon-folder-close"></i><?php the_category(' '); ?></span>
            <?php if (function_exists('the_user_views')): ?>
                <span class="post-view"><i class="icon-sun"></i><?php the_user_views(); ?></span>
            <?php endif; ?>
            <span class="post-comment">
                <i class="icon-comments"></i><?php comments_popup_link('抢沙发', '1 个评论', '% 个评论', 'comments-link'); ?>
           </span>
        </div>
        <div class="post-content clearfix">
            <?php the_content('查看全文'); ?>
        </div>
    </div>
    <?php endwhile; else: ?>
    <div class="post">
        <div class="post-header">
            <h2 class="post-title">没有找到相关的文章, 也许你对以下文章感兴趣</h2>
        </div>
        <div class="post-content clearfix">
            <ul>
                <?php
                    $rand_posts = get_posts('numberposts=15&orderby=rand');
                    foreach( $rand_posts as $post ) :
                ?>   
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
     <?php endif; ?>
</div>
<?php dangopress_paginate_links(); ?>

<?php get_footer(); ?>
