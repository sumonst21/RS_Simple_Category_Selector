<?php
/*
Plugin Name: Simple Category Selector
Plugin URI: https://github.com/saurini/Wordpress_Category-Selector
Description: Gives the ability to set a main category on the post add/edit pages in admin. Stores that info in post meta.
Author: Rob Saurini
Version: 0.1
Author URI: http://saurini.com

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

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

if ( ! defined( 'RSSCS_PLUGIN_BASENAME' ) )
	define( 'RSSCS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'RSSCS_PLUGIN_NAME' ) )
	define( 'RSSCS_PLUGIN_NAME', trim( dirname( RSSCS_PLUGIN_BASENAME ), '/' ) );
	
if ( ! defined( 'RSSCS_PLUGIN_URL' ) )
	define( 'RSSCS_PLUGIN_URL', WP_PLUGIN_URL . '/' . RSSCS_PLUGIN_NAME );

class RS_Simple_Category_Selector {

	private $category_selected;

	function __construct() {
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'admin_print_scripts-post.php', array( $this, 'print_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'print_scripts' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'print_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'print_styles' ) );
		add_action( 'wp_ajax_get_main_category_meta', array( $this, 'get_main_category_meta' ) );
	}

	function print_scripts() {
		wp_enqueue_script( 'rs-simple-category-selector', RSSCS_PLUGIN_URL . '/rs-simple-category-selector.js', array('jquery') );
	}

	function print_styles() {
		wp_enqueue_style( 'rs-simple-category-selector', RSSCS_PLUGIN_URL . '/rs-simple-category-selector.css' );
	}

	function save_post( $post_id, $post ) {

		$this->category_selected = (int) $_POST['rs_category_selected'];
		if ( $post->post_type != 'post' || ! get_the_category_by_ID( $this->category_selected ) )
			return;

		empty( $this->category_selected ) ? delete_post_meta( $post_id, 'rs_category_selected' ) : update_post_meta( $post_id, 'rs_category_selected', $this->category_selected );
	}

	function get_main_category_meta() {

		if ( empty( $_POST['postid'] ) )
			die( 'Error: No post has been selected' );

		$post_id = $_POST['postid'];

		if ( ! is_admin() )
			die( 'Error: this can only be done in the admin' );

		$meta_data = get_post_meta( $post_id, 'rs_category_selected', true );

		if ( ! empty( $meta_data ) && get_the_category_by_ID( $meta_data ) )
			die( $meta_data );

		die( 'No rs_category_selected was found' );
	}

}

$category_selector = new RS_Simple_Category_Selector;