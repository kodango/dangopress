<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dangopress
 */

get_header();?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>
    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Article">
        <meta itemprop="headline" content="<?php the_title(); ?>" />
        <meta itemprop="author" content="<?php the_author(); ?>" />
        <meta itemprop="datePublished" content="<?php echo date('Y-m-d', get_the_time('U')); ?>" />

        <div class="entry-content" itemprop="articleBody">
            <?php the_content(); ?>
        </div>

        <div class="entry-copyright">
            <p>
                <i class="icon-info-circle"></i>
                转载请注明转自: <a href="<?php echo esc_url(home_url()); ?>"><?php bloginfo('name'); ?></a>
                , 本文固定链接:
                <a rel="shortlink" href="<?php echo wp_get_shortlink(); ?>"><?php the_title(); ?></a>
            </p>
        </div>
    </article>

    <?php if (function_exists('related_posts')) related_posts(); ?>

    <div class="entry-footer clearfix">
        <div id="pagination">
            <div class="prev alignleft">
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
            <div class="next alignright">
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
    </div>

<?php endwhile; endif;
comments_template();
get_footer();
?>
