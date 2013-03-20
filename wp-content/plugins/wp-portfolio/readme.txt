=== Wordpress Portfolio Plugin (WP Portfolio) ===
Contributors: DanHarrison 
Donate link: http://www.wpdoctors.co.uk/our-wordpress-plugins/wp-portfolio/
Tags: portfolio, thumbnails, plugins, web designer, websites
Requires at least: 2.9
Tested up to: 3.5
Stable tag: 1.32

	
A plugin that allows you to quickly and easily show off your portfolio of websites on your wordpress blog with automatically generated thumbnails. 


== Description ==

A plugin that allows you to show off your website portfolio through a single page on your wordpress blog with automatically generated thumbnails. To show 
your portfolio, create a new page and paste `[wp-portfolio]` into it.

**Features Include:**

* Automatically generated website thumbnails (to save you hassle)
* Ability to fully customise the layout (through HTML and CSS) if required.
* The option to use a custom image rather than a screenshot (e.g. for showing off graphic design work).
* Support for grouping entries to categorise your work.
* and more!

The plugin requires you to have a free (or paid) account with [Shrink The Web](http://www.shrinktheweb.com/) if you wish to generate the thumbnails **dynamically**. Please read [the first FAQ about account types](http://wordpress.org/extend/plugins/wp-portfolio/faq/) to learn more.  

However, you do not need an account with ShrinkTheWeb to use this plugin if you capture screenshots of your websites yourself. Just can capture your own screenshots as images, upload those images to your website, and then link to them in the `Custom Thumbnail URL` field.

This plugin also requires PHP5 or above.


= About the Author =
Dan Harrison is a blogging fanatic, who has been running Wordpress on all of his websites for years. Dan is a [UK WordPress Developer](http://www.wpdoctors.co.uk), and runs a web development agency, specialising in Wordpress development and design, such as creating new Wordpress plugins and templates. 


= Problems and Support =
Please check the [frequently asked questions](http://wordpress.org/extend/plugins/wp-portfolio/faq/) page if you have any issues. As a last resort, 
please raise a problem in the [WP Portfolio Support Forum on Wordpress.org](http://wordpress.org/tags/wp-portfolio?forum_id=10), and I'll respond to the ticket as soon as 
possible. Please be aware, this might be a couple of days. 


= Comments and Feedback =
If you have any comments, ideas or any other feedback on this plugin, please leave comments on the [WP Portfolio Support Forum on Wordpress.org](http://wordpress.org/tags/wp-portfolio?forum_id=10).



= Requesting Features =

I offer a **feature for a fee policy**. This means if you wish to have a new feature added, you can pay a small fee, and we'll add that particular feature to the plugin. Any features that are paid for
are shared with the community. This helps to cover our costs of developing and supporting the plugin.

You can pay for requested features on the [WP Portfolio Fee for Features page](http://www.wpdoctors.co.uk/our-wordpress-plugins/wp-portfolio/#feature-requests). Payment are handed by Paypal for simplicity. If you are prepared to wait rather than pay for a new feature, I do welcome feature ideas, which can be left on the [WP Portfolio Support Forum on Wordpress.org](http://wordpress.org/tags/wp-portfolio?forum_id=10). 	





This plugin is licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).

	
== Installation ==

* 1) Extract the zip file and just drop the contents in the `wp-content/plugins/` directory of your WordPress installation 
* 2) Activate the plugin from Plugins page.
* 3) Edit a page that you want your portfolio to appear on and paste `[wp-portfolio]`  into it.
* 4) Add your websites in WP Portfolio within your Wordpress admin area.


== Screenshots ==

1. A screenshot of the admin view of the thumbnails that have been automatically generated.
2. The form for adding a new website.

== Changelog ==

= 1.32 =
* Removed intermediate verification page that was being shown for free acounts, as STW has removed it again. Great news!

= 1.31 =
* Added support for transparent GIFs and PNGs
* Added new setting to force creating tables, due to niggles that some people get when upgrading.

= 1.30 =
* Added ability to display single thumbnails instead of only entire groups.
* Added initial multi-language support.
* Added feature to show or hide the URL of the thumbnailed site.

= 1.29 =
* Thumbnails were staying stuck as 'queued', due to a change in STW's API. So updated fetch code to match new API.
* Added new code to disable adding pagepix.js to header of site frontend.

= 1.28 =
* Found and fixed an issue that if debug logging was enabled, thumbnails would show once, and then fail. 

= 1.27 =
* Intermediate fix made to code due to issue with table upgrades. If you're getting database errors, just deactivate and activate the plugin to fix. 

= 1.26 = 
* Added fix to ensure edit links are don't wrap in portfolio summary in admin area.
* Added new error message if you try to use 'Inside Pages' without the the right account.
* Made small tweak to pagepix.js that was causing intermittant load errors for free users.
* Added error caching to prevent you using up your allowance with STW if you've got persistant issues (paid accounts only).

= 1.25 =
* Massive improvements to error reporting. Rather than just showing 'Thumbnail Queued', a more instructive message is shown.

= 1.24 =
* Fixed the broken thumbnails on the frontend of the website that I broke by accident. Sorry! :)

