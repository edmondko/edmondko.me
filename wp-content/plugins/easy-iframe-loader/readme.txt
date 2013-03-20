=== Plugin Name ===
Contributors: andykillen
Donate link: http://phat-reaction.com/wordpress-plugins/easy-iframe-loader/donations/
Tags: iframe, javascript, late load, shortcode, widget, template, tag, speed, page, post, youtube, vimeo, amazon, ipad, iphone, ipod
Requires at least: 2.8.6
Tested up to: 3.3.1
Stable tag: 1.4.0
 
Adds a shortcode/widget/template tag to manage iFrame loading, uses javascript for late loading to increase page speed. Additional iframes support for Youtube, Vimeo and Amazon.

== Description ==

Simple plugin that uses shortcodes, widget or template tags to late load iFrames using javascript.  This gets over a number problems.

* loading of iframe does not happen until after the complete page has loaded by using the window.onload command, thus making page loading quicker. When an iFrame is used directly in the HTML it wants to load first stopping the rest of the page loading.
* Gets over the automatic deletion of iFrame info from the editor when the user changes between Visual and HTML mode.
* Makes Vimeo and YouTube videos load on iPad, iPhone and iPod
* Better than the inbuilt shortcodes from Wordpress as it loads an iFrame and not an embed.  iFrames give the owner chance to manage what content is delivered. So this means Vimeo and YouTube can offer a Quicktime video to iPad/iPhone, and Flash to other devices
* You don't need to remember the best size for an iframe on your site as you can set it once and it will be the same time and time again. Seperate settings for : A Store from Amazon, Vimeo Videos, YouTube Videos, Widgets and Basic iFrames
* Hooks in to the Share and Follow plugin (by myself!) to provide an image_src so that video images from Vimeo and YouTube both offer a thumbnail in the newsfeed for Facebook, Linkedin and others

= Comes in the following formats =
1. shortcodes for content creators
1. widget for administrators
1. template tags for developers

Here's a link to [Easy iFrame Loader](http://phat-reaction.com/wordpress-plugins/easy-iframe-loader/ "Full Instructions") Support Page

Do make sure that you donate when using this plugin, as nobody can live on fresh air alone and I am expecting a baby boy in May (13th) and need some money for diapers/nappies or my girlfriend will kill me!  Or buy me a beer and she will still tell me off ;D, but I will be saving you loads of hassel.

Here's a link to [Easy iFrame Loader Donations](http://phat-reaction.com/wordpress-plugins/easy-iframe-loader/donations/ "Donations") Page

== Installation ==

There are 2 ways to install the Easy iFrame Loader

*Using the Wordpress Admin screen*

1. Click Plugins, Add New
1. Search for Easy iFrame Loader by Andy Killen
1. Install and Activate it
1. Place '[iframe_loader src=""]' in your pages or posts, use the widget or tempate tag

*Using FTP*

1. Upload 'easy-iframe-loader' to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place '[iframe_loader src=""]' in your pages or posts, use the widget or tempate tag

== Frequently Asked Questions ==
*Working with the Shortcodes*
It is possible to setup default for the shortcodes in the admin screen (Settings --> Easy iFrame)

= iframe_loader... the default iframe technique  =
The default settings are

`[iframe_loader width='100%' height='150'  frameborder = '0'  longdesc=' ' marginheight='0'  marginwidth='0' name=' ' click_words=' ' click_url=' '  scrolling='auto'   src=' ' class=' ' ]`

= Can I have an example of it loading a vimeo video? =

`[iframe_vimeo video="video_id"]`

the video id is the numbers at the end of a video URL.  i.e. http://vimeo.com/*16185599*

= example of a YouTube video example =

`[iframe_youtube video="video_id"]`

The video id is the numbers and letters that come after v= i.e. http://www.youtube.com/watch?v=*Ntc4l-poovo*

= example of an A Store from Amazon =

