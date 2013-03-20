<?php
/**
 * The widget that shows WP Portfolio in widget areas.
 */
class WPPortfolioWidget extends WP_Widget {
	
	/**
	 * Constructor
	 * @return wp-portfolioWidget
	 */
	function WPPortfolioWidget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'example', 'description' => 'A widget that allows to show off some of your portfolio in the sidebar.' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 500, 'height' => 40, 'id_base' => 'wp-portfolio-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'wp-portfolio-widget', 'WP Portfolio Widget', $widget_ops, $control_ops );		
	}

	/**
	 * Outputs the content of the widget.
	 * @param $args
	 * @param $instance
	 */
	function widget($args, $instance)
	{
		extract($args);
		extract($instance);
		
		// Clean up any whitespace
		$cssid 			= trim($cssid);
		$description 	= trim($description);
		$layouthtml 	= trim($layouthtml);
		
		// Show widget regardless if there are websites
		echo $before_widget;
		echo $before_title . $title . $after_title; 
		
		// Start the wrapper for the portfolio, using a CSS ID if we have one
		if ($cssid) {
			printf('<div id="%s" class="wp-portfolio-widget">', $cssid);
		} else {
			echo '<div class="wp-portfolio-widget">';
		}
		
		// Add our optional description
		if ($description) {
			printf('<div class="wp-portfolio-widget-des">%s</div>', $description);
		}
		
		
		if (!$layouthtml) {
			$layouthtml = WPP_DEFAULT_WIDGET_TEMPLATE;
		}
		
		switch ($orderby) 
		{
			case 'normal-asc':
				echo WPPortfolio_getAllPortfolioAsHTML($grouplist, $layouthtml, ' ', false, true, 'normal', $websitecount, true);
				break;
				
			case 'normal-desc':
				echo WPPortfolio_getAllPortfolioAsHTML($grouplist, $layouthtml, ' ', false, false, 'normal', $websitecount, true);
				break;				
			
			case 'date-desc':
				echo WPPortfolio_getAllPortfolioAsHTML($grouplist, $layouthtml, ' ', false, true, 'dateadded', $websitecount, true);
				break;
			
			case 'date-asc':
				echo WPPortfolio_getAllPortfolioAsHTML($grouplist, $layouthtml, ' ', false, false, 'dateadded', $websitecount, true);
				break;
					
			case 'random':
				echo WPPortfolio_getRandomPortfolioSelectionAsHTML($grouplist, $websitecount, $layouthtml, true);
				break;
			
		}		
		
		// Closing wrapper tag
		echo '</div>';
		 
		// This always needs to go at the end.
		echo $after_widget; 
	}

	/**
	 * Method called when the widget options are updated.
	 * @param $new_instance The new instance.
	 * @param $old_instance The old instance.
	 * @return Instance The new instance.
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;		
	}

	/**
	 * Outputs the options form in the admin section.
	 * @param $instance The intance that's being updated.
	 */
	function form($instance) 
	{
		extract($instance);
		
		// Create a default layout
		if (!$layouthtml) {
			$layouthtml = WPP_DEFAULT_WIDGET_TEMPLATE;
		}		
        
        ?>
            <p>
            	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-portfolio'); ?>
            		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            		<br><small>The title of the widget with the portfolio. If blank, no title is used.</small> 
            	</label>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'wp-portfolio'); ?>
            		<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" type="text" value="<?php echo $description; ?>" />
            		<br><small>The description to put below the title. HTML is permitted. If blank, no description is used.</small> 
            	</label>
            </p> 
            
            <p>
            	<label for="<?php echo $this->get_field_id('grouplist'); ?>"><?php _e('List of Groups to include:', 'wp-portfolio'); ?>
            		<input class="widefat" id="<?php echo $this->get_field_id('grouplist'); ?>" name="<?php echo $this->get_field_name('grouplist'); ?>" type="text" value="<?php echo $grouplist; ?>" />
            		<br><small>The comma separated list of groups to use, e.g. '1,2,4'. If no group is specified, all websites are used.</small> 
            	</label>
            </p>                     
                       
            <div>
            	<div style="width:45%; float: left; margin-right: 10px;">
	            	<label for="<?php echo $this->get_field_id('websitecount'); ?>"><?php _e('Number of Websites to show:', 'wp-portfolio'); ?>
	            		<input class="widefat" id="<?php echo $this->get_field_id('websitecount'); ?>" name="<?php echo $this->get_field_name('websitecount'); ?>" type="text" value="<?php echo $websitecount; ?>" /> 
	            		<br><small>The number of websites to show. If blank or 0, show all.</small> 
	            	</label>
            	</div>
            	<div style="width:45%; float: left;">
	            	<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Ordering Method:', 'wp-portfolio'); ?>
	            		<select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
	            			<option value="normal-asc" <?php echo ($orderby == 'normal-asc' ? 'selected="selected"' : ''); ?>>Normal Ordering, Ascending (A -&gt; Z)</option>
	            			<option value="normal-desc" <?php echo ($orderby == 'normal-desc' ? 'selected="selected"' : ''); ?>>Normal Ordering, Descending (Z -&gt; A)</option>
	            			<option value="date-desc" <?php echo ($orderby == 'date-desc' ? 'selected="selected"' : ''); ?>>Order By Date, Descending (Newsest -&gt; Oldest)</option>
	            			<option value="date-asc" <?php echo ($orderby == 'date-asc' ? 'selected="selected"' : ''); ?>>Order By Date, Ascending (Oldest -&gt; Newest)</option>
	            			<option value="random" <?php echo ($orderby == 'random' ? 'selected="selected"' : ''); ?>>Order Randomly</option>
	            		</select>
	            	</label>
	            	<br><small>How to order the websites in the widget.</small>
            	</div>
            	<div style="clear: both; height: 0; margin-bottom: 10px;">&nbsp;</div>
            </div> 
            
            <p>
            	<label for="<?php echo $this->get_field_id('layouthtml'); ?>"><?php _e('Layout HTML:', 'wp-portfolio'); ?> <small><i><?php _e('Required', 'wp-portfolio'); ?></i></small>
            		<textarea class="widefat" id="<?php echo $this->get_field_id('layouthtml'); ?>" name="<?php echo $this->get_field_name('layouthtml'); ?>" rows="5"><?php echo $layouthtml; ?></textarea>
            		<br><small>The HTML used to render the widget. <a href="<?php echo WPP_DOCUMENTATION ?>#doc-layout" target="_blank"><?php _e('Template Documentation', 'wp-portfolio')?></a></small> 
            	</label>
            </p>   
            
			<p>
            	<label for="<?php echo $this->get_field_id('cssid'); ?>"><?php _e('Custom CSS ID:', 'wp-portfolio'); ?>
            		<input class="widefat" id="<?php echo $this->get_field_id('cssid'); ?>" name="<?php echo $this->get_field_name('cssid'); ?>" type="text" value="<?php echo $cssid; ?>" />
            		<br><small>The CSS ID to use to wrap the portfolio widget to allow you to add styles for a specific portfolio widget.</small>            		
            	</label>
            </p>

        <?php
		
		// + Show in order/desc order - by date asc, or date desc, random
		// + Groups to use/show
		// + Layout HTML
        
    
	}
} // end of wp-portfolioWidget
add_action('widgets_init', create_function('', 'return register_widget("WPPortfolioWidget");'));


?>