= 1.23 =
* Improved methods of loading scripts to avoid them impacting other plugins.
* Added a field that allows you to edit the date a website was added.

= 1.22 =
* Fixed CSS conflict in admin area, which prevented the advanced area from being shown.
* Updated the FAQ with an issue that's frequently asked above.

= 1.21 =
* Fixed bug with thumbnailer overwriting alt tags for images.
* Improvements to FAQ in documentation, to reflect changes with STW.
* Catching up with the latest STW account changes, so there are numerous documentation updates.
* Fixed issue with settings being overwritten when the plugin is updated, if the existing setting is blank. (thanks to MACscr for spotting it).

= 1.20 =
* Added ability to change the paging template.
* Added 'Previous' link in paging.
* Added more detailed debug logging to help diagnose issues with loading thumbnails.
* Added support for ShrinkTheWeb's new paid mode with additional security.
* Shortcodes now work within any of the fields or template of any website.
* Paid STW Accounts Only - Now supporting ShrinkTheWeb's custom image size.

= 1.19 =
* Added custom field option to WP Portfolio
* Cleaned up summary of websites in admin area.
* Added server compatibility checker
* Added forced thumbnail refresh from the website summary page.
* Added ability to change the cache location.

= 1.18 =
* Added ability to duplicate a website.
* Set default codepage for table (for new installation) to UTF-8
* Added setting to allow existing users to upgrade to UTF-8

= 1.17 =
* Fixed bug with the date being wrong for when a website is added.
* Added option to allow WP Portfolio CSS to be switched off without clearing CSS styles.
* Updated FAQ to address a known issue with certain installations.

= 1.16 =
* Fixed bug where debug table wasn't being created.
* Changed menu access level to use the 'manage_options' setting, rather than the deprecated use of a user level number.
* Fixed bug where errors reported when installing the plugin.
* Fixed minor issue when saving website order.
* Added ability to show websites by the date that they were added. e.g. **`[wp-portfolio ordertype="dateadded" orderby="desc" /]`**
* Added a new template tag to get just the thumbnail URL (**`%WEBSITE_THUMBNAIL_URL%`**), rather than a full image HTML tag (**`%WEBSITE_THUMBNAIL%`**).
* Added option to change how custom thumbnails are resized based on style requirements (match only width of custom thumbnails, match only height of website thumbnails or ensure website thumbnail is never larger than other website thumbnails).

= 1.15 =
* Added support for ShrinkTheWeb.com's new CDN and API.

= 1.14 =
* Added support for internal pages using Shrink The Web's paid-for feature for showing specific pages.
* Updated documentation to mention new website.
* Removed old style tag upgrader code.
* Added debug option that logs requests to help locate problems.

= 1.13 =
* Added paging option for showing X number of websites per page.

= 1.12 =
* Added support for website ordering.
* Added image alt tags by default to templates.
* Fixed bug to show websites by default when adding a new website to the portfolio.

= 1.11 =
* Fixed bug with adding a website with a missing description. Thanks to Adam Coulthard for finding the issue.
* Ensured compliance with Wordpress 2.9 specification.
* Added `target="_blank"` for the links in the author credit link at the bottom of any rendered portfolio. 
* Added ability to hide/show a given website without having to remove it.


= 1.10 =
* Added ability for cached thumbnails to never expire.
* Added custom thumbnails so that you can override the screenshot with your own image, such as custom graphics and photos. Custom thumbnails are automatically resized to match other thumbnails.
* Added a timeout of 10 seconds for loading thumbnail images so that pages do eventually load.
* Added new option `[wp-portfolio hidegroupinfo="1"]` so that you can hide group descriptions on only certain pages or posts.

