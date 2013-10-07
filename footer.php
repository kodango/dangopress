<?php
/**
 * Footer.php outputs the code for footer hooks and closing body/html tags
 *
 * @package dangopress
 */
?>
	
        </div> <!-- end primary -->

        <?php get_sidebar(); ?>
    </div> <!-- end container -->
    </div> <!-- end content -->

    <div id="footer">
        <div class="container clearfix">
            <p>
                <span>Copyright &copy; 2012-2013 <?php bloginfo('name'); ?>.</span>
                <span class="designed-by">Theme designed by <a href="<?php bloginfo('url'); ?>">kodango</a>.</span>
            </p>
            <p>
                <?php
                    $options = get_option('dangopress_option');
                    $sitemap = $options['sitemap_xml'];

                    if (!empty($sitemap))
                        echo '<a rel="nofollow" href="' . get_bloginfo('url') . '/' . $sitemap . '">站点地图</a>';
                ?>

                <a href="#backtop" title="回到顶部" class="backtop">回到顶部<i class="icon-arrow-up"></i></a>
            </p>
            <?php wp_footer(); ?>
        </div>
    </div>

</div> <!-- end page -->
</body>
</html>