`[a_store src="URL to store"]`

You will find this information on the amazon site when you setup your store.  The default width and height are *90%* and *4000* respectivly
 example url *http://astore.amazon.com/wwwphatreacti-20*

= example of a buy now button from amazon =

`[buy_amazon src="Amazon URL provided by Amazon with affiliate id"]`
example url *http://rcm-uk.amazon.co.uk/e/cm?lt1=_blank&bc1=3B5998&IS2=1&bg1=FFFFFF&fc1=000000&lc1=0000FF&t=phatreaction-21&o=2&p=8&l=as1&m=amazon&f=ifr&md=0M5A6TN3AXP2JHJBWT02&asins=B00009QI1U*

= what about an example showing a google map? =

`[iframe_loader width='425' height='350'  click_words='View Larger Map' click_url='http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Dam,+Amsterdam,+Nederland&amp;sll=37.0625,-95.677068&amp;sspn=50.777825,117.509766&amp;ie=UTF8&amp;hq=&amp;hnear=Dam,+Amsterdam,+The+Netherlands&amp;z=14&amp;ll=52.372738,4.892738' src='http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Dam,+Amsterdam,+Nederland&amp;sll=37.0625,-95.677068&amp;sspn=50.777825,117.509766&amp;ie=UTF8&amp;hq=&amp;hnear=Dam,+Amsterdam,+The+Netherlands&amp;z=14&amp;ll=52.372738,4.892738&amp;output=embed']`

*Working with the Widget*
= How do I use the widget? =

Just drag and drop the widget accross into the wanted dynamtic sidebar.  (Apperance --> Widgets).  The only necessary item is the URL of the iframe, all other items are pre-set, so it will still work.  It is better though to spend the time to fill in all the items.

It is possible to setup default for the widget in the admin screen (Settings --> Easy iFrame)

*Working with the Template taga*
= How does the template tag work? =
inside a php file add

= add_iframe_late_load($args) =
*minimum settings*
` <?php $args = array ('src'=>'URL-of-iFrame', 'height'=>'wanted-height-in-px')
add_iframe_late_load($args) ?> `

All options
`$args = array('height' => "150",'width' => "100%",'frameborder' => '0','scrolling'=>'auto', 'src'=>'',
            'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'');`


= add_iframe_a_store($src, $width='', $height='', $class='') =
If no height or width is set it will default back to the ones in the admin page. $src is vital!

= add_iframe_amazon_buy($src, $class='') =
needs the $src of the iframe only, height and width never change

= add_iframe_youtube($video, $click_words='', $click_url='', $class='') =
Just needs the alpha-numeric video id only, optional click URL and click words.  Height and Width are from admin page.  If you want different settings use the add_iframe_late_load($args)

= add_iframe_vimeo($video, $click_words='', $click_url='', $class='') =
   Just needs the numeric video id only, optional click URL and click words.  Height and Width are from admin page.  If you want different settings use the add_iframe_late_load($args)

== Screenshots ==

1. the basic admin screen to enable standard settings of the various shortcodes.
2. Vimeo and YouTube videos working on an iPad.

== Changelog ==
= 1.4.0 =

fixed problem with IE not loading properly


= 1.3.1 =

changed activation class call which got lost by SVN

= 1.3 =

added a 'class' option to the plugin so that iframe styling can be controlled via CSS

= 1.2 =

removed CDATA sections as wordpress screws with them

= 1.1 =
more shortcodes, better OSX support

= 1.0 =
First version

== Upgrade Notice ==

= 1.4.0 =

fixed problem with IE not loading properly

= 1.3.1 =

changed activation class call which got lost by SVN

= 1.3 =

added a 'class' option to the plugin so that iframe styling can be controlled via CSS

= 1.2 =

removed CDATA sections as wordpress screws with them

= 1.1 =
more shortcodes, better OSX support

= 1.0 =
First version, no upgrades