= 1.09 =
* Changed the code that shows the portfolio to `[wp-portfolio]` to improve performance, reduce errors and to allow for new functionality.
* Added a tool to automatically upgrade the old style tags to the new style.

= 1.08 =
* Added the ability to render the portfolio from within your theme files in PHP. 
* Added PHP code to allow you to create a random selection of your portfolio from PHP.
* Moved all documentation into a single documentation page.

= 1.07 =
* Removed a debug message 
* Added silent error handling for creating cache directory

= 1.06 =
* Fixed the broken regular expression to allow the original method of showing all websites. 

= 1.05 =
* Added feature to portfolio admin section that allows to only show websites within a certain group.
* Massive cleanup of code for admin area to reduce errors and allow future features more easily.
* Added the much requested selective rending of groups. This means you can choose which groups of websites you show on any page.

= 1.04 =
* Fixed issue where default thumbnails were not showing when thumbnail is not available.

= 1.03 =
* Fixed an issue where saving the template CSS over-writes the group template code.

= 1.02 =
* Added option for using cURL rather than fopen for fetching thumbnails to handle strict server security settings.
* Moved formatting options for portfolio into separate settings section.
* Created option to enable/disable credit link back to my website.
* Now handles lack of `str_ireplace` function if using PHP4.
* Added button to empty the thumbnail cache.


= 1.01 =
* Removed test.css from header when CSS is rendered on page.

= 1.00 =
* Initial Release



== Frequently Asked Questions ==

= Troubleshooting = 


**What are the different ShrinkTheWeb account types?**

