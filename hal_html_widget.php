<?php
/*
Plugin Name: Hal Html Widget
Plugin URI: http://halil.me
Description: Yazıları ve html kodlarını yan menüde gösterir. Bu bölümün istenen yerde gizlenmesini sağlar. Ayrıca "shortcode" desteği sunar.
Author: Halil İbrahim Özdemir
Version: 1.0
Author URI: http://halil.me
*/

class hal_html_widget extends WP_Widget {
	function hal_html_widget() {
		$widget_ops = array('classname' => 'widget_hal_html', 'description' => __('Arbitrary text or HTML'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::WP_Widget(false, $name = 'Hal Html Widget', $widget_ops, $control_ops);
	}

	function form($instance) {
		$instance = wp_parse_args((array) $instance, array('title' => '', 'text' => ''));
		$title = strip_tags($instance['title']);
		$hal_html = format_to_edit($instance['hal_html']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id('hal_html'); ?>" name="<?php echo $this->get_field_name('hal_html'); ?>"><?php echo $hal_html; ?></textarea></p>
            <p><input id="<?php echo $this->get_field_id('home'); ?>" name="<?php echo $this->get_field_name('home'); ?>" type="checkbox" <?php checked(isset($instance['home']) ? $instance['home'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('home'); ?>"><?php _e('Home'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" type="checkbox" <?php checked(isset($instance['single']) ? $instance['single'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('single'); ?>"><?php _e('Posts'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" type="checkbox" <?php checked(isset($instance['page']) ? $instance['page'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('page'); ?>"><?php _e('Pages'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="checkbox" <?php checked(isset($instance['category']) ? $instance['category'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Categories'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" type="checkbox" <?php checked(isset($instance['search']) ? $instance['search'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('search'); ?>"><?php _e('Search'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" type="checkbox" <?php checked(isset($instance['author']) ? $instance['author'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Author'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="checkbox" <?php checked(isset($instance['tag']) ? $instance['tag'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tags'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('archive'); ?>" name="<?php echo $this->get_field_name('archive'); ?>" type="checkbox" <?php checked(isset($instance['archive']) ? $instance['archive'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('archive'); ?>"><?php _e('Archives'); ?></label></p>
            <p><input id="<?php echo $this->get_field_id('error'); ?>" name="<?php echo $this->get_field_name('error'); ?>" type="checkbox" <?php checked(isset($instance['error']) ? $instance['error'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('error'); ?>"><?php _e('404'); ?></label></p>
        <?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if (current_user_can('unfiltered_html'))
			$instance['hal_html'] =  $new_instance['hal_html'];
		else
		$instance['hal_html']	= stripslashes(wp_filter_post_kses(addslashes($new_instance['hal_html'])));		
		$instance['home']		= isset($new_instance['home']);
		$instance['single']		= isset($new_instance['single']);
		$instance['page']		= isset($new_instance['page']);
		$instance['category']	= isset($new_instance['category']);
		$instance['search']		= isset($new_instance['search']);
		$instance['author']		= isset($new_instance['author']);
		$instance['tag']		= isset($new_instance['tag']);
		$instance['archive']	= isset($new_instance['archive']);
		$instance['error']		= isset($new_instance['error']);
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$hal_html = apply_filters( 'widget_hal_html', $instance['hal_html'], $instance );
		$hal_html = str_replace('[home]', get_option('home'), $hal_html);
		
		if($instance['home'] && is_home())			$x = true;
		if($instance['single'] && is_single())		$x = true;
		if($instance['page'] && is_page())			$x = true;
		if($instance['category'] && is_category())	$x = true;
		if($instance['search'] && is_search())		$x = true;
		if($instance['author'] && is_author())		$x = true;
		if($instance['tag'] && is_tag())			$x = true;
		if($instance['archive'] && is_archive())	$x = true;
		if($instance['error'] && is_404())			$x = true;
			if($x){
				echo $before_widget;
					if ($title) echo $before_title . $title . $after_title;
				echo '<div>' . do_shortcode($hal_html) . '</div>' . $after_widget;
			}
	}
}
add_action('widgets_init', create_function('', 'return register_widget("hal_html_widget");'));
?>