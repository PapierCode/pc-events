<?php

/*
Plugin Name: [PC] Events
Plugin URI: www.papier-code.fr
Description: Actualités
Version: 1.0.9
Author: Papier Codé
*/


define( 'EVENTS_POST_SLUG', 'events' );
define( 'EVENTS_TAX_SLUG', 'taxevents' );

add_filter( 'query_vars', 'pc_events_query_vars' );

	function pc_events_query_vars( $vars ) {

		$vars[] = 'eventpast';
		$vars[] = 'eventtax';
		$vars[] = 'eventpaged';
		return $vars;

	}
	

/*=============================
=            Admin            =
=============================*/

include 'pc-events_admin.php';

add_action( 'admin_enqueue_scripts', 'pc_events_admin_enqueue_scripts' );

	function pc_events_admin_enqueue_scripts( $hook_suffix ) {
		
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php') ) ) {

			global $settings_pc;
			wp_enqueue_script( 'google-map', 'https://maps.googleapis.com/maps/api/js?key='.$settings_pc['google-map-api-key'] );

		}

	};


/*=====  FIN Admin  =====*/

/*================================
=            Includes            =
================================*/

add_action( 'plugins_loaded', 'pc_events_setup' );

	function pc_events_setup() {

		include 'post/register.php';
		include 'post/fields_post.php';
		include 'post/fields_taxonomy.php';
		include 'pc-events_templates.php';

	}


/*=====  FIN Includes  =====*/