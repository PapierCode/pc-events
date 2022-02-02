<?php
/**
 * 
 * [PC] Événements : template single
 * 
 ** Permalink événement passé
 ** Contenu
 ** Page précédente / retour liste
 * 
 */


 /*=================================================
=            Permalink événement passé            =
=================================================*/
 
add_filter( 'post_type_link', 'pc_events_filter_post_link', 10, 2 );

function pc_events_filter_post_link( $link, $post ) {

	if ( EVENTS_POST_SLUG == $post->post_type ) {

		$date_end = new DateTime( get_post_meta( $post->ID, 'event-date-end', true ) );
		$today = new DateTime();

		if ( $date_end < $today ) { $link .= '?eventpast=1'; }

	}

	return $link;

}


/*=====  FIN Permalink événement passé  =====*/

/*===============================
=            Contenu            =
===============================*/

/*----------  Date  ----------*/

add_action( 'pc_action_page_main_content', 'pc_events_display_single_date', 25 );

	function pc_events_display_single_date( $pc_post ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {

			$metas = $pc_post->metas;

			$iso_start = $metas['event-date-start'];
			$date_start = new DateTime( $metas['event-date-start'] );
			$unix_start = $date_start->format('U');
		
			$iso_end = $metas['event-date-end'];
			$date_end = new DateTime( $metas['event-date-end'] );
			$unix_end = $date_end->format('U');
		
			$css = 'event-date';
			if ( isset( $metas['event-date-display'] ) ) {
				echo '<p class="'.$css.'">'.$metas['event-date-display'].'</p>';
				echo '<p class="visually-hidden" aria-hidden="true">';
			} else {
				echo '<p class="'.$css.'">';
			}

			echo '<span class="ico">'.pc_svg('calendar').'</span>';
			echo '<span>';
		
		
			/*----------  Dates identiques  ----------*/
			
			// même jour
			if ( (clone $date_start)->settime(0,0) == (clone $date_end)->settime(0,0) ) {
		
					// même heure
					if ( $unix_start == $unix_end ) { 
						echo '<time datetime="'.$iso_start.'">'.date_i18n( 'j F Y \à G\hi', $unix_start).'</time>';
					
					// heure différente
					} else {
						echo '<time datetime="'.$date_start->format('Y-m-d').'">'.date_i18n( 'j F Y', $unix_start).'</time>';
						echo ' de <time datetime="'.$date_start->format('H:i').'">'.$date_start->format('G\hi').'</time>';
						echo ' à <time datetime="'.$date_end->format('H:i').'">'.$date_end->format('G\hi').'</time>';
					}
		
		
			/*----------  Dates différentes  ----------*/		
		
			} else {
		
				echo 'Du <time datetime="'.$iso_start.'">'.date_i18n( 'j F Y \à G\hi', $unix_start).'</time> au <time datetime="'.$iso_end.'">'.date_i18n( 'j F Y \à G\hi', $unix_end ).'</time>';
		
			}
		
			echo '</span></p>';

		}

	}



/*----------  Adresse & map  ----------*/

add_action( 'pc_action_page_main_content', 'pc_events_display_single_address', 35 );

	function pc_events_display_single_address( $pc_post ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {
			
			$metas = $pc_post->metas;

			echo '<div class="event-location">';
			
				echo '<div class="event-location-ico">'.pc_svg('map').'</div>';

				echo '<div class="event-location-address">';
				echo '<h2 class="event-location-title">Adresse</h2>';
				echo '<address><dl class="event-location-desc">';
					echo '<dt class="name">'.$metas['event-location-name'].'</dt>';
					echo '<dd class="address">'.$metas['event-address'].' '.$metas['event-cp'].' '.$metas['event-city'].'</dd>';
					echo '<dd class="link"><a href="https://www.google.com/maps/search/?api=1&query='.$metas['event-lat'].'%2C'.$metas['event-lng'].'" class="button" title="Google Map (nouvelle fenêtre)" target="_blank"><span class="ico">'.pc_svg('arrow').'</span><span class="txt">Itinéraire</span></a></dd>';
				echo '</dl></address>';
				echo '</div>';

				echo '<div class="event-location-map" id="event-map" data-lat="'.$metas['event-lat'].'" data-lng="'.$metas['event-lng'].'" aria-hidden="true"></div>';
				
			echo '</div>';

		}

	}


/*=====  FIN Contenu  =====*/

/*======================================================
=            Page précédente / retour liste            =
======================================================*/

add_action( 'pc_action_page_main_footer', 'pc_events_display_single_backlink', 20 );

	function pc_events_display_single_backlink( $pc_post ) {

		if ( $pc_post->type == EVENTS_POST_SLUG ) {

			$wp_referer = wp_get_referer();
			
			if ( $wp_referer ) {
				$aria_label = 'Page précédente';
				$href = $wp_referer;
				$txt = 'Retour';
				$icon = 'arrow';
			} else {
				$aria_label = 'Événements';
				$href = pc_get_page_by_custom_content( EVENTS_POST_SLUG );
				$txt = '<span class="visually-hidden">Plus </span>d\'événements';
				$icon = 'more';
			}

			echo '<nav class="main-footer-prev" role="navigation" aria-label="'.$aria_label.'"><a href="'.$href.'" class="button"><span class="ico">'.pc_svg($icon).'</span><span class="txt">'.$txt.'</span></a></nav>';

		}

	}


/*=====  FIN Page précédente / retour liste  =====*/

/*=======================================
=            Dépandances map            =
=======================================*/

add_filter( 'pc_filter_js_files', 'pc_events_map_js_file' );

	function pc_events_map_js_file( $js_files ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {
			$js_files['leaflet'] = get_stylesheet_directory_uri().'/scripts/include/leaflet.js';
		}

		return $js_files;		

	}

add_action( 'wp_enqueue_scripts', 'pc_events_map_css_files', 666 );

    function pc_events_map_css_files() {

		if ( is_singular( EVENTS_POST_SLUG ) ) {
			wp_enqueue_style( 'leaflet-styles', get_stylesheet_directory_uri().'/scripts/include/leaflet.min.css', null, null, 'screen' );
		}

	}


/*=====  FIN Dépandances map  =====*/