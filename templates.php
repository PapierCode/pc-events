<?php
/**
 * 
 * [PC] Événements : templates
 *
 ** Redirection vers les templates du plugin
 ** Classes CSS
 ** Métas SEO
 ** Page (archive)
 ** Taxonomie
 ** Recherche
 ** Includes
 * 
 */


/*================================================================
=            Redirection vers les templates du plugin            =
================================================================*/

add_filter( 'single_template', 'pc_events_redirect_single_template', 666, 1 );

	function pc_events_redirect_single_template( $template ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {
			$template = get_template_directory().'/page.php';
		}

		return $template;

	}

add_filter( 'taxonomy_template', 'pc_events_redirect_taxonomy_template', 666, 1 );

	function pc_events_redirect_taxonomy_template( $template ) {

		if  ( is_tax( EVENTS_TAX_SLUG ) ) {
			$template = dirname( __FILE__ ).'/templates/taxonomy.php';
		}

		return $template;

	}


/*=====  FIN Redirection vers les templates du plugin  =====*/

/*===================================
=            Classes CSS            =
===================================*/

add_filter( 'pc_filter_html_css_class', 'pc_events_edit_single_html_css_class' );

function pc_events_edit_single_html_css_class( $css_classes ) {
	
	if ( is_singular( EVENTS_POST_SLUG ) ) {
		$css_classes[] = 'is-page';
		$css_classes[] = 'is-event';
	}

	if ( is_tax( EVENTS_TAX_SLUG ) ) {
		$css_classes[] = 'is-tax';
		$css_classes[] = 'is-tax-events';
	}

	return $css_classes;

}


/*=====  FIN Classes CSS  =====*/

/*=================================
=            Métas SEO            =
=================================*/

add_filter( 'pc_filter_seo_metas', 'pc_events_filter_seo_metas' );

	function pc_events_filter_seo_metas( $metas ) {

		if ( is_page() ) {
			
			// événements passés
			if ( get_query_var('eventpast') ) {
				$metas['title'] = 'Événements passé';
			// catégories (filtres)
			} else if ( get_query_var( 'eventtax' ) ) {
				$term = get_term( get_query_var( 'eventtax' ) );
				$metas['title'] = 'Événements &quot;'.$term->name.'&quot;';
			}

			// pagination
			if ( get_query_var( 'paged' ) ) {
				$metas['title'] .= ' - Page '.get_query_var( 'paged' );	
			}
	
			// Nom du projet
			if ( get_query_var('eventpast') || get_query_var( 'eventtax' ) ) {
				global $settings_project;
				$metas['title'] .= ' - '.$settings_project['coord-name'];
			}

		} else if ( is_tax( EVENTS_TAX_SLUG ) && get_query_var( 'paged' ) ) {

			$metas['title'] .= ' - Page '.get_query_var( 'paged' );

		}

		return $metas;

	}


/*=====  FIN Métas SEO  =====*/

/*======================================
=            Page (archive)            =
======================================*/

/*----------  Titre événements passés / catégories (filtres)  ----------*/

add_filter( 'the_title', 'pc_events_archive_title', 10, 2 );

	function pc_events_archive_title ( $title, $post_id ) {

		if ( is_page() && in_the_loop() ) {
			
			// événements passés
			if ( get_query_var('eventpast') ) {				
				$title = 'Événements passés';
			// catégories (filtres)
			} else if ( get_query_var('eventtax') ) {
				$term = get_term( get_query_var('eventtax') );
				$title = 'Événements "'.$term->name.'"';
			}

		}

		return $title;

	}


/*----------  Canonical événements passés  ----------*/

add_filter( 'pc_filter_post_canonical', 'pc_events_archive_canonical', 10, 2 );

	function pc_events_archive_canonical( $canonical, $pc_post ) {

		$metas = $pc_post->metas;

		if ( isset( $metas['content-from'] ) && EVENTS_POST_SLUG == $metas['content-from'] && get_query_var( 'eventpast' ) ) {
			$canonical .= '?eventpast=1';
		}

		return $canonical;

	}


/*----------  Wysiwyg masqué pour les événements passés / les catégories (filtres) / la pagination (page 2 et +)  ----------*/

add_filter( 'pc_filter_page_wysiwyg_display', 'pc_events_archive_remove_page_wysiwyg' );

	function pc_events_archive_remove_page_wysiwyg( $display ) {

		if ( is_page() && ( get_query_var('eventpast') || get_query_var('eventtax') || get_query_var( 'paged' ) >= 1 ) ) {
			$display = false;
		}

		return $display;

	}
	

/*=====  FIN Page (archive)  =====*/

/*=================================
=            Taxonomie            =
=================================*/

/*----------  Requête par date et classement  ----------*/

add_action( 'pre_get_posts', 'pc_event_taxonomy_pre_get_posts' );

	function pc_event_taxonomy_pre_get_posts( $query ) {

		if ( $query->is_main_query() && is_tax( EVENTS_TAX_SLUG ) ) {

			$today = date('Y-m-d');
			$query->set( 'meta_query', array(
				'relation' => 'OR',
				array(
					'key'     => 'event-date-start',
					'value'   => $today,
					'type'	  => 'DATE',
					'compare' => '>=',
				),
				array(
					'key'     => 'event-date-end',
					'value'   => $today,
					'type'	  => 'DATE',
					'compare' => '>=',
				)
			));
			
			$query->set( 'order', 'ASC');
			$query->set( 'orderby', 'meta-value-date');
			$query->set( 'meta_key', 'event-date-start');

		}

	}


/*=====  FIN Taxonomie  =====*/

/*=================================
=            Recherche            =
=================================*/

/*----------  Label dans les résultats  ----------*/

add_filter( 'pc_filter_search_results_type', 'pc_events_edit_search_results_type' );

	function pc_events_edit_search_results_type( $types ) {

		$types[EVENTS_POST_SLUG] = 'Événement';
		return $types;

	}


/*=====  FIN Recherche  =====*/

/*================================
=            Includes            =
================================*/

include 'templates/navigation.php';
include 'templates/card.php';
include 'templates/single.php';
include 'templates/schemas.php';
include 'templates/home.php';


/*=====  FIN Includes  =====*/