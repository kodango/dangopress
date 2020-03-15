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

<?php
if (is_category()) dangopress_category_description();
if (have_posts()): while (have_posts()): the_post();
?>

    <article <?php post_class(); ?>>
        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
        <div class="entry-meta">
            <span class="date"><i class="icon-calendar"></i><?php echo date('Y-m-d', get_the_time('U')); ?></span>
            <span class="category"><i class="icon-folder"></i><?php the_category(' '); ?></span>
            <?php if (function_exists('the_user_views')): ?>
                <span class="views"><i class="icon-eye"></i><?php the_user_views(); ?></span>
            <?php endif; ?>
            <span class="entry-comment">
                <i class="icon-comment"></i><?php comments_popup_link('抢沙发', '1 个评论', '% 个评论', 'comments-link'); ?>
           </span>
        </div>
        <div class="entry-content"><?php the_content('继续阅读'); ?></div>
    </article>

<?php endwhile; else: ?>

    <article>
        <h2 class="entry-title">没有找到相关的文章, 也许你对以下文章感兴趣</h2>
        <div class="entry-content">
            <ul>
                <?php
                    $rand_posts = get_posts('numberposts=15&orderby=rand');
                    foreach( $rand_posts as $post ) :
                ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </article>

<?php endif;
dangopress_paginate_links();
get_footer();
?>
