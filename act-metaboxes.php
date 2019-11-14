<?php
/*
Plugin Name: ACT Metaboxes
Plugin URI: https://www.advancedcontenttemplates.com/
Description:  Adds metaboxes from other post types to ACT templates.
Version: 1.1.6
Author: CGD Inc.
Author URI: https://objectiv.co
GitHub URI:: https://github.com/clifgriffin/ACT-Metaboxes

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
		add_action( 'act_loaded', array( $this, 'init' ) );
	}

	function init() {
		global $Advanced_Content_Templates;

		// Add metaboxes
		add_action( 'add_meta_boxes_' . $Advanced_Content_Templates->post_type, array( $this, 'glue_act_metaboxes' ), 1000 );

		/**
		 * Esoteric compatibility patches
		 */
		$this->compatibility_magic();
	}

	function glue_act_metaboxes() {
		global $wp_meta_boxes, $Advanced_Content_Templates;

		// Force Metaboxes to Populate
		do_action( 'add_meta_boxes_post' );

		$act_screen  = convert_to_screen( $Advanced_Content_Templates->post_type );
		$post_screen = convert_to_screen( 'post' );

		foreach ( $wp_meta_boxes[ $post_screen->id ] as $priority => $boxes ) {
			foreach ( $boxes as $position => $mboxes ) {
				$wp_meta_boxes[ $act_screen->id ][ $priority ][ $position ] = (array) $wp_meta_boxes[ $act_screen->id ][ $priority ][ $position ];
				$wp_meta_boxes[ $act_screen->id ][ $priority ][ $position ] = array_merge( $wp_meta_boxes[ $act_screen->id ][ $priority ][ $position ], $mboxes );

				// Remove our own metaboxes
				if ( isset( $wp_meta_boxes[ $act_screen->id ][ $priority ][ $position ]['act_side_car_post'] ) ) {
					unset( $wp_meta_boxes[ $act_screen->id ][ $priority ][ $position ]['act_side_car_post'] );
				}
			}
		}
	}

	function compatibility_magic() {
		$current_theme = wp_get_theme();

		// Studiofolio Theme
		if ( $current_theme->get( 'Name' ) == 'Studiofolio' ) {
			add_action( 'admin_init', array( $this, 'studiofolio_fix' ), 0 );
		}

		// Get all active plugins
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		// Fix for WPSEO not showing on our private custom post type
		if ( in_array( 'wordpress-seo/wp-seo.php', $active_plugins ) || in_array( 'wordpress-seo-premium/wp-seo-premium.php', $active_plugins ) ) {
			add_filter(
				'wpseo_accessible_post_types', function( $post_types ) {
					$post_types[] = 'act_template';

					return $post_types;
				}
			);
		}

	}

	function studiofolio_fix() {
		global $all_mb, $Advanced_Content_Templates;

		if ( isset( $all_mb->types ) && is_array( $all_mb->types ) ) {
			$all_mb->types[] = $Advanced_Content_Templates->post_type;
		}
	}
}

$ACT_Metaboxes = new ACT_Metaboxes();
