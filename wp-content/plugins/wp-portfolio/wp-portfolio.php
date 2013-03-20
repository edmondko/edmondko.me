<?php
/**
 * Plugin Name: Wordpress Portfolio Plugin
 * Plugin URI: http://wordpress.org/extend/plugins/wp-portfolio/
 * Description: A plugin that allows you to show off your portfolio through a single page on your wordpress blog with automatically generated thumbnails. To show your portfolio, create a new page and paste [wp-portfolio] into it. The plugin requires you to have a free account with <a href="http://www.shrinktheweb.com/">Shrink The Web</a> to generate the thumbnails.
 * Version: 1.32
 * Author: Dan Harrison
 * Author URI: http://www.wpdoctors.co.uk 
 
 * This plugin is licensed under the Apache 2 License
 * http://www.apache.org/licenses/LICENSE-2.0
 */


/* Libaries */
include_once('wplib/utils_formbuilder.inc.php');
include_once('wplib/utils_tablebuilder.inc.php');
include_once('wplib/utils_sql.inc.php');

include_once('lib/thumbnailer.inc.php');
include_once('lib/widget.inc.php');
include_once('lib/utils.inc.php');

/* Load translation files */
load_plugin_textdomain('wp-portfolio', false, basename( dirname( __FILE__ ) ) . '/languages' );


/** Constant: The current version of the database needed by this version of the plugin.  */
define('WPP_VERSION', 							'1.31');



/** Constant: The string used to determine when to render a group name. */
define('WPP_STR_GROUP_NAME', 					'%GROUP_NAME%');

/** Constant: The string used to determine when to render a group description. */
define('WPP_STR_GROUP_DESCRIPTION', 	 		'%GROUP_DESCRIPTION%');

/** Constant: The string used to determine when to render a website name. */
define('WPP_STR_WEBSITE_NAME', 	 				'%WEBSITE_NAME%');

/** Constant: The string used to determine when to render a website thumbnail image. */
define('WPP_STR_WEBSITE_THUMBNAIL', 	 		'%WEBSITE_THUMBNAIL%');

/** Constant: The string used to determine when to render a website thumbnail image URL. */
define('WPP_STR_WEBSITE_THUMBNAIL_URL', 	 	'%WEBSITE_THUMBNAIL_URL%');

/** Constant: The string used to determine when to render a website url. */
define('WPP_STR_WEBSITE_URL', 	 				'%WEBSITE_URL%');

/** Constant: The string used to determine when to render a website description. */
define('WPP_STR_WEBSITE_DESCRIPTION', 	 		'%WEBSITE_DESCRIPTION%');

/** Constant: The string used to determine when to render a custom field value. */
define('WPP_STR_WEBSITE_CUSTOM_FIELD', 	 		'%WEBSITE_CUSTOM_FIELD%');

/** Constant: Default HTML to render a group. */
define('WPP_DEFAULT_GROUP_TEMPLATE', 			
"<h2>%GROUP_NAME%</h2>
<p>%GROUP_DESCRIPTION%</p>");

/** Constant: Default HTML to render a website. */
define('WPP_DEFAULT_WEBSITE_TEMPLATE', 			
"<div class=\"portfolio-website\">
    <div class=\"website-thumbnail\"><a href=\"%WEBSITE_URL%\" target=\"_blank\">%WEBSITE_THUMBNAIL%</a></div>
    <div class=\"website-name\"><a href=\"%WEBSITE_URL%\" target=\"_blank\">%WEBSITE_NAME%</a></div>
    <div class=\"website-url\"><a href=\"%WEBSITE_URL%\" target=\"_blank\">%WEBSITE_URL%</a></div>
    <div class=\"website-description\">%WEBSITE_DESCRIPTION%</div>
    <div class=\"website-clear\"></div>
</div>");

/** Constant: Default HTML to render a website in the widget area. */
define('WPP_DEFAULT_WIDGET_TEMPLATE', 			
"<div class=\"widget-portfolio\">
    <div class=\"widget-website-thumbnail\">
    	<a href=\"%WEBSITE_URL%\" target=\"_blank\">%WEBSITE_THUMBNAIL%</a>
    </div>
    <div class=\"widget-website-name\">
    	<a href=\"%WEBSITE_URL%\" target=\"_blank\">%WEBSITE_NAME%</a>
    </div>
    <div class=\"widget-website-description\">
    	%WEBSITE_DESCRIPTION%
    </div>
    <div class=\"widget-website-clear\"></div>
</div>");

/** Constant: Default HTML to render the paging for the websites. */
define('WPP_DEFAULT_PAGING_TEMPLATE', '
<div class="portfolio-paging">
	<div class="page-count">Showing page %PAGING_PAGE_CURRENT% of %PAGING_PAGE_TOTAL%</div>
	%LINK_PREVIOUS% %PAGE_NUMBERS% %LINK_NEXT%
</div>
');


define('WPP_DEFAULT_PAGING_TEMPLATE_PREVIOUS', 	__('Previous', 'wp-portfolio'));
define('WPP_DEFAULT_PAGING_TEMPLATE_NEXT', 		__('Next', 'wp-portfolio'));

/** Constant: Default CSS to style the portfolio. */
define('WPP_DEFAULT_CSS',"
.portfolio-website {
	padding: 10px;
	margin-bottom: 10px;
}
.website-thumbnail {
	float: left;
	margin: 0 20px 20px 0;
}
.website-thumbnail img {
	border: 1px solid #555;
	margin: 0;
	padding: 0;
}
.website-name {
	font-size: 12pt;
	font-weight: bold;
	margin-bottom: 3px;
}
.website-name a,.website-url a {
	text-decoration: none;
}
.website-name a:hover,.website-url a:hover {
	text-decoration: underline;
}
.website-url {
	font-size: 9pt;
	font-weight: bold;
}
.website-url a {
	color: #777;
}
.website-description {
	margin-top: 15px;
}
.website-clear {
	clear: both;
}");

/** Constant: Default CSS to style the paging feature. */
define('WPP_DEFAULT_CSS_PAGING',"
.portfolio-paging {
	text-align: center;
	padding: 4px 10px 4px 10px;
	margin: 0 10px 20px 10px;
}
.portfolio-paging .page-count {
	margin-bottom: 5px;
}
.portfolio-paging .page-jump b {
	padding: 5px;
}
.portfolio-paging .page-jump a {
	text-decoration: none;
}");


/** Constant: Default CSS to style the widget feature. */
define('WPP_DEFAULT_CSS_WIDGET',"
.wp-portfolio-widget-des {
	margin: 8px 0;
	font-size: 110%;
}
.widget-website {
	border: 1px solid #AAA;
	padding: 3px 10px;
	margin: 0 5px 10px;
}
.widget-website-name {
	font-size: 120%;
	font-weight: bold;
	margin-bottom: 5px;
}
.widget-website-description {
	line-height: 1.1em;
}
.widget-website-thumbnail {
	margin: 10px auto 6px auto;
	width: 102px;
}
.widget-website-thumbnail img {
	width: 100px;
	border: 1px solid #555;
	margin: 0;
	padding: 0;
}
.widget-website-clear {
	clear: both;
	height: 1px;
}");


/** Constant: The name of the table to store the website information. */
define('TABLE_WEBSITES', 						'WPPortfolio_websites');

/** Constant: The name of the table to store the website information. */
define('TABLE_WEBSITE_GROUPS', 					'WPPortfolio_groups');

/** Constant: The name of the table to store the debug information. */
define('TABLE_WEBSITE_DEBUG', 					'WPPortfolio_debuglog');

/** Contstant: The path to use to store the cached thumbnails. */
define('WPP_THUMBNAIL_PATH',					'wp-portfolio/cache');

/** Contstant: The name of the setting with the cache setting. */
define('WPP_CACHE_SETTING', 					'WPPortfolio_cache_location');

/** Contstant: The path to use to store the cached thumbnails. */
define('WPP_THUMB_DEFAULTS',					'wp-portfolio/imgs/thumbnail_');

/** Constant: URL location for settings page. */
define('WPP_SETTINGS', 							'admin.php?page=WPP_show_settings');

/** Constant: URL location for settings page. */
define('WPP_DOCUMENTATION', 					'admin.php?page=WPP_show_documentation');

/** Constant: URL location for website summary. */
define('WPP_WEBSITE_SUMMARY', 					'admin.php?page=wp-portfolio/wp-portfolio.php');

/** Constant: URL location for modifying a website entry. */
define('WPP_MODIFY_WEBSITE', 					'admin.php?page=WPP_modify_website');

/** Constant: URL location for showing the list of groups in the portfolio. */
define('WPP_GROUP_SUMMARY', 					'admin.php?page=WPP_website_groups');

/** Constant: URL location for modifying a group entry. */
define('WPP_MODIFY_GROUP', 						'admin.php?page=WPP_modify_group');



/**
 * Function: WPPortfolio_menu()
 *
 * Creates the menu with all of the configuration settings.
 */

function WPPortfolio_menu()
{
	add_menu_page('WP Portfolio - Summary of Websites in your Portfolio', 'WP Portfolio', 'manage_options', __FILE__, 'WPPortfolio_show_websites');
	
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('Modify Website', 'wp-portfolio'), 		'Add New Website', 		'manage_options', 'WPP_modify_website', 'WPPortfolio_modify_website');
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('Modify Group', 'wp-portfolio'), 		'Add New Group', 		'manage_options', 'WPP_modify_group', 'WPPortfolio_modify_group');
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('Groups', 'wp-portfolio'), 				'Website Groups', 		'manage_options', 'WPP_website_groups', 'WPPortfolio_show_website_groups');		
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('General Settings', 'wp-portfolio'), 	'Portfolio Settings', 	'manage_options', 'WPP_show_settings', 'WPPortfolio_showSettingsPage');
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('Layout Settings', 'wp-portfolio'), 	'Layout Settings', 		'manage_options', 'WPP_show_layout_settings', 'WPPortfolio_showLayoutSettingsPage');
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('Documentation', 'wp-portfolio'), 		'Documentation', 		'manage_options', 'WPP_show_documentation', 'WPPortfolio_showDocumentationPage');

	$errorCount = WPPortfolio_errors_getErrorCount();
	$errorCountMsg = false;
	if ($errorCount > 0) {
		$errorCountMsg = sprintf('<span title="%d Error" class="update-plugins"><span class="update-count">%d</span></span>', $errorCount, $errorCount);
	}
	
	add_submenu_page(__FILE__, 'WP Portfolio - '.__('Error Logs', 'wp-portfolio'), 		__('Error Logs', 'wp-portfolio').$errorCountMsg, 'manage_options', 'WPP_show_error_page', 'WPPortfolio_showErrorPage');
}


/**
 * Functions called when plugin initialises with WordPress.
 */
function WPPortfolio_init()
{	
	// Backend
	if (is_admin())
	{
		// Warning boxes in admin area only
		add_action('admin_notices', 'WPPortfolio_messages');
		
		// Menus
		add_action('admin_menu', 'WPPortfolio_menu');
		
		// Scripts and styles
		add_action('admin_print_scripts', 'WPPortfolio_scripts_Backend'); 
		add_action('admin_print_styles',  'WPPortfolio_styles_Backend');	
	}
	
	// Frontend
	else {
		
		// Scripts and styles
		add_action('wp_head', 'WPPortfolio_styles_frontend_renderCSS');
		WPPortfolio_scripts_Frontend();
	}
	
	// Common
	// Add settings link to plugins page
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'WPPortfolio_plugin_addSettingsLink');
}
add_action('init', 'WPPortfolio_init');



/**
 * Messages to show the user in the admin area.
 */
function WPPortfolio_messages()
{
	// Request that the user selects an account type.
	$accountType = get_option('WPPortfolio_setting_stw_account_type');
	if ($accountType != 'free' && $accountType != 'paid') {
		WPPortfolio_showMessage(sprintf(__('WP Portfolio has been upgraded, and there\'s been a slight settings change. Please choose your Shrink The Web account type in the <a href="%s#stw-account">Portfolio Settings</a>', 
		'wp-portfolio'), WPP_SETTINGS), true);
	}
}


/**
 * Determine if we're on a page just related to WP Portfolio in the admin area.
 * @return Boolean True if we're on a WP Portfolio admin page, false otherwise.
 */
function WPPortfolio_areWeOnWPPPage()
{
	if (isset($_GET) && isset($_GET['page']))
	{ 
		$currentPage = $_GET['page'];
		
		// This handles any WPPortfolio page.
		if ($currentPage == 'wp-portfolio/wp-portfolio.php' || substr($currentPage, 0, 4) == 'WPP_') {
			return true;
		}	
	}
	 
	return false;
}


/**
 * Show the main settings page.
 */
