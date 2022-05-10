<?php
/**
 * 
 * [PC] Événements : données structurées
 * 
 ** Archive
 ** Single
 * 
 */


/*===============================
=            Archive            =
===============================*/

/*----------  Données structurées  ----------*/

add_filter ( 'pc_filter_page_schema_article_display', 'pc_events_edit_archive_schema', 10, 2 ); 

	function pc_events_edit_archive_schema( $display, $pc_post ) {

		$metas = $pc_post->metas;

		if ( isset( $metas['content-from'] ) && EVENTS_POST_SLUG == $metas['content-from'] ) {
			$display = false;
		}

		return $display;

	}


/*=====  FIN Archive  =====*/

/*==============================
=            Single            =
==============================*/

add_filter( 'pc_filter_post_schema_article', 'pc_events_edit_single_schema', 10, 2 );

	function pc_events_edit_single_schema( $schema, $pc_post ) {

		if ( EVENTS_POST_SLUG == $pc_post->type ) {

			global $settings_project;

			$metas = $pc_post->metas;
			$image_to_share = $pc_post->get_seo_meta_image_datas();	

			$schema = array(
				'@context' =>'http://schema.org',
				'@type' => 'Event',
				'url' => $pc_post->permalink,
				'name' => $pc_post->get_seo_meta_title(),
				'description' => $pc_post->get_seo_meta_description(),
				'mainEntityOfPage'	=> $pc_post->permalink,
				'image' => array(
					'@type'		=>'ImageObject',
					'url' 		=> $image_to_share[0],
					'width' 	=> $image_to_share[1],
					'height' 	=> $image_to_share[2]
				),
				'startDate' => $metas['event-date-start'],
				'endDate' => $metas['event-date-end'],
				'eventStatus' => ( isset($metas['event-status-canceled'] ) ) ? 'EventCancelled' : 'EventScheduled',
				'eventAttendanceMode' => ( isset($metas['event-status-online'] ) ) ? 'OnlineEventAttendanceMode' : 'OfflineEventAttendanceMode',
				'location' => array(
					'@type' => 'Place',
					'name' => $metas['event-location-name'],
					'latitude' => $metas['event-lat'],
					'longitude' => $metas['event-lng'],
					'address' => array(
						'@type' => 'PostalAddress',
						'streetAddress' => $metas['event-address'],
						'postalCode' => $metas['event-cp'],
						'addressLocality' => $metas['event-city'],
						'addressRegion' => 'FR'
					)
				),
				'offers' => array(
					'@type' => 'Offer',
					'url' => $pc_post->permalink,
					'availability' => 'LimitedAvailability',
					'price' => ( isset( $metas['event-status-price'] ) ) ? $metas['event-status-price'] : 0,
					'priceCurrency' => 'EUR',
					'validFrom' => $metas['event-date-start']
				),
				'organizer' => array(
					'@type' => 'Organization',
					'name' => $settings_project['coord-name'],
					'url' => get_bloginfo('url'),
					'address' => array(
						'@type' => 'PostalAddress',
						'streetAddress' => $settings_project['coord-address'],
						'postalCode' => $settings_project['coord-postal-code'],
						'addressLocality' => $settings_project['coord-city'],
						'addressRegion' => 'FR'
					)
				)
			);

			/*----------  Performer  ----------*/

			if ( isset( $metas['event-infos-performer'] ) ) {
			
				$performers = explode( '|', $metas['event-infos-performer'] );
				$schema['performer'] = array();
				foreach ( $performers as $performer ) {
					$schema['performer'][] = array(
						'@type' => 'Organization',
						'name' => $performer
					);
				}
				
			} else {

				$schema['performer'] = array(
					'@type' => 'Organization',
					'name' => $settings_project['coord-name']
				);

			}

		}

		if ( isset( $post_metas['content-from'] ) && EVENTS_POST_SLUG == $post_metas['content-from'][0] ) {
			// suppression schema article dans la liste d'actualités
			$schema = array();
		}

		return $schema;

	}

	
/*=====  FIN Single  =====*/
