<?php
/*
Plugin Name: Last Seen Posts
Plugin URI: http://curlybracket.net/plugz/lsp
Version: 1.1
Author: Ulrike Uhlig
Author URI: http://curlybracket.net
License: GPLv2
*/
/*
    Copyright 2012  Ulrike Uhlig  (email : u@curlybracket.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?
/*
 * Session handling functions from http://wordpress.org/extend/plugins/simple-session-support/ by P.K. Wooster
 */
/**
 * add actions at initialization to start the session
 * and at logout and login to end the session
 */
add_action('init', 'lspSimpleSessionStart', 1);

/**
 * start the session, after this call the PHP $_SESSION super global is available
 */
function lspSimpleSessionStart() {
    if(!session_id())session_start();
	ob_start();
}

/**
 * get a value from the session array
 * @param type $key the key in the array
 * @param type $default the value to use if the key is not present. empty string if not present
 * @return type the value found or the default if not found
 */
function lspSimpleSessionGet($key, $default='') {
    if(isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }
}

/**
 * set a value in the session array
 * @param type $key the key in the array
 * @param type $value the value to set
 */
function lspSimpleSessionSet($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Save last seen posts to session va
 */
function lastseenposts() {
	if( is_single() AND is_user_logged_in() ) {
		global $post;
		$show_items = 5;
		$session_lastseen = lspSimpleSessionGet('lastseen');
		if(!$session_lastseen) {
			$session_lastseen = array('lastseen');
		}
		$nb_items = count($session_lastseen);
		$lastseenid = $post->ID;

		$session_add = array(
			"id_$lastseenid" => array (
				"id" => $lastseenid,
				"name" => $post->post_title,
				"url" => $post->guid
			)
		);

		// add new post to session
		array_push($session_lastseen, $session_add);
		// cut session to show max items
		$session_lastseen = array_slice($session_lastseen, "-$show_items", $show_items);
		// set updated session var
		lspSimpleSessionSet("lastseen", $session_lastseen);
	}
}

/**
 * Create Widget
 */
class lastSeenPosts extends WP_widget {
    function lastSeenPosts() {
        // Constructor
        parent::WP_Widget(false, $name = 'lastSeenPosts', array("description" => 'Link to last seen posts'));
    }

    function widget($args, $instance) {
        extract( $args );
		$lastseen_session = lspSimpleSessionGet('lastseen');
        if($lastseen_session) {
			$title = apply_filters('widget_title', $instance['title']);
			echo $before_widget;
			if($title)
				echo $before_title . $title . $after_title;
				echo '<ul class="lsp">';
				foreach($lastseen_session as $tmp) {
					if(is_array($tmp)) {
						foreach($tmp as $lastseen_post) {
							$post_thumbnail_id = get_the_post_thumbnail($lastseen_post['id']);
							echo '<li><a class="lastseen" href="' .$lastseen_post['url']. '">' . $post_thumbnail_id . $lastseen_post['name']. '</a></li>';
						}
					}
				}
				echo '</ul>';
			echo $after_widget;
		}
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </label>
        </p>
        <?php
    }
}
add_action('wp_head', 'lastseenposts');
add_action('widgets_init', create_function('', 'return register_widget("lastSeenPosts");'));

// Stylesheet
function lsp_styles() {
	$style_path = WP_PLUGIN_DIR . '/lastseenposts/lsp.css';
	if (file_exists($style_path)) {
		wp_register_style('lsp_styles', plugins_url('lsp.css', __FILE__));
		wp_enqueue_style( 'lsp_styles');
	}
}
add_action('wp_enqueue_scripts', 'lsp_styles');
?>
