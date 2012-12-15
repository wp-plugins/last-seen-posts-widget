<?php
/*
Plugin Name: Last Seen Posts
Plugin URI: http://curlybracket.net/plugz/lsp
Version: 1.0
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
add_action('wp_logout', 'lspSimpleSessionDestroy');
add_action('wp_login', 'lspSimpleSessionDestroy');
/**
 * start the session, after this call the PHP $_SESSION super global is available
 */
function lspSimpleSessionStart() {
    if(!session_id())session_start();
	ob_start();
}

/**
 * destroy the session, this removes any data saved in the session over logout-login
 */
function lspSimpleSessionDestroy() {
    session_destroy ();
	ob_flush();
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
				"url" => $_SERVER['REQUEST_URI'] // $post->guid
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
				foreach($lastseen_session as $tmp) {
					if(is_array($tmp)) {
						foreach($tmp as $lastseen_post) {
							echo '<a class="lastseen" href="' .$lastseen_post['url']. '">' .$lastseen_post['name']. '</a><br />';
						}
					}
				}
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
?>
