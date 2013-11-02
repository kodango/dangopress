<?php
/**
 * Footer.php outputs the code for footer hooks and closing body/html tags
 *
 * @package dangopress
 */
?>
	
        </div> <!-- end primary -->

        <?php get_sidebar(); ?>
    </div> <!-- end content -->

    <div id="footer">
        <p>
            <span>Copyright &copy; 2012-2013 <?php bloginfo('name'); ?>.</span>
            <span class="designed-by">主题由 <a href="http://kodango.com">kodango</a> 设计.</span>
        </p>
        <p>
            <?php
                $options = get_option('dangopress_options');
                $sitemap = $options['sitemap_xml'];

                if (!empty($sitemap)) {
                    $link = '<a href="' . get_bloginfo('url') . '/' . $sitemap . '">站点地图</a>';

                    if (!is_home()) {
                        $link = dangopress_nofollow_link($link);
                    }

                    echo $link;
                }
            ?>

            <a href="#backtop" title="回到顶部" class="backtop">回到顶部<i class="icon-circle-arrow-up"></i></a>
        </p>
        <?php wp_footer(); ?>
    </div>

</div> <!-- end page -->
</body>
</html>
