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
</section> <!-- end content -->

<footer>
    <div class="container">
       <p class="left">
           <?php dangopress_show_copyright(); ?>
       </p>
       <p class="right">
           <?php dangopress_show_sitemap(); ?>
           <span>
               <a href="#backtop" title="回到顶部" class="backtop">回到顶部<i class="icon-arrow-circle-up"></i></a>
           </span>
       </p>
       <?php wp_footer(); ?>
    </div>
</footer>

</body>
</html>
