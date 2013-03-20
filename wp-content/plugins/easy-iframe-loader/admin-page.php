<?php
if (is_user_logged_in() && is_admin() ){
    
$devOptions = $this->getAdminOptions();
// print_r($devOptions);
            if (isset($_POST['update_iframe-loader'])) {//save option changes
     $adminSettings = array( 'youtube_height', 'youtube_width',
                    'amazon_height','amazon_width',
                    'vimeo_height', 'vimeo_width',
                    'basic_height', 'basic_width',
                    'widget_height', 'widget_width',
                        );
      foreach ($adminSettings as $item){
            $devOptions[$item] = $_POST[$item];
        }
if (isset($_POST['devloungeContent'])) {$devOptions['content'] = apply_filters('content_save_pre', $_POST['devloungeContent']);}
update_option($this->adminOptionsName, $devOptions);?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "iframe-loader");?></strong></p></div>
<?php } ?>
<style type="text/css">
    table th {text-align: left;}
    </style>
<form method="post" action="options-general.php?page=easy-iframe-loader.php">

        <div class="wrap">
                <?php wp_nonce_field('update-options'); ?>
                <h1><?php _e('Easy iFrame Loader Setup Screen','iframe-loader'); ?></h1>
                <h2><?php _e('Setup default screen sizes','iframe-loader'); ?></h2>
                <p><?php _e('Entering default values here will setup the shortcodes so that they are perfect for your site','iframe-loader'); ?></p>
                <table>
                    <thead>
                        <tr>
                            <th style="width:12em"><?php _e('Type of iFrame','iframe-loader'); ?></th><th style="width:6em"><?php _e('Width','iframe-loader'); ?></th><th  style="width:6em"><?php _e('Height','iframe-loader'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $args = array ('youtube','vimeo','amazon','basic', 'widget',); ?>
                    <?php foreach ($args as $item) {?>
                        <tr>
                            <th><label><?php echo $item ?></label></th>
                            <td><input type="text" value="<?php echo $devOptions[$item."_width"] ?>" name="<?php echo $item ?>_width" /></td>
                            <td><input type="text" value="<?php echo $devOptions[$item."_height"] ?>" name="<?php echo $item ?>_height" /></td>
                        </tr>
                        <?php } ?>
                        
                        
                    </tbody>
                </table>
<p><?php _e('<strong><i>important:</i></strong> When entering a value either enter a PX value without the px at the end or a percentage value, remembering to leave the % sign at the end. The amazon sizes applies to an A Store iFrame only','iframe-loader'); ?></p>
                 <input type="submit" name="update_iframe-loader" value="<?php _e('Update Settings', 'iframe-loader') ?>" />
 <h2><?php _e('Integration with Share and Follow','iframe-loader'); ?></h2>
 <p><?php _e('This plugin automatically Integrates with the <a href="http://share-and-follow.com">Share and Follow</a> plugin by inserting a custom feild of "image_src" when using Youtube or Vimeo for videos.','iframe-loader'); ?></p>
 <p><?php _e('This is very handy as it means that when you share your page in facebook, linked in or others it will automatically put the video thumbnail as part of the post.','iframe-loader'); ?></p>
 <h2><?php _e('Please Donate I need Diapers/Nappies!','iframe-loader'); ?></h2>
 <p><?php _e('Yep you read it right, on the 25th of May I gained a new son, and he needs Diapers/Napies and much more, so I need your help just like you have received mine.','iframe-loader'); ?></p>
 <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----&lt;br /&gt;
"><br>
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG_global.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"><br>
</form>
 <p><?php _e('All donations valued greatly','iframe-loader'); ?></p>
        </div>
     

    </form>

    
<?php }  ?>