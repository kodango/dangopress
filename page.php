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
    <article <?php post_class(); ?>>
        <div class="entry-content"><?php the_content(); ?></div>
    </article>
<?php endwhile; endif;

comments_template();
get_footer();
?>
