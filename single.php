<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dangopress
 */

get_header();?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>
<div <?php post_class(); ?>>
    <div class="post-content clearfix">
        <?php the_content(); ?>
    </div>

    <div class="post-copyright">
        <p>
            <i class="icon-info-circle"></i>
            转载请注明转自: <a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a>
            , 本文固定链接:
            <a rel="shortlink" href="<?php echo wp_get_shortlink(); ?>"><?php the_title(); ?></a>
        </p>
    </div>
</div>

<?php if (function_exists('related_posts')) related_posts(); ?>

<div class="post-footer clearfix">
<div id="post-pagination" class="alignleft">
    <div class="post-prev">
        <span class="icon-chevron-circle-left"></span>
        <?php
            $prev_post = get_previous_post();

            if (!empty($prev_post)) { ?>
                <a rel="prev" href="<?php echo get_permalink($prev_post->ID); ?>"><?php echo $prev_post->post_title; ?></a>
        <?php
            } else {
                echo '<a href="javascript:void(0);">已经是最新一篇文章</a>';
            }
        ?>
    </div>
    <div class="post-next">
        <span class="icon-chevron-circle-right"></span>
        <?php
            $next_post = get_next_post();

            if (!empty($next_post)) { ?>
                <a rel="prev" href="<?php echo get_permalink($next_post->ID); ?>"><?php echo $next_post->post_title; ?></a>
        <?php
            } else {
                echo '<a href="javascript:void(0);">已经是最后一篇文章</a>';
            }
        ?>
    </div>
</div>
<div id="social-share" class="alignright">
    <?php dangopress_place_bdshare(); ?>
</div>
</div>
<?php endwhile; endif; ?>

<?php comments_template(); ?>
<?php get_footer(); ?>