function WPPortfolio_showSettingsPage() {	
?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32">
	<br/>
	</div>
	<h2>WP Portfolio - <?php _e('General Settings'); ?></h2>
<?php 	

	$settingsList = WPPortfolio_getSettingList(true, false);
	
	// Get all the options from the database for the form
	$settings = array();
	foreach ($settingsList as $settingName => $settingDefault) {
		$settings[$settingName] = stripslashes(get_option('WPPortfolio_'.$settingName)); 
	}
		
	// If we don't have the version in the settings, we're not installed
	if (!get_option('WPPortfolio_version')) {
		WPPortfolio_showMessage(sprintf(__('No %s settings were found, so it appears that the plugin has been uninstalled. Please <b>deactivate</b> and then <b>activate</b> the %s plugin again to fix this.', 'wp-portfolio'), 'WP Portfolio', 'WP Portfolio'), true);
		return false;
	}
	
	// #### UNINSTALL - Uninstall plugin?
	if (WPPortfolio_getArrayValue($_GET, 'uninstall') == "yes")
	{
		if ($_GET['confirm'] == "yes") {
			WPPortfolio_uninstall();
		}
		else {
			WPPortfolio_showMessage(sprintf(__('Are you sure you want to delete all %s settings and data? This action cannot be undone!', 'wp-portfolio'), 'WP Portfolio') .'</strong><br/><br/><a href="'.WPP_SETTINGS.'&uninstall=yes&confirm=yes">' . __('Yes, delete.', 'wp-portfolio') . '</a> &nbsp; <a href="'.WPP_SETTINGS.'">' . __('NO!', 'wp-portfolio') . '</a>');
		}
		return false;
	} // end if ($_GET['uninstall'] == "yes")		
	
	
	// #### CACHE - Check if clearing cache 
	else if ( isset($_POST) && isset($_POST['clear_thumb_cache']) ) 
	{
		$actualThumbPath = WPPortfolio_getThumbPathActualDir();
		
		// Delete all contents of directory but not the root
		WPPortfolio_unlinkRecursive($actualThumbPath, false);
				
		WPPortfolio_showMessage(__('Thumbnail cache has now been emptied.', 'wp-portfolio'));
				
	}
		
	
	// #### SETTINGS - Check if updated data.
	else if (WPPortfolio_getArrayValue($_POST, 'update') == 'general-settings')
	{
		// Copy settings from $_POST
		$settings = array();
		foreach ($settingsList as $settingName => $settingDefault) 
		{
			$settings[$settingName] = WPPortfolio_getArrayValue($_POST, $settingName);			 			
		}		
		
		// Validate keys
		if (WPPortfolio_isValidKey($settings['setting_stw_access_key']) && 
			WPPortfolio_isValidKey($settings['setting_stw_secret_key']))
		{		
			// Save settings
			foreach ($settingsList as $settingName => $settingDefault) {
				update_option('WPPortfolio_'.$settingName, $settings[$settingName]); 
			}
								
			WPPortfolio_showMessage();		
		}
		else {
			WPPortfolio_showMessage(__('The keys must only contain letters and numbers. Please check that they are correct.', 'wp-portfolio'), true);
		}
	}	

	// #### Table UPGRADE - Check if forced table upgrade
	else if (WPPortfolio_getArrayValue($_POST, 'update') == 'tables_force_upgrade')
	{
		WPPortfolio_showMessage(__('Upgrading WP Portfolio Tables...', 'wp-portfolio'));
		flush();		
		WPPortfolio_install_upgradeTables(true, false, false);
		WPPortfolio_showMessage(sprintf(__('%s tables have successfully been upgraded.', 'wp-portfolio'), 'WP Portfolio') );
	}
	
	// #### CODEPAGE UPGRADE - Check if upgrading codepage
	else if (WPPortfolio_getArrayValue($_POST, 'update') == 'codepage_upgrade')
	{
		// Handle the codepage upgrades from default MySQL latin1_swedish_ci to utf8_general_ci to help deal with 
		// other languages
		global $wpdb;
		$wpdb->show_errors;
		
		// Table names
		$table_websites	= $wpdb->prefix . TABLE_WEBSITES;
		$table_groups 	= $wpdb->prefix . TABLE_WEBSITE_GROUPS;
		$table_debug    = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
		
		
		// Website
		$wpdb->query("ALTER TABLE `$table_websites` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query("ALTER TABLE `$table_websites` CHANGE `sitename` 	     `sitename`    	    VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$wpdb->query("ALTER TABLE `$table_websites` CHANGE `siteurl` 		 `siteurl` 			VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$wpdb->query("ALTER TABLE `$table_websites` CHANGE `sitedescription` `sitedescription`  TEXT 		   CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$wpdb->query("ALTER TABLE `$table_websites` CHANGE `customthumb` 	 `customthumb` 		VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
	
		// Groups
		$wpdb->query("ALTER TABLE `$table_groups` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query("ALTER TABLE `$table_groups` CHANGE `groupname` 	    `groupname`    	   VARCHAR( 150 )  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$wpdb->query("ALTER TABLE `$table_groups` CHANGE `groupdescription` `groupdescription` TEXT 		   CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		
		// Debug Log
		$wpdb->query("ALTER TABLE `$table_debug` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query("ALTER TABLE `$table_debug` CHANGE `request_url` 	 `request_url`    VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$wpdb->query("ALTER TABLE `$table_debug` CHANGE `request_detail` `request_detail` TEXT 		     CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		$wpdb->query("ALTER TABLE `$table_debug` CHANGE `request_type`   `request_type`   VARCHAR( 25 )  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
	
		WPPortfolio_showMessage(sprintf(__('%s tables have successfully been upgraded to UTF-8.', 'wp-portfolio'), 'WP Portfolio') );
	}
	
	
	// #### CACHE - Check if changing location 
	else if (WPPortfolio_getArrayValue($_POST, 'update') == 'change_cache_location') 
	{		
		$oldCacheLoc = get_option(WPP_CACHE_SETTING);		
		$newCacheLoc = WPPortfolio_getArrayValue($_POST, 'new_cache_location');

		// Check that we've changed something
		if ($newCacheLoc && $newCacheLoc != $oldCacheLoc)
		{
			// Update the options setting 
			update_option(WPP_CACHE_SETTING, $newCacheLoc);
			
			$newLoc = WPPortfolio_getCacheSetting();
			$oldLoc = ($newLoc == 'wpcontent' ? 'plugin' : 'wpcontent'); 
			
			// Get the full directory paths we need to manipluate the cache files
			$newDirPath = WPPortfolio_getThumbPathActualDir($newLoc);
			$oldDirPath = WPPortfolio_getThumbPathActualDir($oldLoc);
			$newURLPath = WPPortfolio_getThumbPathActualDir($newLoc);
			
			// Create new cache directory
			WPPortfolio_createCacheDirectory($newLoc);
						
			// Copy the files...
			WPPortfolio_fileCopyRecursive($oldDirPath, $newDirPath);
			
			// Remove the old files
			WPPortfolio_unlinkRecursive($oldDirPath, false);
					
			WPPortfolio_showMessage(sprintf(__('The cache location has successfully been changed. The new cache location is now:<br/><br/><code>%s</code>', 'wp-portfolio'), $newURLPath));
		}
		
		// Old and new are the same.
		else {
			WPPortfolio_showMessage(__('The cache location has not changed, therefore there is nothing to do.', 'wp-portfolio'));
		}
	}
	
	
	$form = new FormBuilder('general-settings');
	
	$formElem = new FormElement("setting_stw_access_key", __('STW Access Key ID', 'wp-portfolio'));
	$formElem->value = $settings['setting_stw_access_key'];
	$formElem->description = sprintf(__('The <a href="%s#doc-stw">Shrink The Web</a> Access Key ID is around 15 characters.', 'wp-portfolio'), WPP_DOCUMENTATION);
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("setting_stw_secret_key", __('STW Secret Key', 'wp-portfolio'));
	$formElem->value = $settings['setting_stw_secret_key'];
	$formElem->description = sprintf(__('The <a href="%s#doc-stw">Shrink The Web</a> Secret Key is around 5-10 characters. This key is never shared, it is only stored in your settings and used to generate thumbnails for this website.', 
		'wp-portfolio'), WPP_DOCUMENTATION)."<a name=\"stw-account\"></a>"; // The anchor for the option below
	$form->addFormElement($formElem);
	
	
	$formElem = new FormElement("setting_stw_account_type", __("STW Account Type", 'wp-portfolio'));
	$formElem->value = $settings['setting_stw_account_type'];
	$formElem->setTypeAsComboBox(array('' => __('-- Select an account type --', 'wp-portfolio'), 'free' => __('Free Account', 'wp-portfolio'), 'paid' => __('Paid (Basic or Plus) Account', 'wp-portfolio')));
	$formElem->description = sprintf('&bull; '.__('The type of account you have with <a href="%s#doc-stw">Shrink The Web</a>. ', 'wp-portfolio'), WPP_DOCUMENTATION).
							__('Either a <i>free account</i>, or a <i>paid (basic or plus) account</i>. Your account type determines how the portfolio works.', 'wp-portfolio').'<br/>'.
						 	sprintf('&bull; '.__('Learn more about account types in the <a href="%s" target="_new"> FAQ section.</a>', 'wp-portfolio'), 'http://wordpress.org/extend/plugins/wp-portfolio/faq/');
	$form->addFormElement($formElem);
	
	$form->addBreak('wpp-thumbnails', '<div class="wpp-settings-div">' . __('Thumbnail Settings', 'wp-portfolio') . '</div>');
	
	
	// Thumbnail sizes - Paid Only
	if (WPPortfolio_isPaidAccount())
	{
		$formElem = new FormElement("setting_stw_thumb_size_type", __('What thumbnail sizes do you want to use?', 'wp-portfolio'));
		$formElem->value = $settings['setting_stw_thumb_size_type'];
		$formElem->setTypeAsComboBox(array('standard' => __('Standard STW Sizes', 'wp-portfolio'), 'custom' => __('My own custom sizes', 'wp-portfolio')));
		$formElem->cssclass = 'wpp-size-type';
		$form->addFormElement($formElem);
		
		$formElem = new FormElement("setting_stw_thumb_size_custom", __('Custom Thumbnail Size (Width)', 'wp-portfolio'));
		$formElem->value = $settings['setting_stw_thumb_size_custom'];
		$formElem->cssclass = 'wpp-size-custom';
		$formElem->description = '&bull; '.__('Specify your desired width for the custom thumbnail. STW will resize the thumbnail to be in a 4:3 ratio.', 'wp-portfolio').'<br/>'.
								 '&bull; '.__('This feature requires a STW Paid (Basic or Plus) account with custom thumbnail support.', 'wp-portfolio');
		$formElem->afterFormElementHTML = '<div class="wpp-size-custom-other"></div>';
		$form->addFormElement($formElem);
	}
	
	// Thumbnail sizes - Basic	
	$thumbsizes = array ("sm" => __('Small (120 x 90)', 'wp-portfolio'),
						 "lg" => __('Large (200 x 150)', 'wp-portfolio'),
						 "xlg" => __('Extra Large (320 x 240)', 'wp-portfolio'));
	
	$formElem = new FormElement("setting_stw_thumb_size", __('Thumbnail Size', 'wp-portfolio'));
	$formElem->value = $settings['setting_stw_thumb_size'];
	$formElem->setTypeAsComboBox($thumbsizes);
	$formElem->cssclass = 'wpp-size-select';
	$form->addFormElement($formElem);		
	
	

	
	// Cache days
	$cachedays = array ( "3" => "3 " . __('days', 'wp-portfolio'),
						 "5" => "5 " . __('days', 'wp-portfolio'),
						 "7" => "7 " . __('days', 'wp-portfolio'),
						 "10" => "10 " . __('days', 'wp-portfolio'),
						 "15" => "15 " . __('days', 'wp-portfolio'),
						 "20" => "20 " . __('days', 'wp-portfolio'),
						 "30" => "30 " . __('days', 'wp-portfolio'),
						 "0" => __('Never Expire Thumbnails', 'wp-portfolio'),
						);
	
	$formElem = new FormElement("setting_cache_days", __('Number of Days to Cache Thumbnail', 'wp-portfolio'));
	$formElem->value = $settings['setting_cache_days'];
	$formElem->setTypeAsComboBox($cachedays);
	$formElem->description = __('The number of days to hold thumbnails in the cache. Set to a longer time period if website homepages don\'t change very often', 'wp-portfolio');
	$form->addFormElement($formElem);	
	
	// Thumbnail Fetch Method
	$fetchmethod = array( "curl" => __('cURL (recommended)', 'wp-portfolio'),
						  "fopen" => __("fopen", 'wp-portfolio'));
	
	$formElem = new FormElement("setting_fetch_method", __('Thumbnail Fetch Method', 'wp-portfolio'));
	$formElem->value = $settings['setting_fetch_method'];
	$formElem->setTypeAsComboBox($fetchmethod);
	$formElem->description = __('The type of HTTP call used to fetch thumbnails. fopen is usually less secure and disabled by most web hosts, hence why cURL is recommended.', 'wp-portfolio');
	$form->addFormElement($formElem);		
	
	// Custom Thumbnail Scale Method
	$scalemethod = array( "scale-height" => __('Match height of website thumbnails', 'wp-portfolio'),
						  "scale-width" => __('Match width of website thumbnails', 'wp-portfolio'),
						  "scale-both" => __('Ensure thumbnail is same size or smaller than website thumbnails (default)', 'wp-portfolio') );
	
	$formElem = new FormElement("setting_scale_type", __('Custom Thumbnail Scale Method', 'wp-portfolio'));
	$formElem->value = $settings['setting_scale_type'];
	$formElem->setTypeAsComboBox($scalemethod);

	$formElem->description = __('How custom thumbnails are scaled to match the size of other website thumbnails. This is mostly a matter of style. The thumbnails can match either:', 'wp-portfolio').
							'<br/>&nbsp;&nbsp;&nbsp;&nbsp;'.__('a) <strong>the height</strong> of the website thumbnails (with the width resized to keep the scale of the original image)', 'wp-portfolio').
							'<br/>&nbsp;&nbsp;&nbsp;&nbsp;'.__('b) <strong>the width</strong> of the website thumbnails  (with the height resized to keep the scale of the original image)', 'wp-portfolio').
							'<br/>&nbsp;&nbsp;&nbsp;&nbsp;'.__('c) <strong>the width and the height</strong> of the website thumbnails, where the custom thumbnail is never larger than a website thumbnail, but still scaled correctly.', 'wp-portfolio').
							'<br/>'.__('After changing this option, it\'s recommended to clear the cache so that all custom thumbnails are sized correctly.', 'wp-portfolio');
	$form->addFormElement($formElem);
	
	
	$form->addBreak('wpp-thumbnails', '<div class="wpp-settings-div">' . __('Miscellaneous Settings', 'wp-portfolio') . '</div>');
	
	// Debug mode
	$formElem = new FormElement("setting_enable_debug", __('Enable Debug Mode', 'wp-portfolio'));
	$formElem->value = $settings['setting_enable_debug'];
	$formElem->setTypeAsCheckbox("Enable debug logging");
	$formElem->description = __('Enables logging of successful thumbnail requests too (all errors are logged regardless).', 'wp-portfolio');
	$form->addFormElement($formElem);		
	
	// Show credit link
	$formElem = new FormElement("setting_show_credit", __('Show Credit Link', 'wp-portfolio'));
	$formElem->value = $settings['setting_show_credit'];
	$formElem->setTypeAsCheckbox(__('Creates a link back to WP Portfolio and to WPDoctors.co.uk', 'wp-portfolio'));
	$formElem->description = __("<strong>I've worked hard on this plugin, please consider keeping the link back to my website!</strong> It's the link back to my site that keeps this plugin free!", 'wp-portfolio');
	$form->addFormElement($formElem);	
			
	$form->addButton("clear_thumb_cache", __("Clear Thumbnail Cache", 'wp-portfolio'));
	
	echo $form->toString();
	?>
	
	<p>&nbsp;</p><p>&nbsp;</p>
	<h2><?php _e("Server Compatibility Checker", "wp-portfolio");?></h2>	
	<table id="wpp-checklist">
		<tbody>
			<tr>
				<td><?php _e("PHP Version", "wp-portfolio");?></td>
				<td><?php echo phpversion(); ?></td>
				<td>
					<?php if(version_compare(phpversion(), '5.0.0', '>')) : ?>
						<img src="<?php echo WPPortfolio_getPluginPath(); ?>/imgs/icon_tick.png" alt="Yes" />

                    <?php else : ?>        
						<img src="<?php echo WPPortfolio_getPluginPath(); ?>/imgs/icon_stop.png" alt="No" />
						<span class="wpp-error-info"><?php echo __('WP Portfolio requires PHP 5 or above.', 'wp-portfolio'); ?></span>
					<?php endif; ?>
				</td>
			</tr>	
			
			<tr>
				<?php 					
					// Check for cache path
					$cachePath = WPPortfolio_getThumbPathActualDir(); 
					$isWriteable = (file_exists($cachePath) && is_dir($cachePath) && is_writable($cachePath));
				?>
				<td><?php _e("Writeable Cache Folder", "wp-portfolio");?></td>
				<?php if ($isWriteable) : ?>
					<td><?php _e('Yes'); ?></td>
					<td>					
						<img src="<?php echo WPPortfolio_getPluginPath(); ?>/imgs/icon_tick.png" alt="Yes" />
					</td>
				<?php else : ?>
					<td><?php _e('No'); ?></td>
					<td>        
						<img src="<?php echo WPPortfolio_getPluginPath(); ?>/imgs/icon_stop.png" alt="No" />
						<span class="wpp-error-info"><?php echo __('WP Portfolio requires a directory for the cache that\'s writeable.', 'wp-portfolio'); ?></span>
					</td>
				<?php endif; ?>
			</tr>	
			
			<tr>
				<?php 
					// Check for open_basedir restriction
					$openBaseDirSet = ini_get('open_basedir');
				?>
				<td><?php echo __("open_basedir Restriction", "wp-portfolio");?></td>
				<?php if (!$openBaseDirSet) : ?>
					<td><?php _e('Not Set'); ?></td>
					<td>					
						<img src="<?php echo WPPortfolio_getPluginPath(); ?>/imgs/icon_tick.png" alt="Yes" />
					</td>
				<?php else : ?>
					<td><?php _e('Set'); ?></td>
					<td>        
						<img src="<?php echo WPPortfolio_getPluginPath(); ?>/imgs/icon_stop.png" alt="No" />
						<span class="wpp-error-info"><?php _e("The PHP ini open_basedir setting can cause problems with fetching thumbnails.", "wp-portfolio"); ?></span>
					</td>
				<?php endif; ?>
			</tr>
						
		</tbody>
	</table>
	
	
	
	<p>&nbsp;</p><p>&nbsp;</p>
	<h2><?php _e("Change Cache Location", "wp-portfolio"); ?></h2>
	<p><?php echo __('You can either have the thumbnail cache stored in the <b>plugin directory</b> (which gets deleted when you upgrade the plugin), or you can have the thumbnail cache stored in the <b>wp-content directory</b> (which doesn\'t get deleted when you upgrade wp-portfolio). This is only useful if your thumbnails are set to never be updated and you don\'t want to lose the cached thumbnails.', 'wp-portfolio'); ?></p>
	<dl>
		<dt><?php _e('Plugin Location', 'wp-portfolio'); ?>: <?php if (WPPortfolio_getCacheSetting() == 'plugin') { printf('&nbsp;&nbsp;<i class="wpp-cache-selected">(%s)</i>', __('Currently Selected', 'wp-portfolio')); } ?></dt>
		<dd><code><?php echo WPPortfolio_getThumbPathURL('plugin'); ?></code></dd>	
		
		<dt><?php echo 'wp-content'.__(' Location', 'wp-portfolio'); ?>: <?php if (WPPortfolio_getCacheSetting() == 'wpcontent') { printf('&nbsp;&nbsp;<i class="wpp-cache-selected">(%s)</i>', __('Currently Selected', 'wp-portfolio')); } ?></dt>
		<dd><code><?php echo WPPortfolio_getThumbPathURL('wpcontent'); ?></code></dd>
	</dl>
	
	<?php
	$form = new FormBuilder('change_cache_location');
	
	// List of Cache Locations
	$cacheLocations = array('setting_cache_plugin' => __('Plugin Directory (Recommended)', 'wp-portfolio'), 
							'setting_cache_wpcontent' => __('wp-content Directory', 'wp-portfolio')
						);
	
	$formElem = new FormElement('new_cache_location', __('New Cache Location', 'wp-portfolio'));
	$formElem->setTypeAsComboBox($cacheLocations);
	$form->addFormElement($formElem);
	
	// Set the default location based on current setting.
	$form->setDefaultValues(array('new_cache_location' => get_option(WPP_CACHE_SETTING, true)));
	
	$form->setSubmitLabel(__('Change Cache Location', 'wp-portfolio'));	
	echo $form->toString();
	?>
	
	
	<p>&nbsp;</p>
	<hr>
	
	<h2><?php _e("Upgrade Tables", "wp-portfolio"); ?></h2>
	<p><?php echo __("<p>If you're getting any errors relating to tables, you can force an upgrade of the database tables relating to WP Portfolio.", 'wp-portfolio'); ?></p>
	<?php
	$form = new FormBuilder('tables_force_upgrade');
	$form->setSubmitLabel(__('Force Table Upgrade', 'wp-portfolio'));	
	echo $form->toString();
	?>
	
	<hr>
	
	<h2><?php _e("Upgrade Tables to UTF-8 Codepage (Advanced)", "wp-portfolio"); ?></h2>
	<p><?php echo __('As of V1.18, WP Portfolio uses UTF-8 as the default codepage for all text fields. Previously, for non Latin-based languages, the lack of UTF-8 support caused rendering issues with characters (such as using question marks and blocks for certain characters).', 'wp-portfolio');
			echo __('To upgrade to the new UTF-8 support, just click the button below. If you\'re <b>not experiencing problems</b> with website names and descriptions, then there\'s no need to click this button.</p>', 'wp-portfolio'); ?>
	<?php
	$form = new FormBuilder('codepage_upgrade');
	$form->setSubmitLabel(__('Upgrade Codepage to UTF-8', 'wp-portfolio'));	
	echo $form->toString();
	?>
		
		
		
	<hr>
	<h2><?php _e('Uninstalling WP Portfolio', 'wp-portfolio'); ?></h2>
	<p><?php echo sprintf(__('If you\'re going to permanently uninstall WP Portfolio, you can also <a href="%s">remove all settings and data</a>.</p>', 'wp-portfolio'), 'admin.php?page=WPP_show_settings&uninstall=yes'); ?>
		
	<p>&nbsp;</p>	
	<p>&nbsp;</p>
	</div>
	<?php 	
}




/**
 * Show all the documentation in one place.
 */
function WPPortfolio_showDocumentationPage() 
{

	
	?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32">
	<br/>
	</div>
	
	
	<?php
	echo '<h2>'.__('WP Portfolio - Documentation', 'wp-portfolio').'</h2>';
	
	echo '<p>'.__('All the information you need to run the plugin is available on this page.', 'wp-portfolio').'</p>';	
	
	echo '<h2>'.__('Problems and Support', 'wp-portfolio').'</h2>';
	echo '<p>'.printf(__('Please check the <a href="%s">Frequently Asked Questions</a> page if you have any issues.', 'wp-portfolio'), 'http://wordpress.org/extend/plugins/wp-portfolio/faq/');
	echo printf(__('As a last resort, please raise a problem in the <a href="%s">WP Portfolio Support Forum on Wordpress.org</a>, and I\'ll respond to the ticket as soon as possible. Please be aware, this might be a couple of days.', 'wp-portfolio'), 'http://wordpress.org/tags/wp-portfolio?forum_id=10').'</p>';
	
	echo '<h2>'.__('Comments and Feedback', 'wp-portfolio').'</h2>';
	echo '<p>'.sprintf(__('If you have any comments, ideas or any other feedback on this plugin, please leave comments on the <a href="%s">WP Portfolio Support Forum on Wordpress.org</a>.', 'wp-portfolio'), 'http://wordpress.org/tags/wp-portfolio?forum_id=10').'</p>';
		
	echo '<h2>'.__('Requesting Features', 'wp-portfolio').'</h2>';
	echo '<p>'.sprintf(__('My schedule is extremely busy, and so I have little time to add new features to this plugin. If you are keen for a feature to be implemented, I can add new features in return for a small fee which helps cover my time. Due to running an agency, so my clients are my first priority. By paying a small fee, you effectively become a client, and therefore I can implement desired features more quickly. Please contact me via the <a href="%s">WP Doctors Contact Page</a> if you would like to pay to have a new feature implemented.', 'wp-portfolio'), 'http://www.wpdoctors.co.uk/contact/');
	
	echo '<p>'.sprintf(__('You can see the list of requested features on the <a href="%s">WP Portfolio page</a> on the <a href="%s">WP Doctors</a> website. If you are prepared to wait, I do welcome feature ideas, which can be left on the <a href="%s">WP Portfolio Support Forum on Wordpress.org</a>.', 'wp-portfolio'), 'http://www.wpdoctors.co.uk/our-wordpress-plugins/wp-portfolio/', 'http://www.wpdoctors.co.uk', 'http://wordpress.org/tags/wp-portfolio?forum_id=10').'</p>';
	
	echo '<a name="doc-stw"></a>';
	echo '<h2>'.__('ShrinkTheWeb - Thumbnail Service', 'wp-portfolio').'</h2>';
	echo '<p>'.sprintf(__('The plugin requires you to have a free (or paid) account with <a href="%s" target="_blank">ShrinkTheWeb (STW)</a> if you wish to generate the thumbnails <b>dynamically</b>. Please read <a href="%s" target="_blank">the first FAQ about account types</a> to learn more. If you have a paid account with STW, this plugin will automatically handle the caching of thumbnails to give your website fast loading times.', 'wp-portfolio'), 'http://www.shrinktheweb.com', 'http://wordpress.org/extend/plugins/wp-portfolio/faq/').'</p>';

	echo '<p>'.__('However, you do not need an account with ShrinkTheWeb to use this plugin if you capture screenshots of your websites yourself. Just can capture your own screenshots as images, upload those images to your website, and then link to them in the Custom Thumbnail URL wp-portfolio</b> field.', 'wp-portfolio').'</p>';
	
	echo '<h2>'.__('Portfolio Syntax', 'wp-portfolio').'</h2>';
	echo '<p>'.__('You can use the following syntax for wp-portfolio within any post or page.', 'wp-portfolio').'</p>';
	
	echo '<h3>'.__('Individual websites', 'wp-portfolio').'</h3>';
	echo '<ul class="wp-group-syntax">';
		echo '<li>'.sprintf(__('To show just one website thumbnail, use %s. The number is the ID of the website, which can be found on the WP Portfolio summary page.', 'wp-portfolio'), '<code><b>[wp-portfolio single="1"]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To show a specific selection of thumbnails, use their IDs like so: %s', 'wp-portfolio'), '<code><b>[wp-portfolio single="1,2"]</b></code>').'</li>';
	echo '</ul>';
	
	echo '<h3>'.__('Website Groups', 'wp-portfolio').'</h3>';	
	echo '<ul class="wp-group-syntax">';
		echo '<li>'.sprintf(__('To show all groups, use %s', 'wp-portfolio'), '<code><b>[wp-portfolio]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To show just the group with an ID of 1, use %s', 'wp-portfolio'), '<code><b>[wp-portfolio groups="1"]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To show groups with IDs of 1, 2 and 4, use %s', 'wp-portfolio'), '<code><b>[wp-portfolio groups="1,2,4"]</b></code>').'</li>';
	echo '</ul>';
	
	echo '<h3>'.__('Paging (Showing a portfolio on several pages)', 'wp-portfolio').'</h3>';	
	echo '<ul class="wp-group-syntax">';
		echo '<li>'.sprintf(__('To show all websites without any paging, just use %s as normal', 'wp-portfolio'), '<code><b>[wp-portfolio]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To show 3 websites per page, use %s', 'wp-portfolio'), '<code><b>[wp-portfolio sitesperpage="3"]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To show 5 websites per page, use %s', 'wp-portfolio'), '<code><b>[wp-portfolio sitesperpage="5"]</b></code>').'</li>';
	echo '</ul>';
	
	echo '<h3>'.__('Ordering By Date', 'wp-portfolio').'</h3>';
	echo '<ul class="wp-group-syntax">';
		echo '<li>'.sprintf(__('To order websites by the date they were added, showing newest first (so descending order) use %s. Group names are automatically hidden when ordering by date.'), '<code><b>[wp-portfolio ordertype="dateadded" orderby="desc"]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To order websites by the date they were added, showing oldest first (so ascending order) use %s. Group names are automatically hidden when ordering by date.'), '<code><b>[wp-portfolio ordertype="dateadded" orderby="asc"]</b></code>').'</li>';
	echo '</ul>';
	
	echo '<h3>'.__('Miscellaneous Options').'</h3>';
	echo '<ul class="wp-group-syntax">';
		echo '<li>'.sprintf(__('To hide the title/description of all groups shown in a portfolio for just a single post/page without affecting other posts/pages, just use %s', 'wp-portfolio'), '<code><b>[wp-portfolio hidegroupinfo="1"]</b></code>').'</li>';
		echo '<li>'.sprintf(__('To show the portfolio in reverse order, just use %s (The <code>desc=</code> is short for descending order)'), '<code><b>[wp-portfolio orderby="desc"]</b></code>').'</li>';
	echo '</ul>';	
	
	
	echo '<h2>'.__('Uninstalling WP Portfolio').'</h2>';
	echo '<p>'.sprintf(__('If you\'re going to permanently uninstall WP Portfolio, you can also <a href="%s">remove all settings and data</a>.', 'wp-portfolio'), 'admin.php?page=WPP_show_settings&uninstall=yes').'</p>';
							
	echo '<a name="doc-layout"></a>';
	echo'<h2>'.__('Portfolio Layout Templates').'</h2>';	
	
	echo '<p>'.__('The default templates for the groups and websites below as a reference.').'</p>';
	echo '<ul style="margin-left: 30px; list-style-type: disc;">';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the group name.', 'wp-portfolio'), WPP_STR_GROUP_NAME).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the group description.', 'wp-portfolio'), WPP_STR_GROUP_DESCRIPTION).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the website name.', 'wp-portfolio'), WPP_STR_WEBSITE_NAME).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the website url.', 'wp-portfolio'), WPP_STR_WEBSITE_URL).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the website description.', 'wp-portfolio'), WPP_STR_WEBSITE_DESCRIPTION).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the website thumbnail including the &lt;img&gt; tag.'), WPP_STR_WEBSITE_THUMBNAIL).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the website thumbnail URL (no HTML).', 'wp-portfolio'), WPP_STR_WEBSITE_THUMBNAIL_URL).'</li>';
		echo '<li>'.sprintf('<strong>%s</strong> - '.__('Replace with the custom field data.', 'wp-portfolio'), WPP_STR_WEBSITE_CUSTOM_FIELD).'</li>';
	echo '</ul>';
	?>
	
	<form>
	<table class="form-table">
		<tr class="form-field">
			<th scope="row"><label for="default_template_group"><?php _e('Group Template', 'wp-portfolio'); ?></label></th>
			<td>
				<textarea name="default_template_group" rows="3"><?php echo htmlentities(WPP_DEFAULT_GROUP_TEMPLATE); ?></textarea>
			</td>
		</tr>		
		<tr class="form-field">
			<th scope="row"><label for="default_template_website"><?php  _e('Website Template', 'wp-portfolio'); ?></label></th>
			<td>
				<textarea name="default_template_website" rows="8"><?php echo htmlentities(WPP_DEFAULT_WEBSITE_TEMPLATE); ?></textarea>
			</td>
		</tr>			
		<tr class="form-field">
			<th scope="row"><label for="default_template_css"><?php _e('Template CSS', 'wp-portfolio'); ?></label></th>
			<td>
				<textarea name="default_template_css" rows="8"><?php echo htmlentities(WPP_DEFAULT_CSS); ?></textarea>
			</td>
		</tr>					
		<tr class="form-field">
			<th scope="row"><label for="default_template_css_widget"><?php _e('Widget CSS', 'wp-portfolio'); ?></label></th>
			<td>
				<textarea name="default_template_css_widget" rows="8"><?php echo htmlentities(WPP_DEFAULT_CSS_WIDGET); ?></textarea>
			</td>
		</tr>		
	</table>
	</form>
	<p>&nbsp;</p>
	
	
	<a id="doc-paging"></a>
	<h2><?php _e('Portfolio Paging Templates', 'wp-portfolio'); ?></h2>	
	
	<?php
	echo '<p>'.__('The default templates specifically for the paging of websites (when there are more websites that you want to fit on a single page).', 'wp-portfolio').'</p>';
	echo '<ul style="margin-left: 30px; list-style-type: disc;">';
		echo '<li><strong>%PAGING_PAGE_CURRENT%</strong> - ' . __('Replace with the current page number.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%PAGING_PAGE_TOTAL%</strong> - ' . __('Replace with the total number of pages.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%PAGING_ITEM_START%</strong> - ' . __('Replace with the start of the range of websites/thumbnails being shown on a particular page.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%PAGING_ITEM_END%</strong> - ' . __('Replace with the end of the range of websites/thumbnails being shown on a particular page.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%PAGING_ITEM_TOTAL%</strong> - ' . __('Replace with the total number of websites/thumbnails in the portfolio.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%LINK_PREVIOUS%</strong> - ' . __('Replace with the link to the previous page.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%LINK_NEXT%</strong> - ' . __('Replace with the link to the next page.', 'wp-portfolio') . '</li>';
		echo '<li><strong>%PAGE_NUMBERS%</strong> - ' . __('Replace with the list of pages, with each number being a link.', 'wp-portfolio') . '</li>';
	echo '</ul>';
	?>
	
	<form>
	<table class="form-table">
		<tr class="form-field">
			<th scope="row"><label for="default_template_paging"><?php _e('Paging Template', 'wp-portfolio'); ?></label></th>
			<td>
				<textarea name="default_template_group" rows="3"><?php echo htmlentities(WPP_DEFAULT_PAGING_TEMPLATE); ?></textarea>
			</td>
		</tr>		
		<tr class="form-field">
			<th scope="row"><label for="default_template_css_paging"><?php _e('Paging CSS', 'wp-portfolio'); ?></label></th>
			<td>
				<textarea name="default_template_css_paging" rows="8"><?php echo htmlentities(WPP_DEFAULT_CSS_PAGING); ?></textarea>
			</td>
		</tr>		
	</table>
	</form>
	<p>&nbsp;</p>
		
	<h2><?php _e('Showing the Portfolio from PHP', 'wp-portfolio'); ?></h2>
	<h3>WPPortfolio_getAllPortfolioAsHTML()</h3>
	<p><?php _e(sprintf('You can show all or a part of the portfolio from within code by using the %s function.', '<code>WPPortfolio_getAllPortfolioAsHTML($groups, $template_website, $template_group, $sitesperpage, $showAscending, $orderBy)</code>'), 'wp-portfolio' ); ?></p>
	
	<p><b><?php _e('Parameters', 'wp-portfolio'); ?></b></p>
	<ul class="wp-group-syntax">
	<?php 
		echo '<li><b>$groups</b> - '.				sprintf(__('The comma separated list of groups to include. To show all groups, specify %1$s for %2$s. (<b>default</b> is %1$s)', 'wp-portfolio'), '<code>false</code>', '<code>$groups</code>').'</li>';
		echo '<li><b>$template_website</b> - ' . 	sprintf(__('The HTML template to use for rendering a single website (using the <a href="%1$s#doc-layout">template tags above</a>). Specify %2$s to use the website template stored in the settings. (<b>default</b> is %2$s, i.e. use template stored in settings.)', 'wp-portfolio'), WPP_DOCUMENTATION, '<code>false</code>').'</li>';
		echo '<li><b>$template_group</b> - ' . 		sprintf(__('The HTML template to use for rendering a group description (using the <a href="%1$s#doc-layout">template tags above</a>). Specify %2$s to use the group template stored in the settings. To hide the group description, specify a single space character for %3$s. (<b>default</b> is %2$s, i.e. use template stored in settings.)', 'wp-portfolio'), WPP_DOCUMENTATION, '<code>false</code>', '<code>$template_group</code>').'</li>';
		echo '<li><b>$sitesperpage</b> - ' . 		sprintf(__('The number of websites to show per page, set to %1$s or %2$s if you don\'t want to use paging.  (<b>default</b> is %1$s, i.e. don\'t do any paging.)', 'wp-portfolio'), '<code>false</code>', '<code>0</code>').'</li>';
		echo '<li><b>$showAscending</b> - ' . 		sprintf(__('If %1$s, show the websites in ascending order. If %2$s, show the websites in reverse order. (<b>default</b> is %1$s, i.e. ascending ordering.)', 'wp-portfolio'), '<code>true</code>', '<code>false</code>').'</li>';
		echo '<li><b>$orderBy</b> - ' . 			sprintf(__('Determine how to order the websites. (<b>default</b> is %s, i.e. normal ordering.)', 'wp-portfolio'), '<code>\'normal\'</code>');
		echo '<ul>';
			echo '<li>' . 								sprintf(__('If %s, show the websites in normal group order.', 'wp-portfolio'), '<code>\'normal\'</code>').'</li>';
			echo '<li>' . 								sprintf(__('If %s, show the websites ordered by date. If this mode is chosen, group names are automatically hidden.', 'wp-portfolio'), '<code>\'dateadded\'</code>').'</li>';
		echo '</ul>';
		echo '</li>';
		?>
	</ul>	
	
	<p>&nbsp;</p>	
	
	<p><b><?php _e('Example 1 (using website template stored in settings)', 'wp-portfolio'); ?>:</b></p> 	
	<pre>
&lt;?php 
if (function_exists('WPPortfolio_getAllPortfolioAsHTML')) {
	echo WPPortfolio_getAllPortfolioAsHTML('1,3');
}
?&gt;
	</pre>
	
	<p><b><?php _e('Example 2 (with custom templates)', 'wp-portfolio'); ?>:</b></p>
	<pre>
&lt;?php 
if (function_exists('WPPortfolio_getAllPortfolioAsHTML'))
{
	$website_template = '
		&lt;div class=&quot;portfolio-website&quot;&gt;
		&lt;div class=&quot;website-thumbnail&quot;&gt;&lt;a href=&quot;%WEBSITE_URL%&quot; target=&quot;_blank&quot;&gt;%WEBSITE_THUMBNAIL%&lt;/a&gt;&lt;/div&gt;
		&lt;div class=&quot;website-name&quot;&gt;&lt;a href=&quot;%WEBSITE_URL%&quot; target=&quot;_blank&quot;&gt;%WEBSITE_NAME%&lt;/a&gt;&lt;/div&gt;
		&lt;div class=&quot;website-description&quot;&gt;%WEBSITE_DESCRIPTION%&lt;/div&gt;
		&lt;div class=&quot;website-clear&quot;&gt;&lt;/div&gt;
		&lt;/div&gt;';
		
	$group_template = '
		&lt;h2&gt;%GROUP_NAME%&lt;/h2&gt;
		&lt;p&gt;%GROUP_DESCRIPTION%&lt;/p&gt;';	
	
	echo WPPortfolio_getAllPortfolioAsHTML('1,2', $website_template, $group_template);
}
?&gt;
	</pre>		
	
	<p><b><?php _e('Example 3 (using stored templates, but showing 3 websites per page)', 'wp-portfolio'); ?>:</b></p> 	
	<pre>
&lt;?php 
if (function_exists('WPPortfolio_getAllPortfolioAsHTML')) {
	echo WPPortfolio_getAllPortfolioAsHTML('1,3', false, false, '3');
}
?&gt;
	</pre>	
	
	<p><b><?php _e('Example 4 (using stored templates, but showing 4 websites per page, ordering by date, with the newest website first)', 'wp-portfolio'); ?>:</b></p> 	
	<pre>
&lt;?php 
if (function_exists('WPPortfolio_getAllPortfolioAsHTML')) {
	echo WPPortfolio_getAllPortfolioAsHTML('1,3', false, false, '3', false, 'dateadded');
}
?&gt;
	</pre>	
			
		
	<p>&nbsp;</p>		
	
	<h3>WPPortfolio_getRandomPortfolioSelectionAsHTML()</h3>
	<p><?php echo sprintf(__('You can show a random selection of your portfolio from within code by using the %s function. Please note that there is no group information shown when this function is used.', 'wp-portfolio'), '<code>WPPortfolio_getRandomPortfolioSelectionAsHTML($groups, $count, $template_website)</code>'); ?></p>
	
	<p><b><?php echo _e('Parameters', 'wp-portfolio'); ?></b></p>
	<ul class="wp-group-syntax">
		<li><b>$groups</b> - <?php echo sprintf(__('The comma separated list of groups to make a random selection from. To choose from all groups, specify %1$s for %2$s (<b>default</b> is %1$s).', 'wp-portfolio'), '<code>false</code>', '<code>$groups</code>'); ?></li>
		<li><b>$count</b> - <?php echo sprintf(__('The number of websites to show in the random selection. (<b>default</b> is %s)'), '<code>3</code>'); ?></li>
		<li><b>$template_website</b> - <?php echo sprintf(__('The HTML template to use for rendering a single website (using the <a href="%1$s#doc-layout">template tags above</a>). Specify %2$s to use the website template stored in the settings. (<b>default</b> is %2$s, i.e. use template stored in settings.)', 'wp-portfolio'), WPP_DOCUMENTATION, '<code>false</code>'); ?></li>
	</ul>
	
	<p>&nbsp;</p>	
	
	<p><b><?php _e('Example 1 (using website template stored in settings)', 'wp-portfolio'); ?>:</b></p>
	<pre>
&lt;?php 
if (function_exists('WPPortfolio_getRandomPortfolioSelectionAsHTML')) {
	echo WPPortfolio_getRandomPortfolioSelectionAsHTML('1,4', 4);
}
?&gt;
	</pre>
	
	<p><b><?php _e('Example 2 (with custom templates)', 'wp-portfolio'); ?>:</b></p>
	<pre>
&lt;?php 
if (function_exists('WPPortfolio_getRandomPortfolioSelectionAsHTML')) {
	$website_template = '
		&lt;div class=&quot;portfolio-website&quot;&gt;
		&lt;div class=&quot;website-thumbnail&quot;&gt;&lt;a href=&quot;%WEBSITE_URL%&quot; target=&quot;_blank&quot;&gt;%WEBSITE_THUMBNAIL%&lt;/a&gt;&lt;/div&gt;
		&lt;div class=&quot;website-name&quot;&gt;&lt;a href=&quot;%WEBSITE_URL%&quot; target=&quot;_blank&quot;&gt;%WEBSITE_NAME%&lt;/a&gt;&lt;/div&gt;
		&lt;div class=&quot;website-clear&quot;&gt;&lt;/div&gt;
		&lt;/div&gt;';
	echo WPPortfolio_getRandomPortfolioSelectionAsHTML('1,4', 4, $website_template);
}
?&gt;
	</pre>
		

	<p>&nbsp;</p>	
	
	
	<p>&nbsp;</p>
</div>
	
	<?php
}

/**
 * Show only the settings relating to layout of the portfolio.
 */
function WPPortfolio_showLayoutSettingsPage() 
{
?>
	<div class="wrap">
	<div id="icon-themes" class="icon32">
	<br/>
	</div>
	<h2>WP Portfolio - Layout Settings</h2>
<?php 	

	// Get all the options from the database
	$settingsList = WPPortfolio_getSettingList(false, true);
	
	// Get all the options from the database for the form
	$settings = array();
	foreach ($settingsList as $settingName => $settingDefault) {
		$settings[$settingName] = stripslashes(get_option('WPPortfolio_'.$settingName));
	}	
		
	// If we don't have the version in the settings, we're not installed
	if (!get_option('WPPortfolio_version')) {
		WPPortfolio_showMessage(__('No WP Portfolio settings were found, so it appears that the plugin has been uninstalled. Please <b>deactivate</b> and then <b>activate</b> the WP Portfolio plugin again to fix this.', 'wp-portfolio'), true);
		return false;
	}
	
			
	// Check if updated data.
	if ( isset($_POST) && isset($_POST['update']) )
	{
		// Copy settings from $_POST
		$settings = array();
		foreach ($settingsList as $settingName => $settingDefault) 
		{
			$settings[$settingName] = stripslashes(trim(WPPortfolio_getArrayValue($_POST, $settingName)));			 			
		}		

		// Save settings
		foreach ($settingsList as $settingName => $settingDefault) {
			update_option('WPPortfolio_'.$settingName, $settings[$settingName]); 
		}
							
		WPPortfolio_showMessage();				
	}	
	
	
	$form = new FormBuilder();	
	
	$formElem = new FormElement("setting_template_website", __("Website HTML Template", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_website']);
	$formElem->description = '&bull; '.__('This is the template used to render each of the websites.', 'wp-portfolio').'<br/>'.
							sprintf('&bull; '.__('A complete list of tags is available in the <a href="%s#doc-layout">Portfolio Layout Templates</a> section in the documentation.', 'wp-portfolio'), WPP_DOCUMENTATION);
	$formElem->setTypeAsTextArea(8, 70); 
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("setting_template_group", __("Group HTML Template", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_group']);
	$formElem->description = '&bull; '.__('This is the template used to render each of the groups that the websites belong to.', 'wp-portfolio').'<br/>'.
							sprintf('&bull; '.__('A complete list of tags is available in the <a href="%s#doc-layout">Portfolio Layout Templates</a> section in the documentation.', 'wp-portfolio'), WPP_DOCUMENTATION);
	$formElem->setTypeAsTextArea(3, 70); 
	$form->addFormElement($formElem);	
	
	
	$form->addBreak('settings_paging', '<div class="settings-spacer">&nbsp;</div><h2>'.__('Portfolio Paging Settings', 'wp-portfolio') . '</h2>');
	$formElem = new FormElement("setting_template_paging", __("Paging HTML Template", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_paging']);
	$formElem->description = '&bull; '.__('This is the template used to render the paging for the thumbnails.', 'wp-portfolio').'<br/>'.
							sprintf('&bull; '.__('A complete list of tags is available in the <a href="%s#doc-paging">Portfolio Paging Templates</a> section in the documentation.', 'wp-portfolio'), WPP_DOCUMENTATION);
	$formElem->setTypeAsTextArea(3, 70); 
	$form->addFormElement($formElem);	
	
	$formElem = new FormElement("setting_template_paging_previous", __("Text for 'Previous' link", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_paging_previous']);
	$formElem->description = __('The text to use for the \'Previous\' page link used in the thumbnail paging.', 'wp-portfolio'); 
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("setting_template_paging_next", __("Text for 'Next' link", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_paging_next']);
	$formElem->description = __('The text to use for the \'Next\' page link used in the thumbnail paging.', 'wp-portfolio'); 
	$form->addFormElement($formElem);
	
	
	$form->addBreak('settings_css', '<div class="settings-spacer">&nbsp;</div><h2>' . __('Portfolio Stylesheet (CSS) Settings', 'wp-portfolio') . '</h2>');
	
	// Enable/Disable CSS mode
	$formElem = new FormElement("setting_disable_plugin_css", __("Disable Plugin CSS", 'wp-portfolio'));
	$formElem->value = $settings['setting_disable_plugin_css'];
	$formElem->setTypeAsCheckbox(__("If ticked, don't use the WP Portfolio CSS below.", 'wp-portfolio'));
	$formElem->description = '&bull; '.__('Allows you to switch off the default CSS so that you can use CSS in your template CSS file.', 'wp-portfolio').'<br/>'.
							sprintf('&bull; '.__('<strong>Advanced Tip:</strong> Once you\'re happy with the styles, you should really move all the CSS below into your template %s. This is so that visitor browsers can cache the stylesheet and reduce loading times. Any CSS placed here will be injected into the template &lt;head&gt; tag, which is not the most efficient method of delivering CSS.', 'wp-portfolio'), '<code>style.css</code>');
	$form->addFormElement($formElem);
	
	
	$formElem = new FormElement("setting_template_css", __("Template CSS", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_css']);
	$formElem->description = __('This is the CSS code used to style the portfolio.', 'wp-portfolio');
	$formElem->setTypeAsTextArea(10, 70); 
	$form->addFormElement($formElem);	

	$formElem = new FormElement("setting_template_css_paging", __("Paging CSS", 'wp-portfolio'));				
	$formElem->value = htmlentities($settings['setting_template_css_paging']);
	$formElem->description = __('This is the CSS code used to style the paging area if you are showing your portfolio on several pages.', 'wp-portfolio');
	$formElem->setTypeAsTextArea(6, 70); 
	$form->addFormElement($formElem);	
	
	$formElem = new FormElement("setting_template_css_widget", __("Widget CSS", 'wp-portfolio'));
	$formElem->value = htmlentities($settings['setting_template_css_widget']);
	$formElem->description = __('This is the CSS code used to style the websites in the widget area.', 'wp-portfolio');
	$formElem->setTypeAsTextArea(6, 70); 
	$form->addFormElement($formElem);
	
	
	echo $form->toString();
	
	?>	

</div>
<?php 
}

/**
 * Page that shows a list of websites in your portfolio.
 */
function WPPortfolio_show_websites()
{
?>
<div class="wrap">
	<div id="icon-themes" class="icon32">
	<br/>
	</div>
	<h2><?php _e('Summary of Websites in your Portfolio', 'wp-portfolio'); ?></h2>
	<br>
<?php 		

    // See if a group parameter was specified, if so, use that to show websites
    // in just that group
    $groupid = false;
    if (isset($_GET['groupid'])) {
    	$groupid = $_GET['groupid'] + 0;
    }
    
	$siteid = 0;
	if (isset($_GET['siteid'])) {
		$siteid = (is_numeric($_GET['siteid']) ? $_GET['siteid'] + 0 : 0);
	}	    

	global $wpdb;
	$websites_table = $wpdb->prefix . TABLE_WEBSITES;
	$groups_table   = $wpdb->prefix . TABLE_WEBSITE_GROUPS;

	
	// ### DELETE Check if we're deleting a website
	if ($siteid > 0 && isset($_GET['delete']))
	{
		$websitedetails = WPPortfolio_getWebsiteDetails($siteid);
		
		if (isset($_GET['confirm']))
		{
			$delete_website = "DELETE FROM $websites_table WHERE siteid = '".$wpdb->escape($siteid)."' LIMIT 1";
			if ($wpdb->query( $delete_website )) {
				WPPortfolio_showMessage(__("Website was successfully deleted.", 'wp-portfolio'));
			}
			else {
				WPPortfolio_showMessage(__("Sorry, but an unknown error occured whist trying to delete the selected website from the portfolio.", 'wp-portfolio'), true);
			}
		}
		else
		{
			$message = sprintf(__('Are you sure you want to delete "%1$s" from your portfolio?<br/><br/> <a href="%2$s">Yes, delete.</a> &nbsp; <a href="%3$s">NO!</a>', 'wp-portfolio'), $websitedetails['sitename'], WPP_WEBSITE_SUMMARY.'&delete=yes&confirm=yes&siteid='.$websitedetails['siteid'], WPP_WEBSITE_SUMMARY);
			WPPortfolio_showMessage($message);
			return;
		}
	}		
	
	// ### DUPLICATE Check - creating a copy of a website
	else if ($siteid > 0 && isset($_GET['duplicate']))
	{
		// Get website details and check they are valid
		$websitedetails = WPPortfolio_getWebsiteDetails($siteid);
		if ($websitedetails)
		{
			// Copy details we need for the update message
			$nameOriginal   = stripslashes($websitedetails['sitename']);
			$siteidOriginal = $websitedetails['siteid'];
			
			// Remove existing siteid (so we can insert a fresh copy)
			// Make it clear that the website was copied by changing the site title.
			unset($websitedetails['siteid']);
			$websitedetails['sitename'] = $nameOriginal . ' (Copy)';
			
			// Insert new copy:
			$SQL = arrayToSQLInsert($websites_table, $websitedetails);
			$wpdb->insert($websites_table, $websitedetails);
			$siteidNew = $wpdb->insert_id;
			
			// Create summary message with links to edit the websites.
			$editOriginal	= sprintf('<a href="'.WPP_MODIFY_WEBSITE.'&editmode=edit&siteid=%s" title="'.__('Edit', 'wp-portfolio').' \'%s\'">%s</a>', $siteidOriginal, $nameOriginal, $nameOriginal);
			$editNew   		= sprintf('<a href="'.WPP_MODIFY_WEBSITE.'&editmode=edit&siteid=%s" title="'.__('Edit', 'wp-portfolio').' \'%s\'">%s</a>', $siteidNew, $websitedetails['sitename'], $websitedetails['sitename']);
			
			$message = sprintf(__('The website \'%s\' was successfully copied to \'%s\'', 'wp-portfolio'),$editOriginal, $editNew);
			WPPortfolio_showMessage($message);
		}
	}
	

	// Determine if showing only 1 group
	$WHERE_CLAUSE = false;
	if ($groupid > 0) {
		$WHERE_CLAUSE = "WHERE $groups_table.groupid = '$groupid'";
	}
	
	// Default sort method
	$sorting = "grouporder, groupname, siteorder, sitename";
	
	// Work out how to sort
	if (isset($_GET['sortby'])) {
		$sortby = strtolower($_GET['sortby']);
		
		switch ($sortby) {
			case 'sitename':
				$sorting = "sitename ASC";
				break;
			case 'siteurl':
				$sorting = "siteurl ASC";
				break;			
			case 'siteadded':
				$sorting = "siteadded DESC, sitename ASC";
				break;
		}
	}		
	
	// Get website details, merge with group details
	$SQL = "SELECT *, UNIX_TIMESTAMP(siteadded) as dateadded FROM $websites_table
			LEFT JOIN $groups_table ON $websites_table.sitegroup = $groups_table.groupid
			$WHERE_CLAUSE
			ORDER BY $sorting	 		
	 		";	
		
	
	$wpdb->show_errors();
	$websites = $wpdb->get_results($SQL, OBJECT);	
			
	// Only show table if there are websites to show
	if ($websites)
	{
		$baseSortURL = WPP_WEBSITE_SUMMARY;
		if ($groupid > 0) {
			$baseSortURL .= "&groupid=".$groupid;
		}
		
		?>
		<div class="websitecount">
			<?php
				// If just showing 1 group
				if ($groupid > 0) {
					echo sprintf(__('Showing <strong>%s</strong> websites in the \'%s\' group (<a href="%s" class="showall">or Show All</a>). To only show the websites in this group, use %s', 'wp-portfolio'), $wpdb->num_rows, $websites[0]->groupname, WPP_WEBSITE_SUMMARY, '<code>[wp-portfolio groups="'.$groupid.'"]</code>');
				} else {
					echo sprintf(__('Showing <strong>%s</strong> websites in the portfolio.', 'wp-portfolio'), $wpdb->num_rows);
				}							
			?>
			
		
		</div>
		
		<div class="subsubsub">
			<strong><?php _e('Sort by:', 'wp-portfolio'); ?></strong>
			<?php echo sprintf(__('<a href="%s" title="Sort websites in the order you\'ll see them within your portfolio.">Normal Ordering</a>', 'wp-portfolio'), $baseSortURL); ?>
			|
			<?php echo sprintf(__('<a href="%s" title="Sort the websites by name.">Name</a>', 'wp-portfolio'), $baseSortURL.'&sortby=sitename'); ?>
			|
			<?php echo sprintf(__('<a href="%s" title="Sort the websites by URL.">URL</a>', 'wp-portfolio'), $baseSortURL.'&sortby=siteurl'); ?>
			|
			<?php echo sprintf(__('<a href="%s" title="Sort the websites by the date that the websites were added.">Date Added</a>', 'wp-portfolio'), $baseSortURL.'&sortby=siteadded'); ?>
		</div>
		<br/>
		<?php 
		
		$table = new TableBuilder();
		$table->attributes = array("id" => "wpptable");

		$column = new TableColumn(__("ID", 'wp-portfolio'), "id");
		$column->cellClass = "wpp-id";
		$table->addColumn($column);
		
		$column = new TableColumn(__("Thumbnail", 'wp-portfolio'), "thumbnail");
		$column->cellClass = "wpp-thumbnail";
		$table->addColumn($column);
		
		$column = new TableColumn(__("Site Name", 'wp-portfolio'), "sitename");
		$column->cellClass = "wpp-name";
		$table->addColumn($column);
		
		$column = new TableColumn(__("URL", 'wp-portfolio'), "siteurl");
		$column->cellClass = "wpp-url";
		$table->addColumn($column);
		
		$column = new TableColumn(__("Date Added", 'wp-portfolio'), "dateadded");
		$column->cellClass = "wpp-date-added";
		$table->addColumn($column);

		$column = new TableColumn(__("Custom Info", 'wp-portfolio'), "custominfo");
		$column->cellClass = "wpp-customurl";
		$table->addColumn($column);						
		
		$column = new TableColumn(__("Visible?", 'wp-portfolio'), "siteactive");
		$column->cellClass = "wpp-small";
		$table->addColumn($column);						
		
		$column = new TableColumn(__("Link Displayed?", 'wp-portfolio'), "displaylink");
		$column->cellClass = "wpp-small";
		$table->addColumn($column);

		$column = new TableColumn(__("Ordering", 'wp-portfolio'), "siteorder");
		$column->cellClass = "wpp-small";
		$table->addColumn($column);
		
		$column = new TableColumn(__("Group", 'wp-portfolio'), "group");
		$column->cellClass = "wpp-small";
		$table->addColumn($column);
					
		$column = new TableColumn(__("Action", 'wp-portfolio'), "action");
		$column->cellClass = "wpp-small wpp-action-links";
		$column->headerClass = "wpp-action-links";		
		$table->addColumn($column);							
			
		// Got a paid account?
		$paidAccount = WPPortfolio_isPaidAccount();
			
		
		foreach ($websites as $websitedetails)
		{
			// First part of a link to visit a website
			$websiteClickable = '<a href="'.$websitedetails->siteurl.'" target="_new" title="'.__('Visit the website', 'wp-portfolio').' \''.stripslashes($websitedetails->sitename).'\'">';
			$editClickable    = '<a href="'.WPP_MODIFY_WEBSITE.'&editmode=edit&siteid='.$websitedetails->siteid.'" title="'.__('Edit', 'wp-portfolio').' \''.stripslashes($websitedetails->sitename).'\'" class="wpp-edit">';
			
			$rowdata = array();
			$rowdata['id'] 			= $websitedetails->siteid;			
			$rowdata['dateadded']	= date('D jS M Y \a\t H:i', $websitedetails->dateadded);
			
			$rowdata['sitename'] 	= stripslashes($websitedetails->sitename);			
			$rowdata['siteurl'] 	= $websiteClickable.$websitedetails->siteurl.'</a>';			
			
			// Custom URL will typically not be specified, so show n/a for clarity.
			if ($websitedetails->customthumb)
			{
				// Use custom thumbnail rather than screenshot
				$rowdata['thumbnail'] 	= '<img src="'.WPPortfolio_getAdjustedCustomThumbnail($websitedetails->customthumb, "sm").'" />';
				
				$customThumb = '<a href="'.$websitedetails->customthumb.'" target="_new" title="'.__('Open custom thumbnail in a new window', 'wp-portfolio').'">'.__('View Image', 'wp-portfolio').'</a>';
			} 
			// Not using custom thumbnail
			else 
			{
				$rowdata['thumbnail'] 	= WPPortfolio_getThumbnailHTML($websitedetails->siteurl, "sm", ($websitedetails->specificpage == 1)); 
				$customThumb = false;
			}
			
			// Custom Info column - only show custom info if it exists.
			$rowdata['custominfo'] = false;			
			
			if ($customThumb) {
				$rowdata['custominfo']		= sprintf('<span class="wpp-custom-thumb"><b>'.__('Custom Thumb', 'wp-portfolio').':</b><br/>%s</span>', $customThumb);
			}
			
			if ($websitedetails->customfield) {
				$rowdata['custominfo']		.= sprintf('<span class="wpp-custom-field"><b>'.__('Custom Field', 'wp-portfolio').':</b><br/>%s</span>', $websitedetails->customfield);
			}

			// Ensure there's just a dash if there's no custom information.
			if ($rowdata['custominfo'] == false) {
				$rowdata['custominfo'] = '-';
			}
			
			
			$rowdata['siteorder']   = $websitedetails->siteorder; 
			$rowdata['siteactive']  = ($websitedetails->siteactive ? __('Yes', 'wp-portfolio') : '<b>'.__('No', 'wp-portfolio').'</b>'); 
			$rowdata['displaylink']  = ($websitedetails->displaylink ? __('Yes', 'wp-portfolio') : '<b>'.__('No', 'wp-portfolio').'</b>'); 
			$rowdata['group'] 		= sprintf('<a href="'.WPP_WEBSITE_SUMMARY.'&groupid='.$websitedetails->groupid.'" title="'.__('Show websites only in the \'%s\' group', 'wp-portfolio').'">'.stripslashes($websitedetails->groupname).'</a>', stripslashes($websitedetails->groupname));
			
			
			// Refresh link			 
			$refreshAction = '&bull; <a href="'.WPP_WEBSITE_SUMMARY.'&refresh=yes&siteid='.$websitedetails->siteid.'" class="wpp-refresh" title="'.__('Force a refresh of the thumbnail', 'wp-portfolio').'">'.__('Refresh', 'wp-portfolio').'</a>';			
			
			// The various actions - Delete | Duplicate | Edit
			$rowdata['action'] 		= $refreshAction . '<br/>' .
									  '&bull; '.$editClickable.__('Edit', 'wp-portfolio').'</a><br/>' . 
									  '&bull; <a href="'.WPP_WEBSITE_SUMMARY.'&duplicate=yes&siteid='.$websitedetails->siteid.'" title="'.__('Duplicate this website', 'wp-portfolio').'">'.__('Duplicate', 'wp-portfolio').'</a><br/>' .
									  '&bull; <a href="'.WPP_WEBSITE_SUMMARY.'&delete=yes&siteid='.$websitedetails->siteid.'" title="'.__('Delete this website...', 'wp-portfolio').'">'.__('Delete', 'wp-portfolio').'</a><br/>' 
									  ; 
			;
		
			$table->addRow($rowdata, ($websitedetails->siteactive ? 'site-active' : 'site-inactive'));
		}
		
		// Finally show table
		echo $table->toString();
		
		// Add AJAX loader URL to page, so that it's easier to use the loader image.
		printf('<div id="wpp-loader">%simgs/ajax-loader.gif</div>', WPPortfolio_getPluginPath());
		
		echo "<br/>";
		
	} // end of if websites
	else {
		WPPortfolio_showMessage(__("There are currently no websites in the portfolio.", 'wp-portfolio'), true);
	}
	
	?>	
</div>
<?php 
	
}

/**
 * Show the error logging summary page.
 */
function WPPortfolio_showErrorPage() 
{
	global $wpdb;
	$wpdb->show_errors();
	$table_debug = $wpdb->prefix . TABLE_WEBSITE_DEBUG;	

	
	// Check for clear of logs
	if (isset($_POST['wpp-clear-logs']))
	{
		$SQL = "TRUNCATE $table_debug";
		$wpdb->query($SQL);
		
		WPPortfolio_showMessage(__('Debug logs have successfully been emptied.', 'wp-portfolio'));
	}
	
	
	?>
	<div class="wrap">
	<div id="icon-tools" class="icon32">
	<br/>
	</div>
	<h2>Error Log</h2>
		
		<form class="wpp-button-right" method="post" action="<?= str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="submit" name="wpp-refresh-logs" value="<?php _e('Refresh Logs', 'wp-portfolio'); ?>" class="button-primary" />
			<input type="submit" name="wpp-clear-logs" value="<?php _e('Clear Logs', 'wp-portfolio'); ?>" class="button-secondary" />
			<div class="wpp-clear"></div>
		</form>
	<br/>
	
	<?php 
		
		$SQL = "SELECT *, UNIX_TIMESTAMP(request_date) AS request_date_ts
				FROM $table_debug
				ORDER BY request_date DESC
				LIMIT 50
				";
		
		$wpdb->show_errors();
		$logMsgs = $wpdb->get_results($SQL, OBJECT);

		if ($logMsgs)
		{
			printf('<div id="wpp_error_count">'.__('Showing a total of <b>%d</b> log messages.</div>', 'wp-portfolio'), $wpdb->num_rows);
			
			echo '<p>'.__('All errors are <b>cached for 12 hours</b> so that your thumbnail allowance with STW does not get used up if you have persistent errors.', 'wp-portfolio').'<br>';
			echo __('If you\'ve <b>had errors</b>, and you\'ve <b>now fixed them</b>, you can click on the \'<b>Clear Logs</b>\' button on the right to <b>flush the error cache</b> and re-attempt to fetch a thumbnail.', 'wp-portfolio').'</p>';
			
			$table = new TableBuilder();
			$table->attributes = array("id" => "wpptable_error_log");
	
			$column = new TableColumn(__("ID", 'wp-portfolio'), "id");
			$column->cellClass = "wpp-id";
			$table->addColumn($column);
			
			$column = new TableColumn(__("Result", 'wp-portfolio'), "request_result");
			$column->cellClass = "wpp-result";
			$table->addColumn($column);			
			
			$column = new TableColumn(__("Requested URL", 'wp-portfolio'), "request_url");
			$column->cellClass = "wpp-url";
			$table->addColumn($column);
			
			$column = new TableColumn(__("Type", 'wp-portfolio'), "request_type");
			$column->cellClass = "wpp-type";
			$table->addColumn($column);
			
			$column = new TableColumn(__("Request Date", 'wp-portfolio'), "request_date");
			$column->cellClass = "wpp-request-date";
			$table->addColumn($column);
			
			$column = new TableColumn(__("Detail", 'wp-portfolio'), "request_detail");
			$column->cellClass = "wpp-detail";
			$table->addColumn($column);

			
			foreach ($logMsgs as $logDetail)
			{
				$rowdata = array();
				$rowdata['id'] 				= $logDetail->logid;
				$rowdata['request_url'] 	= $logDetail->request_url;
				$rowdata['request_type'] 	= $logDetail->request_type;
				$rowdata['request_result'] 	= '<span>'.($logDetail->request_result == 1 ? __('Success', 'wp-portfolio') : __('Error', 'wp-portfolio')).'</span>';
				$rowdata['request_date'] 	= $logDetail->request_date . '<br/>' . 'about '. human_time_diff($logDetail->request_date_ts) . ' ago';
				$rowdata['request_detail'] 	= $logDetail->request_detail;
				
				$table->addRow($rowdata, ($logDetail->request_result == 1 ? 'wpp_success' : 'wpp_error'));
			}
			
			// Finally show table
			echo $table->toString();
			echo "<br/>";
		}
		else {
			printf('<div class="wpp_clear"></div>');
			WPPortfolio_showMessage(__("There are currently no debug logs to show.", 'wp-portfolio'), true);
		}
	
	?>
	
	</div><!-- end wrapper -->	
	<?php 
}



/**
 * Shows the page listing the available groups.
 */
function WPPortfolio_show_website_groups()
{
?>
<div class="wrap">
	<div id="icon-edit" class="icon32">
	<br/>
	</div>
	<h2><?php _e('Website Groups', 'wp-portfolio'); ?></h2>
	<br/>

	<?php 
	global $wpdb;
	$groups_table = $wpdb->prefix . TABLE_WEBSITE_GROUPS;
	$websites_table = $wpdb->prefix . TABLE_WEBSITES;
	
    // Get group ID
    $groupid = false;
    if (isset($_GET['groupid'])) {
    	$groupid = $_GET['groupid'] + 0;
    }	
	
	// ### DELETE ### Check if we're deleting a group
	if ($groupid > 0 && isset($_GET['delete'])) 
	{				
		// Now check that ID actually relates to a real group
		$groupdetails = WPPortfolio_getGroupDetails($groupid);
		
		// If group doesn't really exist, then stop.
		if (count($groupdetails) == 0) {
			WPPortfolio_showMessage(sprintf(__('Sorry, but no group with that ID could be found. Please click <a href="%s">here</a> to return to the list of groups.', 'wp-portfolio'), WPP_GROUP_SUMMARY), true);
			return;
		}
		
		// Count the number of websites in this group and how many groups exist
		$website_count = $wpdb->get_var("SELECT COUNT(*) FROM $websites_table WHERE sitegroup = '".$wpdb->escape($groupdetails['groupid'])."'");
		$group_count   = $wpdb->get_var("SELECT COUNT(*) FROM $groups_table");
		
		$groupname = stripcslashes($groupdetails['groupname']);
		
		// Check that group doesn't have a load of websites assigned to it.
		if ($website_count > 0)  {
			WPPortfolio_showMessage(sprintf(__("Sorry, the group '%s' still contains <b>$website_count</b> websites. Please ensure the group is empty before deleting it.", 'wp-portfolio'), $groupname) );
			return;
		}
		
		// If we're deleting the last group, don't let it happen
		if ($group_count == 1)  {
			WPPortfolio_showMessage(sprintf(__("Sorry, but there needs to be at least 1 group in the portfolio. Please add a new group before deleting %s", 'wp-portfolio'), $groupname) );
			return;
		}
		
		// OK, got this far, confirm we want to delete.
		if (isset($_GET['confirm']))
		{
			$delete_group = "DELETE FROM $groups_table WHERE groupid = '".$wpdb->escape($groupid)."' LIMIT 1";
			if ($wpdb->query( $delete_group )) {
				WPPortfolio_showMessage(__("Group was successfully deleted.", 'wp-portfolio'));
			}
			else {
				WPPortfolio_showMessage(__("Sorry, but an unknown error occured whist trying to delete the selected group from the portfolio.", 'wp-portfolio'), true);
			}
		}
		else
		{
			$message = sprintf(__('Are you sure you want to delete the group \'%1$s\' from your portfolio?<br/><br/> <a href="%2$s">Yes, delete.</a> &nbsp; <a href="%3$s">NO!</a>', 'wp-portfolio'), $groupname, WPP_GROUP_SUMMARY.'&delete=yes&confirm=yes&groupid='.$groupid, WPP_GROUP_SUMMARY);
			WPPortfolio_showMessage($message);
			return;
		}
	}	
	
	
	
	// Get website details, merge with group details
	$SQL = "SELECT * FROM $groups_table
	 		ORDER BY grouporder, groupname";	
	
	// DEBUG Uncomment if needed
	// $wpdb->show_errors();
	$groups = $wpdb->get_results($SQL, OBJECT);
		
	
	// Only show table if there are any results.
	if ($groups)
	{					
		$table = new TableBuilder();
		$table->attributes = array("id" => "wpptable");

		$column = new TableColumn(__("ID", 'wp-portfolio'), "id");
		$column->cellClass = "wpp-id";
		$table->addColumn($column);		
		
		$column = new TableColumn(__("Name", 'wp-portfolio'), "name");
		$column->cellClass = "wpp-name";
		$table->addColumn($column);	

		$column = new TableColumn(__("Description", 'wp-portfolio'), "description");
		$table->addColumn($column);	

		$column = new TableColumn(__("# Websites", 'wp-portfolio'), "websitecount");
		$column->cellClass = "wpp-small wpp-center";
		$table->addColumn($column);			
		
		$column = new TableColumn(__("Ordering", 'wp-portfolio'), "ordering");
		$column->cellClass = "wpp-small wpp-center";
		$table->addColumn($column);		
		
		$column = new TableColumn(__("Action", 'wp-portfolio'), "action");
		$column->cellClass = "wpp-small action-links";
		$column->headerClass = "action-links";
		$table->addColumn($column);		
		
		echo '<p>'.__('The websites will be rendered in groups in the order shown in the table.', 'wp-portfolio').'</p>';
		
		foreach ($groups as $groupdetails) 
		{
			$groupClickable = sprintf('<a href="'.WPP_WEBSITE_SUMMARY.'&groupid='.$groupdetails->groupid.'" title="'.__('Show websites only in the \'%s\' group">', 'wp-portfolio'), $groupdetails->groupname);
			
			// Count websites in this group
			$website_count = $wpdb->get_var("SELECT COUNT(*) FROM $websites_table WHERE sitegroup = '".$wpdb->escape($groupdetails->groupid)."'");
			
			$rowdata = array();
			
			$rowdata['id']			 	= $groupdetails->groupid;
			$rowdata['name']		 	= $groupClickable.stripslashes($groupdetails->groupname).'</a>';
			$rowdata['description']	 	= stripslashes($groupdetails->groupdescription);
			$rowdata['websitecount'] 	= $groupClickable.$website_count.($website_count == 1 ? ' website' : ' websites')."</a>";
			$rowdata['ordering']	 	= $groupdetails->grouporder;
			$rowdata['action']		 	= '<a href="'.WPP_GROUP_SUMMARY.'&delete=yes&groupid='.$groupdetails->groupid.'">'.__('Delete', 'wp-portfolio').'</a>&nbsp;|&nbsp;' .
										  '<a href="'.WPP_MODIFY_GROUP.'&editmode=edit&groupid='.$groupdetails->groupid.'">'.__('Edit', 'wp-portfolio').'</a></td>';
			
			$table->addRow($rowdata);
		}
		
		
		// Finally show table
		echo $table->toString();
		echo "<br/>";		
		
	} // end of if groups
	
	// No groups to show
	else {
		WPPortfolio_showMessage(__("There are currently no groups in the portfolio.", 'wp-portfolio'), true);
	}
	?>
</div>
<?php 
	
}


/**
 * Shows the page that allows the details of a website to be modified or added to the portfolio.
 */
function WPPortfolio_modify_website()
{
	// Determine if we're in edit mode. Ensure we get correct mode regardless of where it is.
	$editmode = false;
	if (isset($_POST['editmode'])) {
		$editmode = ($_POST['editmode'] == 'edit');
	} else if (isset($_GET['editmode'])) {
		$editmode = ($_GET['editmode'] == 'edit');
	}	
	
	// Get the site ID. Ensure we get ID regardless of where it is.
	$siteid = 0;
	if (isset($_POST['website_siteid'])) {
		$siteid = (is_numeric($_POST['website_siteid']) ? $_POST['website_siteid'] + 0 : 0);
	} else if (isset($_GET['siteid'])) {
		$siteid = (is_numeric($_GET['siteid']) ? $_GET['siteid'] + 0 : 0);
	}	
	
	// Work out page heading
	$verb = __("Add New", 'wp-portfolio');
	if ($editmode) { 
		$verb = __("Modify", 'wp-portfolio');
	}
	
	?>
	<div class="wrap">
	<div id="icon-themes" class="icon32">
	<br/>
	</div>
	<h2><?php echo $verb.' '.__('Website Details', 'wp-portfolio'); ?></h2>	
	<?php 	
		
	
	// Check id is a valid number if editing $editmode
	if ($editmode && $siteid == 0) {
		WPPortfolio_showMessage(sprintf(__('Sorry, but no website with that ID could be found. Please click <a href="%s">here</a> to return to the list of websites.', 'wp-portfolio'), WPP_WEBSITE_SUMMARY), true);
		return;
	}	
	

	// If we're editing, try to get the website details.
	if ($editmode && $siteid > 0)
	{
		// Get details from the database
		$websitedetails = WPPortfolio_getWebsiteDetails($siteid);

		// False alarm, couldn't find it.
		if (count($websitedetails) == 0) {
			$editmode = false;
		}		
	} // end of editing check
	
	// Add Mode, so specify defaults
	else {
		$websitedetails['siteactive'] = 1;
		$websitedetails['displaylink'] = 1;
	}
	
	
	// Check if website is being added, if so, add to the database.
	if ( isset($_POST) && isset($_POST['update']) )
	{
		// Grab specified details
		$data = array();
		$data['siteid'] 			= $_POST['website_siteid'];
		$data['sitename'] 			= trim(strip_tags($_POST['website_sitename']));
		$data['siteurl'] 			= trim(strip_tags($_POST['website_siteurl']));
		$data['sitedescription'] 	= $_POST['website_sitedescription'];
		$data['sitegroup'] 			= $_POST['website_sitegroup'];
		$data['customthumb']		= trim(strip_tags($_POST['website_customthumb']));
		$data['siteactive']			= trim(strip_tags($_POST['website_siteactive']));
		$data['displaylink']		= trim(strip_tags($_POST['website_displaylink']));
		$data['siteorder']			= trim(strip_tags($_POST['website_siteorder'])) + 0;
		$data['specificpage']	    = trim(strip_tags($_POST['website_specificpage']));		
		$data['customfield'] 		= trim(strip_tags($_POST['website_customfield']));
		$data['siteadded']			= trim(strip_tags($_POST['siteadded']));
		
		// Keep track of errors for validation
		$errors = array();
				
		// Ensure all fields have been completed
		if (!($data['sitename'] && $data['siteurl'] && $data['sitedescription']) ) {
			array_push($errors, __("Please check that you have completed the site name, url and description fields.", 'wp-portfolio'));
		}

		// Check custom field length
		if (strlen($data['customfield']) > 255) {
			array_push($errors, __("Sorry, but the custom field is limited to a maximum of 255 characters.", 'wp-portfolio'));
		}
		
		// Check that the date is correct
		if ($data['siteadded']) 
		{
			$dateTS = 0; //strtotime($data['siteadded']);
			if (preg_match('/^([0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2})$/', $data['siteadded'], $matches)) {
				$dateTS = strtotime($data['siteadded']);
			}
			
			// Invalid date
			if ($dateTS == 0) {
				array_push($errors, __("Sorry, but the 'Date Added' date format was not recognised. Please enter a date in the format <em>'yyyy-mm-dd hh:mm:ss'</em>.", 'wp-portfolio'));
			}
			
			// Valid Date
			else {
				$data['siteadded'] = date('Y-m-d H:i:s', $dateTS); 
			}
		} 
		
		else {
			// Date is blank, so create correct one.
			$data['siteadded'] = date('Y-m-d H:i:s'); 
		}
		
		// Continue if there are no errors
		if (count($errors) == 0)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . TABLE_WEBSITES;
			
			// Change query based on add or edit
			if ($editmode) {						
				$query = arrayToSQLUpdate($table_name, $data, 'siteid');
			}

			// Add
			else {
				unset($data['siteid']); // Don't need id for an insert

				$data['siteadded'] = date('Y-m-d H:i:s'); // Only used if adding a website.
				
				$query = arrayToSQLInsert($table_name, $data);	
			}			
						
			// Try to put the data into the database
			$wpdb->show_errors();
			$wpdb->query($query);
			
			// When adding, clean fields so that we don't show them again.
			if ($editmode) {
				WPPortfolio_showMessage(__("Website details successfully updated.", 'wp-portfolio'));
				
				// Retrieve the details from the database again
				$websitedetails = WPPortfolio_getWebsiteDetails($siteid);				
			} 
			// When adding, empty the form again
			else
			{	
				WPPortfolio_showMessage(__("Website details successfully added.", 'wp-portfolio'));
					
				$data['siteid'] 			= false;
				$data['sitename'] 			= false;
				$data['siteurl'] 			= false;
				$data['sitedescription'] 	= false;
				$data['sitegroup'] 			= false;
				$data['customthumb']		= false;				
				$data['siteactive']			= 1; // The default is that the website is visible.				
				$data['displaylink']		= 1; // The default is to show the link.			
				$data['siteorder']			= 0;
				$data['specificpage']	    = 0; 
				$data['customfield']		= false;
			}
								
		} // end of error checking
	
		// Handle error messages
		else
		{
			$message = __("Sorry, but unfortunately there were some errors. Please fix the errors and try again.", 'wp-portfolio').'<br><br>';
			$message .= "<ul style=\"margin-left: 20px; list-style-type: square;\">";
			
			// Loop through all errors in the $error list
			foreach ($errors as $errormsg) {
				$message .= "<li>$errormsg</li>";
			}
						
			$message .= "</ul>";
			WPPortfolio_showMessage($message, true);
			$websitedetails = $data;
		}
	}
		
	$form = new FormBuilder();
		
	$formElem = new FormElement("website_sitename", __("Website Name", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'sitename');
	$formElem->description = __("The proper name of the website.", 'wp-portfolio').' <em>'.__('(Required)', 'wp-portfolio').'</em>';
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("website_siteurl", __("Website URL", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'siteurl');
	$formElem->description = __("The URL for the website, including the leading", 'wp-portfolio').' <em>http://</em>. <em>'.__('(Required)', 'wp-portfolio').'</em>';
	$form->addFormElement($formElem);	
	
	$formElem = new FormElement("website_sitedescription", __("Website Description", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'sitedescription');
	$formElem->description = __("The description of your website. HTML is permitted.", 'wp-portfolio').' <em>'.__('(Required)', 'wp-portfolio')."</em>";
	$formElem->setTypeAsTextArea(4, 70);
	$form->addFormElement($formElem);	
	
	global $wpdb;
	$table_name = $wpdb->prefix . TABLE_WEBSITE_GROUPS;
	$SQL = "SELECT * FROM $table_name ORDER BY groupname";	
	$groups = $wpdb->get_results($SQL, OBJECT);	
	$grouplist = array();
	
	foreach ($groups as $group) {
		$grouplist[$group->groupid] =  stripslashes($group->groupname);
	}	
		
	$formElem = new FormElement("website_sitegroup", "Website Group");
	$formElem->setTypeAsComboBox($grouplist);				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'sitegroup');
	$formElem->description = __("The group you want to assign this website to.", 'wp-portfolio');
	$form->addFormElement($formElem);	
	
	$form->addBreak('advanced-options', '<div id="wpp-hide-show-advanced" class="wpp_hide"><a href="#">'.__('Show Advanced Settings', 'wp-portfolio').'</a></div>');

	$formElem = new FormElement("website_siteactive", __("Show Website?", 'wp-portfolio'));
	$formElem->setTypeAsComboBox(array('1' => __('Show Website', 'wp-portfolio'), '0' => __('Hide Website', 'wp-portfolio')));
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'siteactive');
	$formElem->description = __("By changing this option, you can show or hide a website from the portfolio.", 'wp-portfolio');
	$form->addFormElement($formElem);

	$formElem = new FormElement("website_displaylink", __("Show Link?", 'wp-portfolio'));
	$formElem->setTypeAsComboBox(array('show_link' => __('Show Link', 'wp-portfolio'), 'hide_link' => __('Hide Link', 'wp-portfolio')));
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'displaylink');
	$formElem->description = __("With this option, you can choose whether or not to display the URL to the website.", 'wp-portfolio');
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("siteadded", __("Date Website Added", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'siteadded');
	$formElem->description = __("Here you can adjust the date in which the website was added to the portfolio. This is useful if you're adding items retrospectively. (valid format is yyyy-mm-dd hh:mm:ss)", 'wp-portfolio');
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("website_siteorder", __("Website Ordering", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'siteorder');
	$formElem->description = '&bull; '.__("The number to use for ordering the websites. Websites are rendered in ascending order, first by this order value (lowest value first), then by website name.", 'wp-portfolio').'<br/>'.
				'&bull; '.__("e.g. Websites (A, B, C, D) with ordering (50, 100, 0, 50) will be rendered as (C, A, D, B).", 'wp-portfolio').'<br/>'.
				'&bull; '.__("If all websites have 0 for ordering, then the websites are rendered in alphabetical order by name.", 'wp-portfolio');
	$form->addFormElement($formElem);	
			
	
	$formElem = new FormElement("website_customthumb", __("Custom Thumbnail URL", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'customthumb');
	$formElem->cssclass = "long-text";
	$formElem->description = __("If specified, the URL of a custom thumbnail to use <em>instead</em> of the screenshot of the URL above.", 'wp-portfolio').'<br/>'.
							'&bull; '.__("The image URL must include the leading <em>http://</em>, e.g.", 'wp-portfolio').' <em>http://www.yoursite.com/wp-content/uploads/yourfile.jpg</em><br/>'.
							'&bull; '.__("Leave this field blank to use an automatically generated screenshot of the website specified above.", 'wp-portfolio').'<br/>'.
							'&bull; '.__("Custom thumbnails are automatically resized to match the size of the other thumbnails.", 'wp-portfolio');
	$form->addFormElement($formElem);	
	
	$formElem = new FormElement("website_customfield", __("Custom Field", 'wp-portfolio')."<br/><span class=\"wpp-advanced-feature\">&bull; ".__("Advanced Feature", 'wp-portfolio')."</span>");
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'customfield');
	$formElem->cssclass = "long-text";
	$formElem->description = sprintf(__("Allows you to specify a value that is substituted into the <code><b>%s</b></code> field. This can be any value. Examples of what you could use the custom field for include:", 'wp-portfolio'), WPP_STR_WEBSITE_CUSTOM_FIELD).'<br/>'.
								'&bull; '.__("Affiliate URLs for the actual URL that visitors click on.", 'wp-portfolio').'<br/>'.
								'&bull; '.__("Information as to the type of work a website relates to (e.g. design work, SEO, web development).", 'wp-portfolio');
	$form->addFormElement($formElem);

	
	// Advanced Features
	$formElem = new FormElement("website_specificpage", __("Use Specific Page Capture", 'wp-portfolio')."<br/>".
								"<span class=\"wpp-advanced-feature\">&bull; ".__("Advanced Feature", 'wp-portfolio')."</span><br/>".
								"<span class=\"wpp-stw-paid\">&bull; ".__("STW Paid Account Only", 'wp-portfolio')."</span>");
	$formElem->setTypeAsComboBox(array('0' => __('No - Homepage Only', 'wp-portfolio'), '1' => __('Yes - Show Specific Page', 'wp-portfolio')));				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'specificpage');
	$formElem->description = '&bull; <b>'.__("Requires Shrink The Web 'Specific Page Capture' Paid (Basic or Plus) feature.", 'wp-portfolio').'</b><br/>'.
							  '&bull; '.__("If enabled show internal web page rather than website's homepage. If in doubt, select <b>'No - Homepage Only'</b>.", 'wp-portfolio');
	$form->addFormElement($formElem);	
	
	// Hidden Elements
	$formElem = new FormElement("website_siteid", false);				
	$formElem->value = WPPortfolio_getArrayValue($websitedetails, 'siteid');
	$formElem->setTypeAsHidden();
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("editmode", false);				
	$formElem->value = ($editmode ? "edit" : "add");
	$formElem->setTypeAsHidden();
	$form->addFormElement($formElem);	
	
	
	$form->setSubmitLabel(($editmode ? __("Update", 'wp-portfolio') : __("Add", 'wp-portfolio')). " ".__("Website Details", 'wp-portfolio'));		
	echo $form->toString();
			
	?>	
	<br><br>
	</div><!-- wrap -->
	<?php 	
}


/**
 * Shows the page that allows a group to be modified.
 */
function WPPortfolio_modify_group()
{
	// Determine if we're in edit mode. Ensure we get correct mode regardless of where it is.
	$editmode = false;
	if (isset($_POST['editmode'])) {
		$editmode = ($_POST['editmode'] == 'edit');
	} else if (isset($_GET['editmode'])) {
		$editmode = ($_GET['editmode'] == 'edit');
	}	
	
	// Get the Group ID. Ensure we get ID regardless of where it is.
	$groupid = 0;
	if (isset($_POST['group_groupid'])) {
		$groupid = (is_numeric($_POST['group_groupid']) ? $_POST['group_groupid'] + 0 : 0);
	} else if (isset($_GET['groupid'])) {
		$groupid = (is_numeric($_GET['groupid']) ? $_GET['groupid'] + 0 : 0);
	}

	$verb = __("Add New", 'wp-portfolio');
	if ($editmode) {
		$verb = __("Modify", 'wp-portfolio');
	}
	
	// Show title to determine action
	?>
	<div class="wrap">
	<div id="icon-edit" class="icon32">
	<br/>
	</div>
	<h2><?php echo $verb.__(' Group Details', 'wp-portfolio'); ?></h2>
	<?php 
	
	// Check id is a valid number if editing $editmode
	if ($editmode && $groupid == 0) {
		WPPortfolio_showMessage(sprintf(__('Sorry, but no group with that ID could be found. Please click <a href="%s">here</a> to return to the list of groups.', 'wp-portfolio'), WPP_GROUP_SUMMARY), true);
		return;
	}	
	$groupdetails = false;

	// ### EDIT ### Check if we're adding or editing a group
	if ($editmode && $groupid > 0)
	{
		// Get details from the database				
		$groupdetails = WPPortfolio_getGroupDetails($groupid);

		// False alarm, couldn't find it.
		if (count($groupdetails) == 0) {
			$editmode = false;
		}
		
	} // end of editing check
			
	// Check if group is being updated/added.
	if ( isset($_POST) && isset($_POST['update']) )
	{
		// Grab specified details
		$data = array();
		$data['groupid'] 			= $groupid;	
		$data['groupname'] 		  	= strip_tags($_POST['group_groupname']);
		$data['groupdescription'] 	= $_POST['group_groupdescription'];
		$data['grouporder'] 		= $_POST['group_grouporder'] + 0; // Add zero to convert to number
						
		// Keep track of errors for validation
		$errors = array();
				
		// Ensure all fields have been completed
		if (!($data['groupname'] && $data['groupdescription'])) {
			array_push($errors, __("Please check that you have completed the group name and description fields.", 'wp-portfolio'));
		}	
		
		// Continue if there are no errors
		if (count($errors) == 0)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . TABLE_WEBSITE_GROUPS;

			// Change query based on add or edit
			if ($editmode) {							
				$query = arrayToSQLUpdate($table_name, $data, 'groupid');
			}

			// Add
			else {
				unset($data['groupid']); // Don't need id for an insert	
				$query = arrayToSQLInsert($table_name, $data);	
			}
			
			// Try to put the data into the database
			$wpdb->show_errors();
			$wpdb->query($query);
			
			// When editing, show what we've just been editing.
			if ($editmode) {
				WPPortfolio_showMessage(__("Group details successfully updated.", 'wp-portfolio'));
				
				// Retrieve the details from the database again
				$groupdetails = WPPortfolio_getGroupDetails($groupid);
			} 
			// When adding, empty the form again
			else {																							
				WPPortfolio_showMessage(__("Group details successfully added.", 'wp-portfolio'));
				
				$groupdetails['groupid'] 			= false;
				$groupdetails['groupname'] 			= false;
				$groupdetails['groupdescription'] 	= false;
				$groupdetails['grouporder'] 		= false;
			}

		} // end of error checking
	
		// Handle error messages
		else
		{
			$message = __("Sorry, but unfortunately there were some errors. Please fix the errors and try again.", 'wp-portfolio').'<br><br>';
			$message .= "<ul style=\"margin-left: 20px; list-style-type: square;\">";
			
			// Loop through all errors in the $error list
			foreach ($errors as $errormsg) {
				$message .= "<li>$errormsg</li>";
			}
						
			$message .= "</ul>";
			WPPortfolio_showMessage($message, true);
			$groupdetails = $data;
		}
	}
	
	$form = new FormBuilder();
	
	$formElem = new FormElement("group_groupname", __("Group Name", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($groupdetails, 'groupname');
	$formElem->description = __("The name for this group of websites.", 'wp-portfolio');
	$form->addFormElement($formElem);	
	
	$formElem = new FormElement("group_groupdescription", __("Group Description", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($groupdetails, 'groupdescription');
	$formElem->description = __("The description of your group. HTML is permitted.", 'wp-portfolio');
	$formElem->setTypeAsTextArea(4, 70);
	$form->addFormElement($formElem);		
	
	$formElem = new FormElement("group_grouporder", __("Group Order", 'wp-portfolio'));				
	$formElem->value = WPPortfolio_getArrayValue($groupdetails, 'grouporder');
	$formElem->description = '&bull; '.__("The number to use for ordering the groups. Groups are rendered in ascending order, first by this order value (lowest value first), then by group name.", 'wp-portfolio').'<br/>'.
				'&bull; '.__('e.g. Groups (A, B, C, D) with ordering (50, 100, 0, 50) will be rendered as (C, A, D, B).', 'wp-portfolio').'<br/>'.
				'&bull; '.__("If all groups have 0 for ordering, then the groups are rendered in alphabetical order.", 'wp-portfolio');
	$form->addFormElement($formElem);		
	
	// Hidden Elements
	$formElem = new FormElement("group_groupid", false);				
	$formElem->value = WPPortfolio_getArrayValue($groupdetails, 'groupid');
	$formElem->setTypeAsHidden();
	$form->addFormElement($formElem);
	
	$formElem = new FormElement("editmode", false);				
	$formElem->value = ($editmode ? "edit" : "add");
	$formElem->setTypeAsHidden();
	$form->addFormElement($formElem);	
	
	
	$form->setSubmitLabel(($editmode ? __("Update", 'wp-portfolio') : __("Add", 'wp-portfolio')). " ".__("Group Details", 'wp-portfolio'));		
	echo $form->toString();	
		
	?>		
	<br><br>
	</div><!-- wrap -->
	<?php 	
}

/**
 * Return the list of settings for this plugin.
 * @return Array The list of settings and their default values.
 */
function WPPortfolio_getSettingList($general = true, $style = true)
{
	$generalSettings = array(
		'setting_stw_access_key' 		=> false,
		'setting_stw_secret_key' 		=> false,
		'setting_stw_account_type'		=> false,
		'setting_stw_thumb_size' 		=> 'lg',
		'setting_stw_thumb_size_type'	=> 'standard',
		'setting_stw_thumb_size_custom' => '300',
		'setting_cache_days'	 		=> 7,
		'setting_fetch_method' 			=> 'curl',
		'setting_show_credit' 			=> 'on',	
		'setting_enable_debug'			=> false,
		'setting_scale_type'			=> 'scale-both',
	);
	
	$styleSettings = array(
		'setting_template_website'			=> WPP_DEFAULT_WEBSITE_TEMPLATE,
		'setting_template_group'			=> WPP_DEFAULT_GROUP_TEMPLATE,
		'setting_template_css'				=> WPP_DEFAULT_CSS,
		'setting_template_css_paging'		=> WPP_DEFAULT_CSS_PAGING,
		'setting_template_css_widget'		=> WPP_DEFAULT_CSS_WIDGET,
		'setting_disable_plugin_css'		=> false,
		'setting_template_paging'			=> WPP_DEFAULT_PAGING_TEMPLATE,
		'setting_template_paging_previous'	=> WPP_DEFAULT_PAGING_TEMPLATE_PREVIOUS,
		'setting_template_paging_next'		=> WPP_DEFAULT_PAGING_TEMPLATE_NEXT,
	);
	
	$settingsList = array();
	
	// Want to add general settings?
	if ($general) {
		$settingsList = array_merge($settingsList, $generalSettings);
	}
	
	// Want to add style settings?
	if ($style) {
		$settingsList = array_merge($settingsList, $styleSettings);
	}
	
	return $settingsList;
}


/**
 * Install the WP Portfolio plugin, initialise the default settings, and create the tables for the websites and groups.
 */
function WPPortfolio_install()
{
	// ### Create Default Settings
	$settingsList = WPPortfolio_getSettingList();
	
	// Initialise all settings in the database
	foreach ($settingsList as $settingName => $settingDefault) 
	{
		if (get_option('WPPortfolio_'.$settingName) === FALSE)
		{
			// Set the default option
			update_option('WPPortfolio_'.$settingName, $settingDefault);
		}
	}
							
		
	// Check the current version of the database
	$installed_ver  = get_option("WPPortfolio_version") + 0;
	$current_ver    = WPP_VERSION + 0;
	$upgrade_tables = ($current_ver > $installed_ver);
	
	// Upgrade tables
	WPPortfolio_install_upgradeTables($upgrade_tables);		
	
			
	// Update the version regardless
	update_option("WPPortfolio_version", WPP_VERSION);
	
	// Create cache directory
	WPPortfolio_createCacheDirectory(); 
}
register_activation_hook(__FILE__,'WPPortfolio_install');


/**
 * Function to upgrade tables.
 * @param Boolean $upgradeNow If true, upgrade tables now.
 */
function WPPortfolio_install_upgradeTables($upgradeNow, $showErrors = false, $addSampleData = true)
{
	global $wpdb;
		
	// Table names
	$table_websites	= $wpdb->prefix . TABLE_WEBSITES;
	$table_groups 	= $wpdb->prefix . TABLE_WEBSITE_GROUPS;
	$table_debug    = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
	
	if ($showErrors) {
		$wpdb->show_errors();
	}	
				
	// Check tables exist
	$table_websites_exists	= ($wpdb->get_var("SHOW TABLES LIKE '$table_websites'") == $table_websites);
	$table_groups_exists	= ($wpdb->get_var("SHOW TABLES LIKE '$table_groups'") == $table_groups);
	$table_debug_exists		= ($wpdb->get_var("SHOW TABLES LIKE '$table_debug'") == $table_debug);
	
	// Only enable if debugging	
	//$wpdb->show_errors();

	// #### Create Tables - Websites
	if (!$table_websites_exists || $upgradeNow) 
	{
		$sql = "CREATE TABLE `$table_websites` (
  				   siteid INT(10) unsigned NOT NULL auto_increment,
				   sitename varchar(150),
				   siteurl varchar(255),
				   sitedescription TEXT,
				   sitegroup int(10) unsigned NOT NULL,
				   customthumb varchar(255),
				   customfield varchar(255),
				   siteactive TINYINT NOT NULL DEFAULT '1',
				   displaylink varchar(10) NOT NULL DEFAULT 'show_link',
				   siteorder int(10) unsigned NOT NULL DEFAULT '0',
				   specificpage TINYINT NOT NULL DEFAULT '0',	
				   siteadded datetime default NULL,
				   PRIMARY KEY  (siteid) 
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
				
	}
	
	// Set default date if there isn't one
	$results = $wpdb->query("UPDATE `$table_websites` SET `siteadded` = NOW() WHERE `siteadded` IS NULL OR `siteadded` = '0000-00-00 00:00:00'");
	
	
	// #### Create Tables - Groups
	if (!$table_groups_exists || $upgradeNow)
	{
		$sql = "CREATE TABLE `$table_groups` (
  				   groupid int(10) UNSIGNED NOT NULL auto_increment,
				   groupname varchar(150),
				   groupdescription TEXT,
				   grouporder INT(1) UNSIGNED NOT NULL DEFAULT '0',
				   PRIMARY KEY  (groupid)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		// Creating new table? Add default group that has ID of 0
		if ($addSampleData)
		{
			$SQL = "INSERT INTO `$table_groups` (groupid, groupname, groupdescription) VALUES (1, 'My Websites', 'These are all my websites.')";
	 		$results = $wpdb->query($SQL);
		}
	}	
	
	// Needed for hard upgrade - existing method of trying to update
	// the table seems to be failing.
	$wpdb->query("DROP TABLE IF EXISTS $table_debug");
	
	// #### Create Tables - Debug Log
	if (!$table_debug_exists || $upgradeNow) 
	{
		$sql = "CREATE TABLE $table_debug (
  				  `logid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `request_url` varchar(255) NOT NULL,
				  `request_param_hash` varchar(35) NOT NULL,
				  `request_result` tinyint(4) NOT NULL DEFAULT '0',
				  `request_error_msg` varchar(30) NOT NULL,
				  `request_detail` text NOT NULL,
				  `request_type` varchar(25) NOT NULL,
				  `request_date` datetime NOT NULL,
  				  PRIMARY KEY  (logid)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}


/**
 * Add the custom stylesheet for this plugin.
 */
function WPPortfolio_styles_Backend()
{
	// Only show our stylesheet on a WP Portfolio page to avoid breaking other plugins.
	if (!WPPortfolio_areWeOnWPPPage()) {
		return;
	}
		
	wp_enqueue_style('wpp-portfolio', 			WPPortfolio_getPluginPath() . 'portfolio.css', false, WPP_VERSION);
}



/** 
 * Add the scripts needed for the page for this plugin.
 */
function WPPortfolio_scripts_Backend()
{
	if (!WPPortfolio_areWeOnWPPPage()) 
		return;
		
	// Plugin-specific JS
	wp_enqueue_script('wpl-admin-js', WPPortfolio_getPluginPath() .  'js/wpp-admin.js', array('jquery'), WPP_VERSION);
}


/**
 * Scripts used on front of website.
 */
function WPPortfolio_scripts_Frontend()
{		
}    




/**
 * Get the URL for the plugin path including a trailing slash.
 * @return String The URL for the plugin path.
 */
function WPPortfolio_getPluginPath() {
	return trailingslashit(trailingslashit(WP_PLUGIN_URL) . plugin_basename(dirname(__FILE__)));
}


/**
 * Method called when we want to uninstall the portfolio plugin to remove the database tables.
 */
function WPPortfolio_uninstall() 
{
	// Remove all options from the database
	delete_option('WPPortfolio_setting_stw_access_key');
	delete_option('WPPortfolio_setting_stw_secret_key');	
	delete_option('WPPortfolio_setting_stw_thumb_size');
	delete_option('WPPortfolio_setting_cache_days');
	
	delete_option('WPPortfolio_setting_template_website');
	delete_option('WPPortfolio_setting_template_group');
	delete_option('WPPortfolio_setting_template_css');
	delete_option('WPPortfolio_setting_template_css_paging');
	delete_option('WPPortfolio_setting_template_css_widget');
			
	delete_option('WPPortfolio_version');
		
	
	// Remove all tables for the portfolio
	global $wpdb;
	$table_name    = $wpdb->prefix . TABLE_WEBSITES;
	$uninstall_sql = "DROP TABLE IF EXISTS ".$table_name;
	$wpdb->query($uninstall_sql);
	
	$table_name    = $wpdb->prefix . TABLE_WEBSITE_GROUPS;
	$uninstall_sql = "DROP TABLE IF EXISTS ".$table_name;
	$wpdb->query($uninstall_sql);
		
	$table_name    = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
	$uninstall_sql = "DROP TABLE IF EXISTS ".$table_name;
	$wpdb->query($uninstall_sql);
	
	
	// Remove cache
	$actualThumbPath = WPPortfolio_getThumbPathActualDir();
	WPPortfolio_unlinkRecursive($actualThumbPath);		
		
	WPPortfolio_showMessage(__("Deleted WP Portfolio database entries.", 'wp-portfolio'));
}




/**
 * This method is called just before the <head> tag is closed. We inject our custom CSS into the 
 * webpage here.
 */
function WPPortfolio_styles_frontend_renderCSS() 
{
	// Only render CSS if we've enabled the option
	$setting_disable_plugin_css = strtolower(trim(get_option('WPPortfolio_setting_disable_plugin_css')));
	
	// on = disable, anything else is enable
	if ($setting_disable_plugin_css != 'on')
	{
		$setting_template_css 		 = trim(stripslashes(get_option('WPPortfolio_setting_template_css')));
		$setting_template_css_paging = trim(stripslashes(get_option('WPPortfolio_setting_template_css_paging')));
		$setting_template_css_widget = trim(stripslashes(get_option('WPPortfolio_setting_template_css_widget')));
	
		echo "\n<!-- WP Portfolio Stylesheet -->\n";
		echo "<style type=\"text/css\">\n";
		
		echo $setting_template_css;
		echo $setting_template_css_paging;
		echo $setting_template_css_widget;
		
		echo "\n</style>";
		echo "\n<!-- WP Portfolio Stylesheet -->\n";
	}
}



/**
 * Turn the portfolio of websites in the database into a single page containing details and screenshots using the [wp-portfolio] shortcode.
 * @param $atts The attributes of the shortcode.
 * @return String The updated content for the post or page.
 */
function WPPortfolio_convertShortcodeToPortfolio($atts)
{	
	// Process the attributes
	extract(shortcode_atts(array(
		'groups' 		=> '',
		'hidegroupinfo' => 0,
		'sitesperpage'	=> 0,
		'orderby' 		=> 'asc',
		'ordertype'		=> 'normal',
		'single'		=> 0,
	), $atts));
	
	// Check if single contains a valid item ID
	if (is_numeric($single) && $single > 0) 
	{	
		$websiteDetails = WPPortfolio_getWebsiteDetails($single, OBJECT);
		
		// Portfolio item not found, abort
		if (!$websiteDetails) {
			return sprintf('<p>'.__('Portfolio item <b>ID %d</b> does not exist.', 'wp-portfolio').'</p>', $single); 
		}
		
		// Item found, so render it
		else  {
			return WPPortfolio_renderPortfolio(array($websiteDetails), false, false, false, false);
		}
	
	}
	
	// If hidegroupinfo is 1, then hide group details by passing in a blank template to the render portfolio function
	$grouptemplate = false; // If false, then default group template is used.
	if (isset($atts['hidegroupinfo']) && $atts['hidegroupinfo'] == 1) {
		$grouptemplate = "&nbsp;";
	}
	
	// Sort ASC or DESC?
	$orderAscending = true;
	if (isset($atts['orderby']) && strtolower(trim($atts['orderby'])) == 'desc') {
		$orderAscending = false;
	}
	
	// Convert order type to use normal as default
	$orderType = strtolower(trim(WPPortfolio_getArrayValue($atts, 'ordertype')));
	if ($orderType != 'dateadded') {
		$orderType = 'normal';
	}
	
	// Groups 
	$groups = false;
	if (isset($atts['groups'])) {
		$groups = $atts['groups'];
	}
	
	// Sites per page
	$sitesperpage = 0;
	if (isset($atts['sitesperpage'])) {
		$sitesperpage = $atts['sitesperpage'] + 0;
	}
	
	return WPPortfolio_getAllPortfolioAsHTML($groups, false, $grouptemplate, $sitesperpage, $orderAscending, $orderType);
}
add_shortcode('wp-portfolio', 'WPPortfolio_convertShortcodeToPortfolio');



/**
 * Method to get the portfolio using the specified list of groups and return it as HTML.
 * 
 * @param $groups The comma separated string of group IDs to show.
 * @param $template_website The template used to render each website. If false, the website template defined in the settings is used instead.
 * @param $template_group The template used to render each group header. If false, the group template defined in the settings is used instead.
 * @param $sitesperpage The number of sites to show per page, or false if showing all sites at once. 
 * @param $orderAscending Order websites in ascending order, or if false, order in descending order.
 * @param $orderBy How to order the results (choose from 'normal' or 'dateadded'). Default option is 'normal'. If 'dateadded' is chosen, group names are not shown.
 * @param $count If > 0, only show the specified number of websites. This overrides $sitesperpage.
 * @param $isWidgetTemplate If true, then we're rendering this as a widget layout. 
 * 
 * @return String The HTML which contains the portfolio as HTML.
 */
function WPPortfolio_getAllPortfolioAsHTML($groups = '', $template_website = false, $template_group = false, $sitesperpage = false, $orderAscending = true, $orderBy = 'normal', $count = false, $isWidgetTemplate = false)
{
	// Get portfolio from database
	global $wpdb;
	$websites_table = $wpdb->prefix . TABLE_WEBSITES;
	$groups_table   = $wpdb->prefix . TABLE_WEBSITE_GROUPS;		
		
	// Determine if we only want to show certain groups
	$WHERE_CLAUSE = "";
	if ($groups)
	{ 
		$selectedGroups = explode(",", $groups);
		foreach ($selectedGroups as $possibleGroup)
		{
			// Some matches might be empty strings
			if ($possibleGroup > 0) {
				$WHERE_CLAUSE .= "$groups_table.groupid = '$possibleGroup' OR ";
			}
		}
	} // end of if ($groups)
		
	// Add initial where if needed
	if ($WHERE_CLAUSE)
	{
		// Remove last OR to maintain valid SQL
		if (substr($WHERE_CLAUSE, -4) == ' OR ') {
			$WHERE_CLAUSE = substr($WHERE_CLAUSE, 0, strlen($WHERE_CLAUSE)-4);
		}				
		
		// Selectively choosing groups.
		$WHERE_CLAUSE = sprintf("WHERE (siteactive = 1) AND (%s)", $WHERE_CLAUSE);
	} 
	// Showing whole portfolio, but only active sites.
	else {
		$WHERE_CLAUSE = "WHERE (siteactive = 1)";
	}

	$ORDERBY_ORDERING = "";
	if (!$orderAscending) {
		$ORDERBY_ORDERING = 'DESC';
	}
	
	// How to order the results
	if (strtolower($orderBy) == 'dateadded') {
		$ORDERBY_CLAUSE = "ORDER BY siteadded $ORDERBY_ORDERING, sitename ASC";
		$template_group = ' '; // Disable group names
	} else {
		$ORDERBY_CLAUSE = "ORDER BY grouporder $ORDERBY_ORDERING, groupname $ORDERBY_ORDERING, siteorder $ORDERBY_ORDERING, sitename $ORDERBY_ORDERING";
	}
			
	// Get website details, merge with group details
	$SQL = "SELECT * FROM $websites_table
			LEFT JOIN $groups_table ON $websites_table.sitegroup = $groups_table.groupid
			$WHERE_CLAUSE
		 	$ORDERBY_CLAUSE
		 	";			
					
	$wpdb->show_errors();
	
	$paginghtml = false; 
	
	
	$LIMIT_CLAUSE = false;
	
	// Convert to a number
	$count = $count + 0;
	$sitesperpage = $sitesperpage + 0; 
	
	// Show a limited number of websites	
	if ($count > 0) {
		$LIMIT_CLAUSE = 'LIMIT '.$count;
	}
	
	// Limit the number of sites shown on a single page.	
	else if ($sitesperpage)
	{
		// How many sites do we have?
		$websites = $wpdb->get_results($SQL, OBJECT);
		$website_count = $wpdb->num_rows;
		
		// Paging is needed, as we have more websites than sites/page.
		if ($website_count > $sitesperpage)
		{
			$numofpages = ceil($website_count / $sitesperpage);
			
			// Pick up the page number from the GET variable
			$currentpage = 1;
			if (isset($_GET['portfolio-page']) && ($_GET['portfolio-page'] + 0) > 0) {
				$currentpage = $_GET['portfolio-page'] + 0;
			}			

			// Load paging defaults from the DB
			$setting_template_paging 			= stripslashes(get_option('WPPortfolio_setting_template_paging'));
			$setting_template_paging_next 		= stripslashes(get_option('WPPortfolio_setting_template_paging_next'));
			$setting_template_paging_previous 	= stripslashes(get_option('WPPortfolio_setting_template_paging_previous'));
			

			// Add Previous Jump Links
			if ($numofpages > 1 && $currentpage > 1) { 
				$html_previous = sprintf('&nbsp;<span class="page-jump"><a href="?portfolio-page=%s"><b>%s</b></a></span>&nbsp;', $currentpage-1, $setting_template_paging_previous);
			} else {
				$html_previous = sprintf('&nbsp;<span class="page-jump"><b>%s</b></span>&nbsp;', $setting_template_paging_previous);
			}			
			
			
			// Render the individual pages
			$html_pages = false;
			for ($i = 1; $i <= $numofpages; $i++) 
			{								
				// No link for current page.
				if ($i == $currentpage) {
					$html_pages .= sprintf('&nbsp;<span class="page-jump page-current"><b>%s</b></span>&nbsp;', $i, $i);
				} 
				// Link for other pages 
				else  {
					// Avoid parameter if first page
					if ($i == 1) {
						$html_pages .= sprintf('&nbsp;<span class="page-jump"><a href="?"><b>%s</b></a></span>&nbsp;', $i, $i);
					} else {
						$html_pages .= sprintf('&nbsp;<span class="page-jump"><a href="?portfolio-page=%s"><b>%s</b></a></span>&nbsp;', $i, $i);
					}
				}				
			}
			// Add Next Jump Links
			if ($currentpage < $numofpages) {
				$html_next = sprintf('&nbsp;<span class="page-jump"><a href="?portfolio-page=%s"><b>%s</b></a></span>&nbsp;', $currentpage+1, $setting_template_paging_next);
			} else {
				$html_next = sprintf('&nbsp;<span class="page-jump"><b>%s</b></span>&nbsp;', $setting_template_paging_next);
			}

			

			// Update the SQL for the pages effect
			// Show first page and set limit to start at first record.
			if ($currentpage <= 1) {
				$firstresult = 1;
				$LIMIT_CLAUSE = sprintf("LIMIT 0, %s", $sitesperpage);
			} 
			// Show websites only for current page for inner page
			else
			{
				$firstresult = (($currentpage - 1) * $sitesperpage);
				$LIMIT_CLAUSE = sprintf("LIMIT %s, %s", $firstresult, $sitesperpage);
			}
			
			// Work out the number of the website being shown at the end of the range. 
			$website_endNum = ($currentpage * $sitesperpage);
			if ($website_endNum > $website_count) {
				$website_endNum = $website_count;
			}
			
			
			// Create the paging HTML using the templates.
			$paginghtml = $setting_template_paging;
			
			// Summary info			
			$paginghtml = str_replace('%PAGING_PAGE_CURRENT%', 		$currentpage, 		$paginghtml);
			$paginghtml = str_replace('%PAGING_PAGE_TOTAL%', 		$numofpages, 		$paginghtml);

			$paginghtml = str_replace('%PAGING_ITEM_START%', 		$firstresult, 		$paginghtml);
			$paginghtml = str_replace('%PAGING_ITEM_END%', 			$website_endNum, 	$paginghtml);
			$paginghtml = str_replace('%PAGING_ITEM_TOTAL%', 		$website_count, 	$paginghtml);
			
			// Navigation
			$paginghtml = str_replace('%LINK_PREVIOUS%', 			$html_previous, 	$paginghtml);
			$paginghtml = str_replace('%LINK_NEXT%', 				$html_next, 		$paginghtml);
			$paginghtml = str_replace('%PAGE_NUMBERS%', 			$html_pages, 		$paginghtml);
			
		} // end of if ($website_count > $sitesperpage)
	}
	
	
	// Add the limit clause.
	$SQL .= $LIMIT_CLAUSE;
		
	$websites = $wpdb->get_results($SQL, OBJECT);

	// If we've got websites to show, then render into HTML
	if ($websites) {
		$portfolioHTML = WPPortfolio_renderPortfolio($websites, $template_website, $template_group, $paginghtml, $isWidgetTemplate);
	} else {
		$portfolioHTML = false;
	}
	
	return $portfolioHTML;
}




/**
 * Method to get a random selection of websites from the portfolio using the specified list of groups and return it as HTML. No group details are 
 * returned when showing a random selection of the portfolio.
 * 
 * @param $groups The comma separated string of group IDs to use to find which websites to show. If false, websites are selected from the whole portfolio.
 * @param $count The number of websites to show in the output.
 * @param $template_website The template used to render each website. If false, the website template defined in the settings is used instead.
 * @param $isWidgetTemplate If true, then we're rendering this as a widget layout. 
 * 
 * @return String The HTML which contains the portfolio as HTML.
 */
function WPPortfolio_getRandomPortfolioSelectionAsHTML($groups = '', $count = 3, $template_website = false, $isWidgetTemplate = false)
{
	// Get portfolio from database
	global $wpdb;
	$websites_table = $wpdb->prefix . TABLE_WEBSITES;
	$groups_table   = $wpdb->prefix . TABLE_WEBSITE_GROUPS;		
		
	// Validate the count is a number
	$count = $count + 0;
	
	// Determine if we only want to get websites from certain groups
	$WHERE_CLAUSE = "";
	if ($groups)
	{ 
		$selectedGroups = explode(",", $groups);
		foreach ($selectedGroups as $possibleGroup)
		{
			// Some matches might be empty strings
			if ($possibleGroup > 0) {
				$WHERE_CLAUSE .= "$groups_table.groupid = '$possibleGroup' OR ";
			}
		}
	} // end of if ($groups)
		
	// Add initial where if needed
	if ($WHERE_CLAUSE)
	{
		// Remove last OR to maintain valid SQL
		if (substr($WHERE_CLAUSE, -4) == ' OR ') {
			$WHERE_CLAUSE = substr($WHERE_CLAUSE, 0, strlen($WHERE_CLAUSE)-4);
		}				
		
		$WHERE_CLAUSE = "WHERE siteactive != '0' AND (". $WHERE_CLAUSE . ")";
	}
	// Always hide inactive sites
	else {
		$WHERE_CLAUSE = "WHERE siteactive != '0'";
	}
	
		
	// Limit the number of websites if requested
	$LIMITCLAUSE = false;
	if ($count > 0) {
		$LIMITCLAUSE = 'LIMIT '.$count;
	}
	
			
	// Get website details, merge with group details
	$SQL = "SELECT * FROM $websites_table
			LEFT JOIN $groups_table ON $websites_table.sitegroup = $groups_table.groupid
			$WHERE_CLAUSE
		 	ORDER BY RAND()
		 	$LIMITCLAUSE
		 	";			
					
	$wpdb->show_errors();
	$websites = $wpdb->get_results($SQL, OBJECT);

	// If we've got websites to show, then render into HTML. Use blank group to avoid rendering group details.
	if ($websites) {
		$portfolioHTML = WPPortfolio_renderPortfolio($websites, $template_website, ' ', false, $isWidgetTemplate);
	} else {
		$portfolioHTML = false;
	}
	
	return $portfolioHTML;
}



/**
 * Convert the website details in the database object into the HTML for the portfolio.
 * 
 * @param Array $websites The list of websites as objects.
 * @param String $template_website The template used to render each website. If false, the website template defined in the settings is used instead.
 * @param String $template_group The template used to render each group header. If false, the group template defined in the settings is used instead.
 * @param String $paging_html The HTML used for paging the portfolio. False by default.
 * @param Boolean $isWidgetTemplate If true, then we're rendering this as a widget layout.
 * 
 * @return String The HTML for the portfolio page.
 */
function WPPortfolio_renderPortfolio($websites, $template_website = false, $template_group = false, $paging_html = false, $isWidgetTemplate = false)
{
	if (!$websites)
		return false;
			
	// Just put some space after other content before rendering portfolio.	
	$content = "\n\n";			

	// Used to track what group we're working with.
	$prev_group = "";
	
	// Get templates to use for rendering the website details. Use the defined options if the parameters are false.
	if (!$template_website) {
		$setting_template_website = stripslashes(get_option('WPPortfolio_setting_template_website'));
	} else {
		$setting_template_website = $template_website;		
	}

	if (!$template_group) {
		$setting_template_group = stripslashes(get_option('WPPortfolio_setting_template_group'));						
	} else {
		$setting_template_group = $template_group;	
			
	}
	 	
	
	// Render all the websites, but look after different groups
	foreach ($websites as $websitedetails)
	{
		// If we're rendering a new group, then show the group name and description 
		if ($prev_group != $websitedetails->groupname)
		{
			// Replace group name and description.					
			$renderedstr = WPPortfolio_replaceString(WPP_STR_GROUP_NAME, stripslashes($websitedetails->groupname), $setting_template_group);
			$renderedstr = WPPortfolio_replaceString(WPP_STR_GROUP_DESCRIPTION, stripslashes($websitedetails->groupdescription), $renderedstr);
			
			// Update content with templated group details
			$content .= "\n\n$renderedstr\n";
		}
		
		// Render the website details
		$renderedstr = WPPortfolio_replaceString(WPP_STR_WEBSITE_NAME, 		 	stripslashes($websitedetails->sitename), $setting_template_website);
		$renderedstr = WPPortfolio_replaceString(WPP_STR_WEBSITE_DESCRIPTION, 	stripslashes($websitedetails->sitedescription), $renderedstr);
		$renderedstr = WPPortfolio_replaceString(WPP_STR_WEBSITE_CUSTOM_FIELD, 	stripslashes($websitedetails->customfield), $renderedstr);
		
		
		// Remove website link if requested to
		if ($websitedetails->displaylink == 'hide_link')
		{		
			$renderedstr = preg_replace('/<a\shref="%WEBSITE_URL%"[^>]+>%WEBSITE_URL%<\/a>/i', '', $renderedstr);
		}
		
		$renderedstr = WPPortfolio_replaceString(WPP_STR_WEBSITE_URL, 		 	stripslashes($websitedetails->siteurl), $renderedstr);
		
				
		
		// Handle the thumbnails - use custom if provided.
		$imageURL = false;
		if ($websitedetails->customthumb) 
		{
			$imageURL = WPPortfolio_getAdjustedCustomThumbnail($websitedetails->customthumb);
			$imagetag = sprintf('<img src="%s" alt="%s"/>', $imageURL, stripslashes($websitedetails->sitename));
		} 
		// Standard thumbnail
		else {
			$imagetag = WPPortfolio_getThumbnailHTML($websitedetails->siteurl, false, ($websitedetails->specificpage == 1), stripslashes($websitedetails->sitename)); 			
		}
		$renderedstr = WPPortfolio_replaceString(WPP_STR_WEBSITE_THUMBNAIL_URL, $imageURL, $renderedstr); /// Just URLs		
		$renderedstr = WPPortfolio_replaceString(WPP_STR_WEBSITE_THUMBNAIL, $imagetag, $renderedstr);  // Full image tag
		
		// Handle any shortcodes that we have in the template
		$renderedstr = do_shortcode($renderedstr);
		
		
		$content .= "\n$renderedstr\n";
		
		// If fetching thumbnails, this might take a while. So flush.
		flush();
		
		// Track the groups
		$prev_group = $websitedetails->groupname;
	}
	
	$content .= $paging_html;
	
	// Credit link on portfolio. 
	if (!$isWidgetTemplate && get_option('WPPortfolio_setting_show_credit') == "on") {				
		$content .= sprintf('<div style="clear: both;"></div><div class="wpp-creditlink" style="font-size: 8pt; font-family: Verdana; float: right; clear: both;">'.__('Created using %s by the %s</div>', 'wp-portfolio'), '<a href="http://wordpress.org/extend/plugins/wp-portfolio" target="_blank">WP Portfolio</a>', '<a href="http://www.wpdoctors.co.uk/" target="_blank">WordPress Doctors</a>');
	} 
				
	// Add some space after the portfolio HTML 
	$content .= "\n\n";
	
	return $content;
}



/**
 * Create the cache directory if it doesn't exist.
 * $pathType If specified, the particular cache path to create. If false, use the path stored in the settings.
 */
function WPPortfolio_createCacheDirectory($pathType = false)
{
	// Cache directory
	$actualThumbPath = WPPortfolio_getThumbPathActualDir($pathType);
			
	// Create cache directory if it doesn't exist	
	if (!file_exists($actualThumbPath)) {
		@mkdir($actualThumbPath, 0777, true);		
	} else {
		// Try to make the directory writable
		@chmod($actualThumbPath, 0777);
	}
}

/**
 * Gets the full directory path for the thumbnail directory with a trailing slash.
 * @param $pathType The type of directory to fetch, or just return the one specified in the settings if false. 
 * @return String The full directory path for the thumbnail directory.
 */
function WPPortfolio_getThumbPathActualDir($pathType = false) 
{
	// If no path type is specified, then get the setting from the options table.
	if ($pathType == false) {
		$pathType = WPPortfolio_getCacheSetting();
	}
	
	switch ($pathType)
	{
		case 'wpcontent':
			return trailingslashit(trailingslashit(WP_CONTENT_DIR).WPP_THUMBNAIL_PATH);
			break;
			
		default:
			return trailingslashit(trailingslashit(WP_PLUGIN_DIR).WPP_THUMBNAIL_PATH);
			break;
	}	
}


/**
 * Gets the full URL path for the thumbnail directory with a trailing slash.
 * @param $pathType The type of directory to fetch, or just return the one specified in the settings if false.
 * @return String The full URL for the thumbnail directory.
 */
function WPPortfolio_getThumbPathURL($pathType = false) 
{
	// If no path type is specified, then get the setting from the options table.
	if ($pathType == false) {
		$pathType = WPPortfolio_getCacheSetting();
	}
	
	switch ($pathType)
	{
		case 'wpcontent':
			return trailingslashit(trailingslashit(WP_CONTENT_URL).WPP_THUMBNAIL_PATH);
			break;
			
		default:
			return trailingslashit(trailingslashit(WP_PLUGIN_URL).WPP_THUMBNAIL_PATH);
			break;
	}
}


/**
 * Get the type of cache that we need to use. Either 'wpcontent' or 'plugin'.
 * @return String The type of cache we need to use.
 */
function WPPortfolio_getCacheSetting()
{
	$cacheSetting = get_option(WPP_CACHE_SETTING);
	
	if ($cacheSetting == 'setting_cache_wpcontent') {
		return 'wpcontent';
	}
	return 'plugin';
}


/**
 * Get the full URL path of the pending thumbnails.
 * @return String The full URL path of the pending thumbnails.
 */
function WPPortfolio_getPendingThumbURLPath() {
	return trailingslashit(WP_PLUGIN_URL).WPP_THUMB_DEFAULTS;
}

/**
 * Shows either information or error message.
 */
function WPPortfolio_showMessage($message = false, $errormsg = false)
{
	if (!$message) {
		$message = __("Settings saved.", 'wp-portfolio');
	}
	
	if ($errormsg) {
		echo '<div id="message" class="error">';
	}
	else {
		echo '<div id="message" class="updated fade">';
	}

	echo "<p><strong>$message</strong></p></div>";
}

/**
 * Function: WPPortfolio_showRedirectionMessage();
 *
 * Shows settings saved and page being redirected message.
 */
function WPPortfolio_showRedirectionMessage($message, $target, $delay)
{
?>
	<div id="message" class="updated fade">
		<p>
			<strong><?php echo $message; ?><br /><br />
			<?php echo sprintf(__('Redirecting in %1$s seconds. Please click <a href="%2$s">here</a> if you do not wish to wait.', 'wp-portfolio'), $delay, $target); ?>
			</strong>
		</p>
	</div>
	
	<script type="text/javascript">
    <!--
            function getgoing() {
                     top.location="<?php echo $target; ?>";
            }

            if (top.frames.length==0) {
                setTimeout('getgoing()',<?php echo $delay * 1000 ?>);
            }
	//-->
	</script>
	<?php
}




/**
 * Get the details for the specified Website ID.
 * @param $siteid The ID of the Website to get the details for.
 * @return Array An array of the Website details.
 */
function WPPortfolio_getWebsiteDetails($siteid, $dataType = ARRAY_A) 
{
	global $wpdb;
	$table_name = $wpdb->prefix . TABLE_WEBSITES;
	
	$SQL = "SELECT * FROM $table_name 
			WHERE siteid = '".$wpdb->escape($siteid)."' LIMIT 1";

	// We need to strip slashes for each entry.
	if (ARRAY_A == $dataType) {
		return WPPortfolio_cleanSlashesFromArrayData($wpdb->get_row($SQL, $dataType));
	} else {
		return $wpdb->get_row($SQL, $dataType);
	}
}



/**
 * AJAX callback function that refreshes a thumbnail.
 */
function WPPortfolio_handleForcedThumbnailRefresh() 
{
	global $wpdb; 

	// Get the website ID
	$siteid = $_POST['siteid'] + 0;
	$websitedetails = WPPortfolio_getWebsiteDetails($siteid);
	
	// We should always get a valid website, but handle in case.
	if (!$websitedetails) {
		echo '';
		die();
	}

	// Delete existing thumbnails, then reload image 
	if ($websitedetails['customthumb']) 
	{
		WPPortfolio_removeCachedPhotos($websitedetails['customthumb']);
		$newImageURL = WPPortfolio_getAdjustedCustomThumbnail($websitedetails['customthumb'], 'sm');
	} 
	// Standard thumbnail
	else 
	{
		// Remove cached thumb and errors
		WPPortfolio_removeCachedPhotos($websitedetails['siteurl']);
		WPPortfolio_errors_removeCachedErrors($websitedetails['siteurl']);
		
		$newImageURL = WPPortfolio_getThumbnail($websitedetails['siteurl'], 'sm', ($websitedetails['specificpage'] == 1));
	}
	
	// Return the newly cached image
	echo $newImageURL;
	die();
}
add_action('wp_ajax_thumbnail_refresh', 'WPPortfolio_handleForcedThumbnailRefresh');


/**
 * Function that removes the physical cached files of the specified URL.
 * @param $fileurl The URL of the file that has been cached.
 */
function WPPortfolio_removeCachedPhotos($fileurl)
{
	$allCached = md5($fileurl).'*';
	$cacheDir = trailingslashit(WPPortfolio_getThumbPathActualDir());
	
	foreach (glob($cacheDir.$allCached) AS $filename) {
		unlink($filename);
	}
}


/**
 * Do we have a paid account?
 * @return Boolean True if we have a paid account, false otherwise.
 */
function WPPortfolio_isPaidAccount()
{
	$accountType = get_option('WPPortfolio_setting_stw_account_type');
	return ($accountType == 'paid');	
}


/**
 * Determine if there's a custom size option that's been selected.
 * @return The custom size, or false.
 */
function WPPortfolio_getCustomSizeOption()
{
	if (!WPPortfolio_isPaidAccount()) {
		return false;
    }

    // Do we want to use custom thumbnail types?
    if (get_option('WPPortfolio_setting_stw_thumb_size_type') != 'custom') {
    	return false;
    }
        
	return get_option('WPPortfolio_setting_stw_thumb_size_custom') + 0;
}


/**
 * Delete all error messages relating to this URL.
 * @param String $url The URL to purge from the error logs.
 */
function WPPortfolio_errors_removeCachedErrors($url)
{
	global $wpdb;
	$wpdb->show_errors;
				
	$table_debug = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
	$SQL = $wpdb->prepare("
		DELETE FROM $table_debug
		WHERE request_url = %s
		", $url);
	
	$wpdb->query($SQL);
}


/**
 * Function checks to see if there's been an error in the last 12 hours for
 * the requested thumbnail. If there has, then return the error associated
 * with that fetch.
 * 
 * @param Array $args The arguments used to fetch the thumbnail
 * @param String $pendingThumbPath The path for images when a thumbnail cannot be loaded. 
 * @return String The URL to the error image, or false if there's no cached error.
 */
function WPPortfolio_errors_checkForCachedError($args, $pendingThumbPath)
{
	global $wpdb;
	$wpdb->show_errors;
		
	$argHash = md5(serialize($args));
		
	$table_debug    = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
	$SQL = $wpdb->prepare("
		SELECT * 
		FROM $table_debug
		WHERE request_param_hash = %s
		  AND request_date > NOW() - INTERVAL 12 HOUR
		  AND request_result = 0
		ORDER BY request_date DESC
		", $argHash);
	
	$errorCache = $wpdb->get_row($SQL);
	
	if ($errorCache)  {
		return WPPortfolio_error_getErrorStatusImg($args, $pendingThumbPath, $errorCache->request_error_msg);
	}
	
	return false;
}

/**
 * Get a total count of the errors currently logged.
 */
function WPPortfolio_errors_getErrorCount()
{
	global $wpdb;
	$wpdb->show_errors;
	$table_debug    = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
	
	return $wpdb->get_var("SELECT COUNT(*) FROM $table_debug WHERE request_result = 0");
}

/**
 * Adds a link to the plugin page to click through straight to the plugin page.
 */
function WPPortfolio_plugin_addSettingsLink($links) 
{ 
	$settings_link = sprintf('<a href="%s">Settings</a>', admin_url('admin.php?page=WPP_show_settings')); 
	array_unshift($links, $settings_link); 
	return $links; 
}

?>