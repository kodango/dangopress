<?php
/*
 * Template Name: Archives
 */

get_header();

query_posts('posts_per_page=-1');

$prev_post_ts = null;
$prev_post_year = null;
?>

<div <?php post_class(); ?>>
    <div class="clearfix post-content">

    <?php while (have_posts()): the_post();
        $post_ts = strtotime($post->post_date);
        $post_year = date('Y', $post_ts);

        if (($prev_post_year != $post_year)) {
            if (!is_null($prev_post_year)) { /* Close off the UL */?>

            </ul>

        <?php
            }
        ?>

            <h3 class="archive-year">
                <a href="<?php echo get_year_link($post_year); ?>"><?php echo $post_year . ' 年'; ?></a>
            </h3>
            <ul class="archives-list">

        <?php
        } ?>

               <li class="archive-item clearfix">
                   <div class="alignleft">
                       <span class="archive-date"><?php the_time('m-d'); ?></span>
                   </div>
                   <div class="aligncenter">
                       <span class="post-link"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
                   </div>
                   <div class="alignright">
                       <span class="comments-num"><?php comments_popup_link('0 个评论', '1 个评论', '% 个评论', '', '评论关闭'); ?></span>
                   </div>
               </li>

        <?php

        /* For subsequent iterations */
        $prev_post_ts = $post_ts;
        $prev_post_year = $post_year;
    endwhile;

    /* If we've processed at least *one* post, close the ordered list */
    if (!is_null( $prev_post_ts)) { ?>

            </ul>

    <?php
    } ?>
    </div>
</div>

<?php get_footer(); ?>
