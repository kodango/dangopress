<?php
/*
 * Comments.php
 *
 * @package dangopress
 */

/* If a post password is required or no comments are given and comments/pings are closed, return. */
if (post_password_required() || (!have_comments() && !comments_open() && !pings_open()))
    return;
?>

<?php
    $comment_count = get_comments_number();
    $comments_by_type = &separate_comments($comments);

    $trackbacks = $comments_by_type['pings'];
    $tb_count = count($trackbacks);
    $co_count = $comment_count - $tb_count;
?>

<div id="comments" class="comments-area">
    <meta itemprop="interactionCount" content="UserComments:<?php echo $comment_count;?>">

    <div id="comments-tabber" class="clearfix">
	    <a id="reviews-tab" class="curtab" rel="nofollow"><span><?php echo $co_count; ?></span> 条评论</a>
	<?php if ($tb_count != 0): ?>
        <a id="trackbacks-tab" class="tab" rel="nofollow"><span><?php echo $tb_count; ?></span> 次引用</a>
    <?php endif; ?>
    </div>

    <div id="reviews">

    <?php
        // custom fields
        $fields = array(
            'author' => '<input id="author" name="author" type="text" placeholder="昵称*" value="' . esc_attr($comment_author) . '" size="30" aria-required="true" /><label for="author">昵称*</label>',
            'email' => '<input id="email" name="email" type="text" placeholder="邮箱*" value="' . esc_attr($comment_author_email) . '" size="30" aria-required="true" /><label for="author">邮箱*</label>',
            'url' => '<input id="url" name="url" type="text" placeholder="网站" value="' . esc_attr($comment_author_url) . '" size="30" /><label for="author">网站*</label>',
            'email_notify' => '<input type="checkbox" name="comment_mail_notify" id="comment_mail_notify" value="" checked="checked" /><label for="comment_mail_notify">有人回复时邮件通知我</label>',
        );

        // custom comment args
        $comments_args = array(
            'fields' => $fields,
            'title_reply'=> '',
            'cancel_reply_link' => '取消回复',
            'comment_notes_before' => '',
            'comment_notes_after' => '',
            'comment_field' => '<div id="comment-textarea"><textarea id="comment" name="comment" aria-required="true" rows="8"></textarea></div>',
            'label_submit' => '提交回复',
        );

        if (!is_user_logged_in() && !empty($comment_author)) {
            $welcome_login = '<p id="welcome-login"><span>欢迎回来, <strong>' . $comment_author . '</strong>.</span>';
            $welcome_login .=  ' <span id="toggle-author"><u>更改</u> <i class="icon-signout"></i></span></p>';

            $comments_args['comment_field'] = '</div>' . $comments_args['comment_field'];
            $comments_args['comment_notes_before'] = $welcome_login . '<div id="author-info" class="hide">';
        }

        // show comment form
        comment_form($comments_args);
    ?>
    
    <?php if ( have_comments() ): ?>

        <ol class="commentlist">
            <?php wp_list_comments(array('callback' => 'dangopress_comments_callback', 'type' => 'comment', 'max_depth' => 30)); ?>
        </ol>

        <?php if (get_option('page_comments')): ?>
        <div class="comment-pagenavi clearfix">
            <div class="alignright">
                <?php previous_comments_link('<i class="icon-circle-arrow-left"></i> 旧评论'); ?>
                <?php next_comments_link('新评论 <i class="icon-circle-arrow-right"></i>'); ?>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>
    </div>

    <div id="trackbacks">
        <ol class="trackback-list">

        <?php foreach ($trackbacks as $comment) : ?>
            <li id="comment-<?php comment_ID( ); ?>" class="trackback">
                <?php comment_author_link(); ?>
                <small><?php comment_time();?></small>
            </li>  
        <?php endforeach; ?>

        </ol>
    </div>
</div>
