<?php
/*
 * Template Name: Archives
 *
 * @package dangopress
 */

get_header();

# Create a new query
$new_query = new WP_Query('posts_per_page=-1&ignore_sticky_posts=1');

# Remember the last year and last month when iterate the year/month
$last_year = 0;
$last_mon = 0;

# Archives output html
$output = '';

while ($new_query->have_posts() ): $new_query->the_post();
    # Get the current post year and month
    $curr_year = get_the_time('Y');
    $curr_mon = get_the_time('n');

    # Add closing tags
    if ($last_mon > 0 && $last_mon != $curr_mon)
        $output .= '</div></div>';

    # Stores the year
    if ($last_year != $curr_year) {
        $last_year = $curr_year;
    }

    # Stores the month
    if ($last_mon != $curr_mon) {
        $last_mon = $curr_mon;

        $output .= "<div class='year-archives' id='arti-$curr_year-$curr_mon'>";
        $output .= "<h3 class='archive-title'>$curr_year-$curr_mon</h3>";
        $output .= "<div class='month-archives archives-$curr_mon' id='arti-$curr_year-$curr_mon'>";
    }

    $output .= '<div class="archive-item"><a href="' . get_permalink() . '">';
    $output .= '<span class="post-time">' . get_the_time('n-d') . '</span>' . get_the_title();
    $output .= '<span class="comment-num">(' . get_comments_number('0', '1', '%') . ')</span>';
    $output .= '</a></div>';
endwhile;

# Add closing tags
$output .= '</div></div>';

# Reset the post to current post
wp_reset_postdata();
?>

<div <?php post_class(); ?>>
    <div class="clearfix post-content">
        <div id="archives-content"><?php echo $output; ?></div>
    </div> <!-- end post-content -->
 </div> <!-- end post -->

<?php get_footer(); ?>
