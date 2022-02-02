<?php
/**
 * 
 * [PC] Événements : template accueil
 * 
 */


/*----------  Événements à venir  ----------*/

add_action( 'pc_action_home_main_content', 'pc_events_display_home_last_events', 35 );

	function pc_events_display_home_last_events( $pc_home ) {

		$metas = $pc_home->metas;
		$archive_permalink = pc_get_page_by_custom_content( EVENTS_POST_SLUG );
		$today = date('Y-m-d');

		// liste
		$home_events = get_posts(array(
			'post_type' => EVENTS_POST_SLUG,
			'posts_per_page' => 4,
			'order' => 'ASC',
			'orderby' => 'meta-value-date',
			'meta_key' => 'event-date-start',
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
		));
		// titre de la section
		$title = ( isset($metas['content-events-title']) && $metas['content-events-title'] != '' ) ? $metas['content-events-title'] : 'Événements à venir';

		// affichage des résumés de pages
		if ( count($home_events) > 0 ) {

			echo '<div class="home-events">';
			echo '<h2 class="home-title-sub">'.$title.'</h2>';
			echo '<ul class="st-list st-list--events reset-list">';

			foreach ($home_events as $key => $post) {
		
				// début d'élément
				echo '<li class="st st--event">';

					$pc_post = new PC_Post( $post );

					// affichage résumé
					$pc_post->display_card( 3, 'st-inner', array( 'archive_permalink' => $archive_permalink ) );
					
					// données structurée de la liste
					add_filter( 'pc_filter_home_schema_collection_page', function( $schema_collection_page ) use( $pc_post ) {
						$key = count( $schema_collection_page['mainEntity']['itemListElement'] ) + 1;
						$schema_collection_page['mainEntity']['itemListElement'][] = $pc_post->get_schema_list_item( $key );
						return $schema_collection_page;
					} );
		
				// fin d'élément
				echo '</li>';

			}

			echo '</ul>';
			echo '</div>';
		}

	}