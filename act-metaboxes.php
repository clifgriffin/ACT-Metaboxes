<?php
/*
Plugin Name: ACT Metaboxes
Plugin URI: http://cgd.io/
Description:  Adds metaboxes from other post types to ACT templates. 
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2013-2015 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class ACT_Metaboxes {
	public function __construct() {
		add_action('act_loaded', array($this, 'init') );
	}
	
	function init() {
		global $AdvancedContentTemplates;
		
		// Add metaboxes 
		add_action('add_meta_boxes' . $AdvancedContentTemplates->post_type, array($this, 'glue_act_metaboxes'), 1000 );
	}
	
	function glue_act_metaboxes() {
		global $wp_meta_boxes, $AdvancedContentTemplates;
		
		// Force Metaboxes to Populate 
		do_action( 'add_meta_boxes_post');
		
		$act_screen = convert_to_screen( $AdvancedContentTemplates->post_type );
		$post_screen = convert_to_screen( 'post' ); 

		foreach($wp_meta_boxes[$post_screen->id] as $priority => $boxes) {
			foreach ( $boxes as $position => $mboxes ) {
				$wp_meta_boxes[$act_screen->id][$priority][$position] = (array)$wp_meta_boxes[$act_screen->id][$priority][$position];
				$wp_meta_boxes[$act_screen->id][$priority][$position] = array_merge($wp_meta_boxes[$act_screen->id][$priority][$position], $mboxes);
				
				// Remove our own metaboxes
				if ( isset( $wp_meta_boxes[$act_screen->id][$priority][$position]['act_side_car'] ) ) {
					unset($wp_meta_boxes[$act_screen->id][$priority][$position]['act_side_car']);
				}
			}
		}
	}
}

$ACT_Metaboxes = new ACT_Metaboxes();