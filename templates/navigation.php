<?php
/**
 * 
 * [PC] Événements : templates navigation & filtres
 * 
 ** Menu item actif
 ** Fil d'ariane
 ** Filtres catégories/passés
 * 
 */


/*=======================================
=            Menu item actif            =
=======================================*/

add_filter( 'wp_nav_menu_objects', 'pc_events_edit_single_nav_item_active', NULL, 2 );

function pc_events_edit_single_nav_item_active( $menu_items, $args ) {

	// si menu d'entête
	if ( $args->theme_location == 'nav-header' ) {

		// si c'est un événement ou une catégorie
		if ( is_singular( EVENTS_POST_SLUG ) || is_tax( EVENTS_TAX_SLUG ) ) {

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

		if ( is_singular( EVENTS_POST_SLUG ) || is_tax( EVENTS_TAX_SLUG ) ) {


			/*----------  Lien page (événements à venir)  ----------*/
			
			$events_archive = pc_get_page_by_custom_content( EVENTS_POST_SLUG, 'object' );
			$pc_events_archive = new PC_Post( $events_archive );

			$links[] = array(
				'name' => $pc_events_archive->get_card_title(),
				'permalink' => $pc_events_archive->permalink
			);


			/*----------  Single  ----------*/

			if ( is_singular( EVENTS_POST_SLUG ) ) {

				if ( get_query_var( 'eventpast' ) ) {

					$links[] = array(
						'name' => 'Passés',
						'permalink' => $pc_events_archive->permalink.'?eventpast=1'
					);

					if ( get_query_var( 'eventpaged' ) ) {
	
						$links[] = array(
							'name' => 'Page '.get_query_var( 'eventpaged' ),
							'permalink' => $pc_events_archive->permalink.'page/'.get_query_var( 'eventpaged' ).'/?eventpast=1'
						);
	
					}

				} else if ( get_query_var( 'eventtax' ) ) {

					global $settings_pc;
					$term_from = get_term( get_query_var( 'eventtax' ) );
					$term_from_link = get_term_link( $term_from );

					switch ( $settings_pc['events-tax'] ) {

						case 'pages':	
							$links[] = array(
								'name' => $term_from->name,
								'permalink' => $term_from_link
							);
							if ( get_query_var( 'eventpaged' ) ) {
								$links[] = array(
									'name' => 'Page '.get_query_var( 'eventpaged' ),
									'permalink' => $term_from_link.'page/'.get_query_var( 'eventpaged' )
								);
							}
							break;

						case 'filters':	
							$links[] = array(
								'name' => $term_from->name,
								'permalink' => $pc_events_archive->permalink.'?eventtax='.get_query_var( 'eventtax' )
							);
							if ( get_query_var( 'eventpaged' ) ) {
								$links[] = array(
									'name' => 'Page '.get_query_var( 'eventpaged' ),
									'permalink' => $pc_events_archive->permalink.'page/'.get_query_var( 'eventpaged' ).'?eventtax='.get_query_var( 'eventtax' )
								);
							}
							break;

					}

				} else if ( get_query_var( 'eventpaged' ) ) {

					$links[] = array(
						'name' => 'Page '.get_query_var( 'eventpaged' ),
						'permalink' => $pc_events_archive->permalink.'page/'.get_query_var( 'eventpaged' ).'/'
					);

				}
	
			}
			
		}

		return $links;

	}

add_filter( 'pc_filter_breadcrumb_before_display', 'pc_events_edit_breadcrumb_before_display' );

	function pc_events_edit_breadcrumb_before_display( $links ) {

		if ( is_page() && ( get_query_var('eventpast') || get_query_var('eventtax') ) ) {

			global $pc_post;
			
			if ( get_query_var('eventpast') ) { 

				$link = array( 
					array(
						'name' => 'Passés',
						'permalink' => $pc_post->permalink.'?eventpast=1'
					)
				);

			} else if ( get_query_var( 'eventtax' ) ) {
				
				$term = get_term( get_query_var( 'eventtax' ) );
				$link = array( 
					array(
						'name' => $term->name,
						'permalink' => $pc_post->permalink.'?eventtax='.get_query_var( 'eventtax' )
					)
				);

			}

			if ( get_query_var( 'paged' ) ) {
				$links = array_merge(
					array_slice( $links, 0, 2),
					$link,
					array_slice( $links, 2)
				);
				$links[array_key_last($links)]['permalink'] .= '?eventtax='.get_query_var( 'eventtax' );
			} else {
				$links[] = $link[0];
			}

		}

		return $links;

	}


/*=====  FIN Fil d'ariane  =====*/

/*=================================================
=            Filtres catégories/passés            =
=================================================*/

function pc_events_display_filters( $current_id = '', $archive_url = null ) {

	global $settings_pc;

	/*----------  Toggle à venir / passés / tous  ----------*/

	// page par défaut des événements
	if ( is_null( $archive_url ) ) { $archive_url = pc_get_page_by_custom_content( EVENTS_POST_SLUG ); }

	$nav_css = 'event-filter';
	$btn_past_css = 'event-filter-btn event-filter-btn--past button';
	$btn_past_ico = pc_svg('arrow');

	// si c'est une catégorie (filtre ou page)
	if ( get_query_var( 'eventtax' ) || is_tax( EVENTS_TAX_SLUG ) )  {
		$btn_past_link = '<a href="'.$archive_url.'" class="'.$btn_past_css.'" title="Tous les événements à venir"><span class="ico">'.$btn_past_ico.'</span><span class="txt">Tous les événements</span></a>';

	// si ce sont les événements passés
	} else if ( get_query_var('eventpast') ) {
		$nav_css .= ' event-filter--past';
		$btn_past_link = '<a href="'.$archive_url.'" class="'.$btn_past_css.'"><span class="ico">'.$btn_past_ico.'</span><span class="txt">Événements à venir</span></a>';

	// si ce sont les événements à venir
	} else {
		$btn_past_link = '<a href="'.$archive_url.'?eventpast=1" class="'.$btn_past_css.'"><span class="ico">'.$btn_past_ico.'</span><span class="txt">Événéments passés</span></a>';;
	}


	/*----------  Affichage  ----------*/	

	echo '<nav role="navigation" aria-label="Catégories des événements" class="'.$nav_css.'">';

	// si ce ne sont pas les événements archivés
	// et si les catégories sont activées (filtre ou pages)
	if ( !get_query_var('eventpast') && in_array( $settings_pc['events-tax'], array( 'filters', 'pages' ) ) ) {

		$today = date('Y-m-d');

		// tous les événements à venir
		$events_to_come = get_posts( array(
			'post_type' => EVENTS_POST_SLUG,
			'posts_per_page' => -1,
			'fields' => 'ids',
			'meta_query' => array(
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
			)
		) );
		// catégories associées aux événéments à venir
		$terms = wp_get_object_terms( $events_to_come, EVENTS_TAX_SLUG );

		if ( count( $terms ) >  0 ) {

				echo '<button type="button" id="event-filter-btn" class="event-filter-btn event-filter-btn--toggle button js-toggle" aria-controls="event-filter-list" aria-expanded="false" title="Afficher/masquer les catégories"><span class="ico">'.pc_svg('tag').'</span><span class="txt">Catégories</span></button>';

				echo '<ul id="event-filter-list" class="event-filter-list reset-list" aria-hidden="true" aria-labelledby="event-filter-btn" style="display:none">';

					foreach ( $terms as $term ) {

						$link_attrs = array(
							'class' => array( 'event-filter-link', 'button' )
						);

						switch ( $settings_pc['events-tax'] ) {
							case 'filters':
								$link_attrs['href'] = array( $archive_url.'?eventtax='.$term->term_id );
								$link_attrs['rel'] = array( 'nofollow' );
								break;
							case 'pages':
								$link_attrs['href'] = array( get_term_link( $term->term_id ) );
								break;
						}
						
						if ( '' != $current_id && $current_id == $term->term_id ) {
							$link_attrs['class'][] = 'is-active';
							$link_attrs['aria-current'] = array( 'page' );
						}
						$link_attrs_str = '';
						foreach ( $link_attrs as $name => $values ) {
							$link_attrs_str .= ' '.$name.'="'.implode(' ',$values).'"';
						}

						echo '<li class="event-filter-item"><a'.$link_attrs_str.'>'.$term->name.'</a></li>';

					}

				echo '</ul>';

				echo $btn_past_link;

		} // FIN if terms

	} else { // FIN if settings tax

		echo $btn_past_link;

	} // FIN if ! settings tax

	echo '</nav>';

}


/*=====  FIN Filtres catégories/passés  =====*/