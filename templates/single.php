<?php
/**
 * 
 * [PC] Événements : template single
 * 
 ** Redirection template
 ** Classes CSS
 ** Menu item actif
 ** Contenu
 ** Résultats de recherche
 * 
 */

 
/*=========================================================
=            Redirection vers la template page            =
=========================================================*/

add_filter( 'single_template', 'pc_events_edit_single_template', 999, 1 );

	function pc_events_edit_single_template( $single_template ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {
			$single_template = get_template_directory().'/page.php';
		}

		return $single_template;

	}


/*=====  FIN Redirection vers la template page  =====*/

/*===================================
=            Classes CSS            =
===================================*/

add_filter( 'pc_filter_html_css_class', 'pc_events_edit_single_html_css_class' );

function pc_events_edit_single_html_css_class( $css_classes ) {
	
	if ( is_singular( EVENTS_POST_SLUG ) ) {
		$css_classes[] = 'is-page';
		$css_classes[] = 'is-event';
	}

	return $css_classes;

}


/*=====  FIN Classes CSS  =====*/

/*=======================================
=            Menu item actif            =
=======================================*/

add_filter( 'wp_nav_menu_objects', 'pc_events_edit_single_nav_item_active', NULL, 2 );

function pc_events_edit_single_nav_item_active( $menu_items, $args ) {

	// si menu d'entête
	if ( $args->theme_location == 'nav-header' ) {

		// si c'est une actualité d'afficher
		if ( is_singular( EVENTS_POST_SLUG ) ) {

			// page qui publie les actus
			$post = pc_get_page_by_custom_content( EVENTS_POST_SLUG, 'object' );
			if ( $post ) {
				// si la page qui publie les actus a un parent ou pas
				$id_to_search = ( $post->post_parent > 0 ) ? $post->post_parent : $post->ID;
			}

		}
		
		// recherche de l'item
		if ( isset($id_to_search) ) {

			foreach ( $menu_items as $object ) {
				if ( $object->object_id == $id_to_search ) {
					// ajout classe WP (remplacée dans le Walker du menu)
					$object->classes[] = 'current-menu-item';
				}
			}

		}

	}

	return $menu_items;

};


/*=====  FIN Menu item actif  =====*/

/*====================================
=            Fil d'ariane            =
====================================*/

add_filter( 'pc_filter_breadcrumb', 'pc_events_edit_breadcrumb' );

	function pc_events_edit_breadcrumb( $links ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {

			$post = pc_get_page_by_custom_content( EVENTS_POST_SLUG, 'object' );
			$pc_post = new PC_Post( $post );

			$links[] = array(
				'name' => $pc_post->get_card_title(),
				'permalink' => $pc_post->permalink
			);

		}

		return $links;

	}


/*=====  FIN Fil d'ariane  =====*/

/*===============================
=            Contenu            =
===============================*/
	
/*----------  Date  ----------*/

add_filter( 'pc_the_content_before', 'pc_events_display_single_date' );

	function pc_events_display_single_date( $before ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {

			global $pc_post;

			$before .= '<p><time class="event-date" datetime="'.$pc_post->get_date('c').'">Le '.$pc_post->get_date().'</time></p>';

		}

		return $before;

	}


/*----------  Page précédente / retour liste  ----------*/

add_action( 'pc_action_page_main_footer', 'pc_events_display_single_backlink', 20 );

	function pc_events_display_single_backlink( $pc_post ) {

		if ( $pc_post->type == EVENTS_POST_SLUG ) {

			$wp_referer = wp_get_referer();
			
			if ( $wp_referer ) {
				$back_link = $wp_referer;
				$back_title = 'Page précédente';
				$back_txt = 'Retour';
				$back_ico = 'arrow';
			} else {
				$back_link = pc_get_page_by_custom_content( EVENTS_POST_SLUG );
				$back_title = 'Tous les événements';
				$back_txt = 'd\'événements';
				$back_ico = 'more';
			}

			echo '<div class="main-footer-prev"><a href="'.$back_link.'" class="button" title="'.$back_title.'"><span class="ico">'.pc_svg($back_ico).'</span><span class="txt">'.$back_txt.'</span></a></div>';

		}

	}


/*=====  FIN Contenu  =====*/

/*==============================================
=            Résultats de recherche            =
==============================================*/


add_filter( 'pc_filter_search_results_type', 'pc_events_edit_search_results_type' );

	function pc_events_edit_search_results_type( $types ) {

		$types[EVENTS_POST_SLUG] = 'Événement';
		return $types;

	}


/*=====  FIN Résultats de recherche  =====*/