See the [different account types from ShrinkTheWeb](http://www.shrinktheweb.com/content/compare-thumbnail-offerings.html). 

However, you do not need an account with ShrinkTheWeb to use this plugin if you capture screenshots of your websites yourself. Just can capture your own screenshots as images, upload those images to your website, and then link to them in the `Custom Thumbnail URL` field.


**I've got a thumbnail error, I've fixed the issue, but the error is still there. Why?**

To prevent using up your allowance at STW, we've added error caching. To retry loading a thumbnail, either click on the **Refresh** link or clear all of the **Error Logs** in the WP Portfolio admin area.



**When should I use the `Lock to Account` feature in STW?**

Lock to Account is a feature in STW that ensures only you can use your account to generate thumbnails. It's essentially extra security. To use the locking feature, you need to go to `My Account` in STW, then `Security`. The section marked `Lock to account` is what you need to take a look at.

* If you have a free account with STW, then you'll need to associate your website with your account.
* If you have a paid (basic or plus) account with STW, then locking your account to certain domains is optional. If you do lock your account, you may need to specify the IP address of your server if you domain name doesn't work.


**Why are my thumbnails not showing up straight away?**

The Shrink The Web (STW) servers do not create thumbnails straight away once they are requested. It typically takes up to 2 minutes for the thumbnail to be created and made available.



**How do I force the thumbnail to be re-captured?**

You need to visit the STW website and request it. 



**My thumbnails are not showing up? Help!**

There could be a number of reasons why the thumbnail files are not being downloaded. However, here's a list of things to check.

* Ensure you've correctly set the `STW Access Key ID` and `STW Secret Key` options in the `Portfolio Settings`.

* Ensure you've selected the correct STW account type in the `Portfolio Settings`.

* Ensure the cache directory exists. Although the plugin tries to create the cache directory, some server set-ups don't let it work. So create the cache 
directory with permissions 0777 as `/wp-content/plugins/wp-portfolio/cache/`.

* Check that you've added some websites to your portfolio.

* Check that you have `[wp-portfolio]` in one of your pages. You can specify specific groups later, you just want to check that all of the websites are shown.

* Ensure that the `Website HTML Template`, `Group HTML Template` and `Template CSS` fields in `Layout Settings`  contain something. If they don't, you can 
copy the default templates from lower down that page.

* Check that your web host is using PHP5 and not the outdated PHP4 (that information can be found in the `Server Compatibility Checker` on the `Portfolio Settings` page).

* STW changed their accounts system on 23rd October 2011 and required everyone to re-validate their email addresses. If you didn't confirm your email address, your account might have been accidently banned.


**Why are my custom thumbnails not showing up?**

* The most likely reason is that the URL for the image is incorrect. Copy and paste the image URL into your web browser, and make sure you see the image correctly. If you don't see the image correctly, then there's no way that the plugin can load the image correctly. 

* The other likely cause is that cache directory does not exist (see above), 


**I get the following error, what's going on? (1)**

`Parse error: syntax error, unexpected T_STRING, expecting T_OLD_FUNCTION or
T_FUNCTION or T_VAR or '}' in
/home/path/to/wordpress/wp-content/plugins/wp-portfolio/wplib/utils_formbuilder.inc.php
on line 30`

WP Portfolio only supports PHP5, not PHP4. The error above is due to `function class_exists()` only existing in PHP5 and not PHP4. 

Most web hosting companies have the old PHP4 switched on by default. Just ask them to change your hosting account to PHP5. Some hosting accounts allow you to 
do this yourself from within your hosting control panel.


**I get the following error, what's going on? (2)**

`Fatal error: Call to undefined function wp_get_current_user() in ...\wp-includes\capabilities.php on line 1059`

Some plugins seem to force the include of files in strange orders. Often these are plugins relating to access control or users. To fix this, edit `wp-portfolio.php` and add the following bit of code at the very top. `require_once ABSPATH .'/wp-includes/pluggable.php';`.

I've not added this to WP Portfolio in the main code, simply because it might break other plugins. It shouldn't be necessary to add this line at all, because WP Portfolio doesn't do anything particularly exotic in the code.


**I get the following error, what's going on? (3)**
`WordPress database error: [Unknown column 'customfield' in 'field list']
INSERT INTO wp_WPPortfolio_websites (sitename, siteurl, sitedescription, sitegroup, customthumb, siteactive, siteorder, specificpage, customfield, siteadded) VALUES ('example', 'http://www.example.com', 'Example', '1', '', '1', '0', '0', '', '2012-01-05 20:12:06')`

This often occurs when the plugin is upgraded without being deactivated and activated. So to fix this, just deactivate, and then activate the plugin. 


**When trying to show the portfolio, I get an error about a missing column. What do I do?**

This is usually due to the plugin tables not being created properly. In **Portfolio Settings**, click on the **Force Table Upgrade** button.


**When looking at the setings page, I just get a blank page or errors.**

This has been encountered when an `open_basedir restriction in effect` security restriction is in place, typically for those with plesk-based hosting. It's probably justified for standard users as it prevents them from accessing unwanted dirs. However, it may need to be turned off by people who want to do more with their website.
The key for all those interested is to turn off "open_basedir restriction" in their Plesk hosting account. Speaking to your hosting company if you need help with this issue.



= Features and Support =

**Does WP Portfolio support paging?**

Yes it does. To show 3 websites per page, use `[wp-portfolio sitesperpage="3"]`, or to show all websites, just use `[wp-portfolio]` as normal. Check the documentation for full usage details.

**What is the WP Portfolio group syntax?**

* To show all groups, use `[wp-portfolio]`
* To show just the group with an ID of 1, use `[wp-portfolio groups="1"]`
* To show groups with IDs of 1, 2 and 4, use `[wp-portfolio groups="1,2,4"]`

Please note that the order of the group numbers e.g. "1,2,3" does not indicate the order in which they are shown. The order of IDs in the brackets are in fact 
ignored. The order of the groups is determined by the order value for each group in the admin area.

** What is the WP Portfolio syntax to show one thumbnail at a time? **	
* To show just one website thumbnail, use `[wp-portfolio single="1"]`. The number is the ID of the website, which can be found on the WP Portfolio summary page.
* To show a specific selection of thumbnails, use their IDs like so: `[wp-portfolio single="1,2"]`

**Does WP Portfolio support shortcodes**

Yes it does. Shortcodes can go anywhere in the website template or website details.


**In the settings, you can only use three sizes of thumbnails; Small (120 x 90), Large (200 x 150) and Extra Large (240 x 340). Is it possible to get custom 
image size of 550 x 227?**

* Free STW Accounts - No, unfortunately not you can only use the standard ShrinkTheWeb thumbnail sizes.
* Paid (Basic and Plus) STW Accounts - Yes you can, if you have that feature selected in your account.


= Usage =

**How do I hide the category title and description on the portfolio page?**

Go to `Layout Settings` in the WP Portfolio admin section. Change the value of `Group HTML Template` to `&nbsp;` and save your settings. That will remove the 
category details from any page showing your portfolio of websites.

