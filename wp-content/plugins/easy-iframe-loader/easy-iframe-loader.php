<?php
/* 
Plugin Name: Easy iFrame Loader
Plugin URI: http://phat-reaction.com/wordpress-plugins/easy-iframe-loader
Version: 1.4.0
Author: Andy Killen
Author URI: http://phat-reaction.com
Description: Simple plugin to handle iframe late loading from a shortcode, template tag or widget
Copyright 2010 Andy Killen  (email : andy  [a t ] phat hyphen reaction DOT com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
              
if (!class_exists("iframeLoader")) {
class iframeLoader {
    var $adminOptionsName = "iframeLoaderAdminOptions";
        //
        // class constructor
        //
        function iframeLoader() {
        }
        function init() {
                $this->getAdminOptions();
        }

        function activate() {
                $this->getAdminOptions();
        }

         public function iframe_cache_manager($option_name = iframeLoader::adminOptionsName){
                    $value = get_transient( $option_name  );
                    if(false === $value){
                        $value = get_option( $option_name );

                        set_transient( $option_name, $value, 60*60*24 );
                    }

                return $value;
            }


        function iframe_defaults(){
             $iframeAdminOptions = array(
                    'youtube_height'=>'345', 'youtube_width'=>'560',
                    'vimeo_height'=>'315', 'vimeo_width'=>'560',
                    'amazon_width'=>'90%','amazon_height'=>'4000',
                    'basic_height'=>'250', 'basic_width'=>'100%',
                    'widget_height'=>'250', 'widget_width'=>'100%',);
             return $iframeAdminOptions;
        }

        function getAdminOptions() {
               $iframeAdminOptions = $this->iframe_defaults();
                $devOptions = get_option("iframeLoaderAdminOptions");
                if (!empty($devOptions)) {
                        foreach ($devOptions as $key => $option)
                                $iframeAdminOptions[$key] = $option;
                }
                update_option("iframeLoaderAdminOptions", $iframeAdminOptions);
                return $iframeAdminOptions;
        }

        function loadLangauge ()
        {
            $plugin_dir = basename(dirname(__FILE__));
            load_plugin_textdomain( 'easy-iframe-loader', null, $plugin_dir );
        }

        function do_iframe_script($args){
                $options = get_option("iframeLoaderAdminOptions");
                $defaults = array('height' => $options['basic_height'],'width' => $options['basic_width'],'frameborder' => '0','scrolling'=>'auto', 'src'=>'',
                'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'', 'class'=>'', 'title'=>'','youtube'=>'','vimeo'=>'');
                $args = wp_parse_args( $args, $defaults );
                extract( $args, EXTR_SKIP );
                $html = "<script type='text/javascript'>  \n";
              //  $html .= "//<![CDATA[ \n";
                $html .="window.onload = document.write(\"";
                $html .="<iframe width='".$width."' height='".$height."' marginwidth='".$marginwidth."' marginheight='".$marginheight."' scrolling='".$scrolling."' frameborder='".$frameborder."' ";
                 if (!empty ($name)){ $html .= " name='".$name."' ";}
                 
                 if (!empty ($longdesc)){ $html .= " longdesc='".$longdesc."' ";}
                 if (!empty ($class)){ $html .= " class='".$class."' ";}
                 if (!empty ($title)){ $html .= " title='".$title."' ";}
                 if (!empty ($src)){ $html .= " src='".$src."' ";}
                 if (!empty ($youtube)){ $html .= " src='http://www.youtube.com/embed/".$youtube."?rel=0' ";}
                 if (!empty ($vimeo)){ $html .= " src='http://player.vimeo.com/video/".$vimeo."' ";}
                 $html .= "></iframe> \"); \n ";
                 if (!empty($click_words) && !empty($click_url)){$html .="window.onload = document.write(\"<br /><a href='".$click_url."'>".$click_words."</a><br/>\"); \n";}
               //  $html .= "//]]> \n";
                 $html .= "</script>";
                 return $html;
        }

        function late_iframe_loader($atts){
               $options = get_option("iframeLoaderAdminOptions");
               extract(shortcode_atts(array(
                    'height' => $options['basic_height'],'width' => $options['basic_width'],'frameborder' => '0','scrolling'=>'auto', 'src'=>'',
                    'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'', 'class'=>'',
               ), $atts));
                $args = array('height' => $height,'width' => $width,'frameborder' => $frameborder,
                    'longdesc'=>$longdesc,'marginheight'=>$marginheight,'marginwidth'=>$marginwidth, 'name'=>$name,'click_words'=>$click_words,'click_url'=>$click_url,
                    'scrolling'=>$scrolling, 'src'=>$src, 'class'=>$class);
                $html = $this->do_iframe_script($args);
                return $html;
        }

        function a_store_loader($atts){
            $options =get_option("iframeLoaderAdminOptions");
               extract(shortcode_atts(array(
                    'height' => $options['amazon_height'],'width' => $options['amazon_width'],'frameborder' => '0','scrolling'=>'no', 'src'=>'',
                    'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'', 'class'=>'',
               ), $atts));
                $args = array('height' => $height,'width' => $width,'frameborder' => $frameborder,
                    'longdesc'=>$longdesc,'marginheight'=>$marginheight,'marginwidth'=>$marginwidth, 'name'=>$name,'click_words'=>$click_words,'click_url'=>$click_url,
                    'scrolling'=>$scrolling, 'src'=>$src, 'class'=>$class);
                $html = $this->do_iframe_script($args);
                return $html;
        }

        function amazon_buy_loader($atts){
               extract(shortcode_atts(array(
                    'height' => "240",'width' => "120",'frameborder' => '0','scrolling'=>'no', 'src'=>'',
                    'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'','class'=>'',), $atts));
                $args = array('height' => $height,'width' => $width,'frameborder' => $frameborder,
                    'longdesc'=>$longdesc,'marginheight'=>$marginheight,'marginwidth'=>$marginwidth, 'name'=>$name,'click_words'=>$click_words,'click_url'=>$click_url,
                    'scrolling'=>$scrolling, 'src'=>$src,'class'=>$class);
                $html = $this->do_iframe_script($args);
                return $html;
        }

        function youtube_iframe_loader($atts){
             $options =get_option("iframeLoaderAdminOptions");
               extract(shortcode_atts(array(
                    'height' => $options['youtube_height'],'width' => $options['youtube_width'],'frameborder' => '0','scrolling'=>'no', 'src'=>'',
                    'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'','video'=>'','class'=>'',
               ), $atts));
                $args = array('height' => $height,'width' => $width,'frameborder' => $frameborder,
                    'longdesc'=>$longdesc,'marginheight'=>$marginheight,'marginwidth'=>$marginwidth, 'name'=>$name,'click_words'=>$click_words,'click_url'=>$click_url,
                    'scrolling'=>$scrolling, 'src'=>$src, 'youtube'=>$video,'class'=>$class);
                $html = $this->do_iframe_script($args);

                $image_src = get_post_meta(get_the_ID(), 'image_src', true);
                if (empty($image_src)){
                 if (!empty($video)){
                    $url = "http://img.youtube.com/vi/".$video."/0.jpg";
                    add_post_meta(get_the_ID(), 'image_src', $url, true);
                 }
            }
                
                return $html;
        }
        function youtube_share_image($page_ID){
            
        }

        function vimeo_iframe_loader($atts){
             $options =get_option("iframeLoaderAdminOptions");
               extract(shortcode_atts(array(
                    'height' => $options['vimeo_height'],'width' => $options['vimeo_width'],'frameborder' => '0','scrolling'=>'no', 'src'=>'',
                    'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>'','video'=>'','class'=>'',
               ), $atts));
                $args = array('height' => $height,'width' => $width,'frameborder' => $frameborder,
                    'longdesc'=>$longdesc,'marginheight'=>$marginheight,'marginwidth'=>$marginwidth, 'name'=>$name,'click_words'=>$click_words,'click_url'=>$click_url,
                    'scrolling'=>$scrolling, 'src'=>$src, 'vimeo'=>$video,'class'=>$class);
                $html = $this->do_iframe_script($args);
                $image_src = get_post_meta(get_the_ID(), 'image_src', true);
                if (empty($image_src)){
                 if (!empty($video)){
                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$video.".php"));
                    $url = $hash[0][thumbnail_large];
                    add_post_meta(get_the_ID(), 'image_src', $url, true);
                 }
            }
                return $html;
        }

        function vimeo_share_image($page_ID, $vimeo){
           
        }

        function printAdminPage() {
            require_once('admin-page.php');
        }//End function printAdminPage()


        function load_widgets() {
            register_widget( 'iFrame_Widget' );
        }

    }
}
require_once('iframe-widget.php');   //  includes the code for the iframe widget
//  setup new instance of plugin
if (class_exists("iframeLoader")) {$cons_iframeLoader = new iframeLoader();}
//Actions and Filters	
if (isset($cons_iframeLoader)) {
//Actions
// setup widgets
//Initialize the admin panel
        if (!function_exists("iframeLoader_ap")) {
	function iframeloader_ap() {
		global $cons_iframeLoader;
		if (!isset($cons_iframeLoader)) {
			return;
		}
		if (function_exists('add_options_page')) {
                    add_options_page('Easy iFrames', 'Easy iFrames', 'manage_options', basename(__FILE__), array(&$cons_iframeLoader, 'printAdminPage'));
		}
	}
}

add_action('admin_menu', 'iframeLoader_ap',1); //admin page
add_action('widgets_init',array(&$cons_iframeLoader, 'load_widgets'),1); // loads widgets
add_action ('init',array(&$cons_iframeLoader, 'loadLangauge'),1);  // add languages
add_shortcode('iframe_loader', array(&$cons_iframeLoader,'late_iframe_loader'),1); // setup shortcode [iframe_loader] basic
add_shortcode('a_store', array(&$cons_iframeLoader,'a_store_loader'),1); // setup shortcode [a_store] amazon store
add_shortcode('amazon_buy', array(&$cons_iframeLoader,'amazon_buy_loader'),1); // setup shortcode [amazon_buy] amazon buy button with image
add_shortcode('iframe_youtube', array(&$cons_iframeLoader,'youtube_iframe_loader'),1); // setup shortcode [iframe_youtube] youtube videos
add_shortcode('iframe_vimeo', array(&$cons_iframeLoader,'vimeo_iframe_loader'),1); // setup shortcode [iframe_youtube] youtube videos
register_activation_hook( __FILE__, array(&$cons_iframeLoader, 'activate') );
}
//
// tempate tags
//
// LATE LOAD IFRAME BASIC
function add_iframe_late_load($args){
   $iframe = new iframeLoader();
   echo $iframe->do_iframe_script($args);
}
//
// Add an amazon store uses the admin settings but allows for overide
//
function add_iframe_a_store($src, $width='', $height='', $class=''){

   $iframe = new iframeLoader();
   $options = $iframe->iframe_cache_manager();
   
   if(empty($width)){$width = $option['amazon_width'];}
   if(empty($height)){$width = $option['amazon_height'];}
   $args = array ('src'=>$src,'width'=>$width,'height'=>$height, 'class'=>$class);
   
   echo $iframe->do_iframe_script($args);
}
//
// add an amazon by now iframe
//
function add_iframe_amazon_buy($src, $class=''){
   $args = array('src'=>$src,'width'=>'120','height'=>'240', $class='');

   $iframe = new iframeLoader();

   echo $iframe->do_iframe_script($args);
}
//
// add a youtube video to a page using the admin settings
//
function add_iframe_youtube($video, $click_words='', $click_url='', $class=''){

   $iframe = new iframeLoader();
   $options = $iframe->iframe_cache_manager();
   $args = array('youtube'=>$video,'width'=>$options['youtube_width'],'height'=>$options['youtube_height'],'click_words'=>$click_words,'click_url'=>$click_url, 'class'=>$class );
   
   echo $iframe->do_iframe_script($args);
}
//
// add a vimeo video to a page using the admin settings
//
function add_iframe_vimeo($video, $click_words='', $click_url='', $class=''){
   $iframe = new iframeLoader();
   $options = $iframe->iframe_cache_manager();
   $args = array('vimeo'=>$video,'width'=>$options['vimeo_width'],'height'=>$options['vimeo_height'],'click_words'=>$click_words,'click_url'=>$click_url, 'class'=>$class );
   echo $iframe->do_iframe_script($args);
}
?>
