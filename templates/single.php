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

add_action( 'pc_action_page_main_header', 'pc_events_display_single_date', 40 );

	function pc_events_display_single_date( $pc_post ) {

		if ( is_singular( EVENTS_POST_SLUG ) ) {
			pc_event_display_date( $pc_post->metas, 'event-date' );
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