<?php
/**
 * The Template for displaying search form.
 *
 * @package dangopress
 */
?>
<form role="search" action="/" method="get" id="searchform" class="searchform" onsubmit="location.href='<?php echo home_url('/search/'); ?>' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">
    <div>
		<label class="screen-reader-text" for="s">搜索：</label>
		<input type="text" value="wordpress 文章" name="s" id="s" />
 		<input type="submit" id="searchsubmit" value="搜索" />
    </div>
</form>
