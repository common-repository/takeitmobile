<?php
/*
Plugin Name: TakeItMobile
Plugin URI: http://www.novaadvertising.com/takeitmobile
Description: This plugin's widget allows you to auto generate a QR Code for your web pages. We created this to allow websites to provide visitors the ability to easily continue reading websites on the run! TakeItMobile uses the technology of QR-SERVER, read more here: http://goqr.me/
Author: NOVA Advertising
Version: 1.0
Author URI: http://www.novaadvertising.com
License: GPL2

    NOVA Advertising (email : takeitmobile@novaadvertising.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
    
*/

class TakeItMobile_Widget extends WP_Widget {

	function TakeItMobile_Widget() {
		$widget_ops = array('classname' => 'widget_takeitmobile', 'description' => __('Auto Generates QR Code'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('takeitmobile', __('TakeItMobile'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$text = apply_filters( 'widget_takeitmobile', $instance['text'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
			ob_start();
			eval('?>'.$text);
			$text = ob_get_contents();
			ob_end_clean();
			?>			
			<div class="takeitmobileqr"><?php echo $instance['filter'] ? wpautop($text) : $text; ?><img src="http://api.qrserver.com/v1/create-qr-code/?size=100x100&amp;data=<?php the_permalink(); ?> "/></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( $new_instance['text'] ) );
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("TakeItMobile_Widget");'));