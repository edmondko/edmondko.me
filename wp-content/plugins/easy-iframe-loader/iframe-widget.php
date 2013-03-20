<?php
class iFrame_Widget  extends WP_Widget {
    function iFrame_Widget() {
        /* Widget settings. */
        $widget_ops = array( 'classname' => 'iframe-loader-widget', 'description' => 'Late loader for iFrames, easy to use. handy for things like google maps' );
        /* Widget control settings. */
        $control_ops = array( 'width' => 220, 'height' => 300, 'id_base' => 'iframe-loader' );
        /* Create the widget. */
        $this->WP_Widget( 'iframe-loader', 'iFrame Easy Loader', $widget_ops, $control_ops );
    }
    function widget( $args, $instance ) {
            extract( $args );

    /* User-selected settings. */
    $title = apply_filters('widget_title', $instance['title'] );
    /* Before widget (defined by themes). */
    echo $before_widget;
    /* Title of widget (before and after defined by themes). */
    if ( $title )
            echo $before_title . $title . $after_title;
            /* Before widget (defined by themes). */
                /*
                 */
                 $settings = array ('height' => $instance['height'],'width' => $instance['width'],'frameborder' => $instance['frameborder'],
                 'scrolling'=>$instance['scrolling'], 'src'=>$instance['src'],
                'longdesc'=>$instance['longdesc'],'marginheight'=>$instance['marginheight'],'marginwidth'=>$instance['marginwidth'], 'name'=>$instance['name'],'click_words'=>$instance['click_words'],'click_url'=>$instance['click_url']);
                add_iframe_late_load($settings);
                 /*
                 *
                 *  After widget (defined by themes). */
                echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            /* Strip tags (if needed) and update the widget settings. */
            $instance['title'] = $new_instance['title'];
            $instance['height'] = $new_instance['height'];
            $instance['width'] = $new_instance['width'];
            $instance['frameborder'] = $new_instance['frameborder'];
            $instance['scrolling']= $new_instance['scrolling'];
            $instance['src']=$new_instance['src'];
            $instance['longdesc']=$new_instance['longdesc'];
            $instance['marginheight']=$new_instance['marginheight'];
            $instance['marginwidth']=$new_instance['marginwidth'];
            $instance['name']=$new_instance['name'];
            $instance['click_words']=$new_instance['click_words'];
            $instance['click_url']=$new_instance['click_url'];
        return $instance;
    }


    function form( $instance ) {

            /* Set up some default widget settings. */
           $options = get_option("iframeLoaderAdminOptions");
           $defaults = array(
               'title'=>'',
               'height' => $options['widget_height'],'width' => $options['widget_width'],'frameborder' => '0','scrolling'=>'no', 'src'=>'',
                'longdesc'=>'','marginheight'=>'0','marginwidth'=>'0', 'name'=>'','click_words'=>'','click_url'=>''
           );
            $instance = wp_parse_args( (array) $instance, $defaults ); ?>
            <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','iframeLoader'); ?></label>
                    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'src' ); ?>"><?php _e('iFrame URL:','iframeLoader'); ?></label>
                    <input type="text" id="<?php echo $this->get_field_id( 'src' ); ?>" name="<?php echo $this->get_field_name( 'src' ); ?>" value="<?php echo $instance['src']; ?>" style="width:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('iFrame height:','iframeLoader'); ?></label>
                    <input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('iFrame width:','iframeLoader'); ?></label>
                    <input type="text" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" style="width:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'scrolling' ); ?>"><?php _e('iFrame scrolling:','iframeLoader'); ?></label><br />
                    <input type="radio" name="<?php echo $this->get_field_name( 'scrolling' ); ?>" value="auto" <?php if ($instance['scrolling'] == "auto"){echo "checked=\"checked\"";} ?>  />auto&nbsp;&nbsp;
                    <input type="radio" name="<?php echo $this->get_field_name( 'scrolling' ); ?>" value="yes" <?php if ($instance['scrolling'] == "yes"){echo "checked=\"checked\"";} ?>  />yes&nbsp;&nbsp;
                    <input type="radio" name="<?php echo $this->get_field_name( 'scrolling' ); ?>" value="no" <?php if ($instance['scrolling'] == "no"){echo "checked=\"checked\"";} ?> />no
            </p>
            <p>
                    <label for="<?php echo $this->get_field_id( 'frameborder' ); ?>"><?php _e('iFrame frameborder:','iframeLoader'); ?></label><br />
                    <input type="radio" name="<?php echo $this->get_field_name( 'frameborder' ); ?>" value="0" <?php if ($instance['frameborder'] == "0"){echo "checked=\"checked\"";} ?>  />no&nbsp;&nbsp;
                    <input type="radio" name="<?php echo $this->get_field_name( 'frameborder' ); ?>" value="1" <?php if ($instance['frameborder'] == "1"){echo "checked=\"checked\"";} ?>  />yes&nbsp;&nbsp;
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'marginwidth' ); ?>"><?php _e('iFrame marginwidth:','iframeLoader'); ?></label><br />
                    <input type="text" id="<?php echo $this->get_field_id( 'marginwidth' ); ?>" name="<?php echo $this->get_field_name( 'marginwidth' ); ?>" value="<?php echo $instance['marginwidth']; ?>" style="marginwidth:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'marginheight' ); ?>"><?php _e('iFrame marginheight:','iframeLoader'); ?></label><br />
                    <input type="text" id="<?php echo $this->get_field_id( 'marginheight' ); ?>" name="<?php echo $this->get_field_name( 'marginheight' ); ?>" value="<?php echo $instance['marginheight']; ?>" style="marginheight:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('iFrame name:','iframeLoader'); ?></label><br />
                    <input type="text" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="name:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'longdesc' ); ?>"><?php _e('iFrame longdesc:','iframeLoader'); ?></label><br />
                    <input type="text" id="<?php echo $this->get_field_id( 'longdesc' ); ?>" name="<?php echo $this->get_field_name( 'longdesc' ); ?>" value="<?php echo $instance['longdesc']; ?>" style="longdesc:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'click_words' ); ?>"><?php _e('iFrame click_words:','iframeLoader'); ?></label><br />
                    <input type="text" id="<?php echo $this->get_field_id( 'click_words' ); ?>" name="<?php echo $this->get_field_name( 'click_words' ); ?>" value="<?php echo $instance['click_words']; ?>" style="click_words:90%" />
            </p>

            <p>
                    <label for="<?php echo $this->get_field_id( 'click_url' ); ?>"><?php _e('iFrame click_url:','iframeLoader'); ?></label><br />
                    <input type="text" id="<?php echo $this->get_field_id( 'click_url' ); ?>" name="<?php echo $this->get_field_name( 'click_url' ); ?>" value="<?php echo $instance['click_url']; ?>" style="click_url:90%" />
            </p>


            <?php
    }
}


?>
