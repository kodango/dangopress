<?php
/*
 * Personal custom functions file
 */

/*
 * Remove script/style handlers
 */
function dangopress_remove_scripts()
{
    // Remove yarpp widget styles
    wp_dequeue_style('yarppWidgetCss');
}
add_action('wp_enqueue_scripts', 'dangopress_remove_scripts');

/*
 * Remove script/style handlers in the footer
 */
function dangopress_remove_footer_scripts()
{
    // Remove yarpp related posts styles
    wp_dequeue_style('yarppRelatedCss');
}
add_action('get_footer', 'dangopress_remove_footer_scripts');
?>
