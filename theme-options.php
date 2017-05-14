<?php
/*
 * Theme options page
 */

/*
 * Get theme options
 */
function dangopress_get_options()
{
    $options = get_option('dangopress_options');
    $defaults = array(
        'cdn_prefix' => '',
        'bdshare_uid' => '',
        'bdtj_siteid' => '',
        'google_webid' => '',
        'adsense_publisher_id' => '',
        'sitemap_xml' => '',
        'using_compressed_files' => true,
        'bing_webmaster_user' => '',
        'home_meta_descripton' => ''
    );

    $options = wp_parse_args($options, $defaults);
    update_option('dangopress_options', $options);

    return $options;
}

/*
 * Add theme option to the admin menu
 */
function dangopress_add_admin_menu()
{
    add_theme_page('主题设置', '主题选项', 'edit_theme_options', basename(__FILE__),
            'dangopress_theme_options');
}

/*
 * Display theme options
 */
function dangopress_theme_options()
{
    $options = dangopress_get_options();

?>

<h2>dangopress 主题设置</h2><br/>

<?php
    if ($_POST['update_themeoptions'] == 'true') {
        foreach ($_POST as $key => $value) {
            if (isset($value) && isset($options[$key]))
                $options[$key] = $value;
        }

        $options['using_compressed_files'] = $_POST['using_compressed_files'] ? true : false;
        update_option('dangopress_options', $options);
        $options = get_option('dangopress_options');
    ?>

    <div id="setting-error-settings_updated" class="updated settings-error">
        <p><strong>设置已保存。</strong></p>
    </div>

<?php
    }

?>

<p>注意: 如果以下某个选项设置为空, 则不会启用该功能。如果当前用户是管理员账号, 不会加载统计代码。</p>

<form method="POST" action="">
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <label for="cdn_prefix">文件托管地址</label> (<a href="http://kodango.com/use-oss-in-wordpress">参考</a>)
        </th>
        <td><input name="cdn_prefix" id="cdn_prefix" type="text" value="<?php echo $options['cdn_prefix']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="home_meta_descripton">首页 Meta Description</label>
        </th>
        <td><textarea name="home_meta_descripton" id="home_meta_descripton" rows="5" class="regular-text code"><?php echo $options['home_meta_descripton']; ?></textarea></td>
    </tr>
    <tr>
        <th>
            <label for="bdshare_uid">百度分享 UID</label> (<a href="http://share.baidu.com/code">帮助</a>)
        </th>
        <td><input name="bdshare_uid" id="bdshare_uid" type="text" value="<?php echo $options['bdshare_uid']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="adsense_publisher_id">Google Adsense Publisher ID</label> (<a href="https://support.google.com/code/answer/73069">帮助</a>)
        </th>
        <td><input name="adsense_publisher_id" id="adsense_publisher_id" type="text" value="<?php echo $options['adsense_publisher_id']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="google_webid">Google Analytics Web ID</label> (<a href="https://developers.google.com/analytics/devguides/collection/gajs/">帮助</a>)
        </th>
        <td><input name="google_webid" id="google_webid" type="text" value="<?php echo $options['google_webid']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="bing_webmaster_user">Bing Webmaster User ID</label> (<a href="https://www.bing.com/webmaster/help/getting-started-checklist-66a806de">帮助</a>)
        </th>
        <td><input name="bing_webmaster_user" id="bing_webmaster_user" type="text" value="<?php echo $options['bing_webmaster_user']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="bdtj_siteid">百度统计 Site ID</label> (<a href="http://tongji.baidu.com/open/api/more?p=ref_setAccount">帮助</a>)
        </th>
        <td><input name="bdtj_siteid" id="bdtj_siteid" type="text" value="<?php echo $options['bdtj_siteid']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="sitemap_xml">站点地图文件名（如: sitemap.xml）</label>
        </th>
        <td><input name="sitemap_xml" id="sitemap_xml" type="text" value="<?php echo $options['sitemap_xml']; ?>" class="regular-text code"></td>
    </tr>
    <tr>
        <th>
            <label for="using_compressed_files">使用压缩的 JS/CSS 文件</label>
        </th>
        <td><input type="checkbox" name="using_compressed_files" id="using_compressed_files" value="1"<?php checked('1', $options['using_compressed_files']); ?>></td>
    </tr>
    </tbody>
</table>
<input type="hidden" name="update_themeoptions" value="true" />
<p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改">
</p>
</form>

<?php
}

add_action('admin_menu', dangopress_add_admin_menu);
?>
