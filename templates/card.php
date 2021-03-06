<?php
/**
 * 
 * [PC] Événements : template résumé
 * 
 ** Annulation
 ** Date
 ** Catégories
 ** Lien
 * 
 */
 

/*==================================
=            Annulation            =
==================================*/

add_action( 'pc_post_card_after_start', 'pc_events_display_cancel_label', 10 );

	function pc_events_display_cancel_label( $pc_post ) {

		if ( EVENTS_POST_SLUG == $pc_post->type ) {

			$metas = $pc_post->metas;

			if ( isset( $metas['event-infos-canceled'] ) ) {
				echo '<p class="st-label st-label--canceled">';
					echo apply_filters( 'pc_filter_event_card_canceled_text', 'Annulé', $pc_post );
				echo '</p>';
			}

		}

	}


/*=====  FIN Annulation  =====*/

/*============================
=            Date            =
============================*/

add_action( 'pc_post_card_after_title', 'pc_events_display_card_date', 10 );

function pc_events_display_card_date( $pc_post ) {

	if ( EVENTS_POST_SLUG == $pc_post->type ) {

		$metas = $pc_post->metas;
		$date_start = new DateTime( $metas['event-date-start'] );
		$date_end = new DateTime( $metas['event-date-end'] );
		$css = 'st-date';

		if ( $date_start->settime(0,0) == $date_end->settime(0,0) ) {

			echo '<time class="'.$css.'" datetime="'.$date_start->format('c').'">Le <span>'.date_i18n( 'j F Y', strtotime($metas['event-date-start']) ).'</span></time>';

		} else {

			echo '<p class="'.$css.'">Du <time datetime="'.$date_start->format('c').'"><span>'.date_i18n( 'j F Y', strtotime($metas['event-date-start']) ).'</span></time> au <time datetime="'.$date_end->format('c').'"><span>'.date_i18n( 'j F Y', strtotime($metas['event-date-end']) ).'</span></time>';

		}		

	}

}


/*=====  FIN Date  =====*/

/*==================================
=            Catégories            =
==================================*/

add_action( 'pc_post_card_before_end', 'pc_events_display_card_tax', 10, 2 );

function pc_events_display_card_tax( $pc_post, $params ) {	
	
	global $settings_pc;

	if ( EVENTS_POST_SLUG == $pc_post->type && !get_query_var('eventpast') && in_array( $settings_pc['events-tax'], array( 'filters', 'pages' ) ) ) {	

		$terms = wp_get_post_terms( $pc_post->id, EVENTS_TAX_SLUG );

		if ( is_array( $terms ) && !empty( $terms ) ) {

			echo '<p class="st-tax">'.pc_svg('tag');

				foreach ( $terms as $key => $term ) {

					$link_attrs = array(
						'title' => 'Catégorie '.$term->name
					);

					switch ( $settings_pc['events-tax'] ) {
						case 'filters':
							$link_attrs['href'] = $params['archive_permalink'].'?eventtax='.$term->term_id;
							$link_attrs['rel'] = 'nofollow';
							break;
						case 'pages':
							$link_attrs['href'] = get_term_link( $term->term_id );
							break;
					}

					if ( $key > 0 ) { echo ', '; }

					$link_attrs_str = '';
					foreach ( $link_attrs as $name => $values ) {
						$link_attrs_str .= ' '.$name.'="'.$values.'"';
					}
					echo '<a'.$link_attrs_str.'>'.$term->name.'</a>';

				}

			echo '</p>';

		}

	}

}


/*=====  FIN Catégories  =====*/

/*============================
=            Lien            =
============================*/

add_filter( 'pc_filter_card_link_params', 'pc_events_edit_card_link_params', 10, 2 );

	function pc_events_edit_card_link_params( $params, $pc_post ) {

		if ( EVENTS_POST_SLUG == $pc_post->type && get_query_var('paged') ) {
			$params['eventpaged'] = get_query_var('paged');
		} 
		if ( is_tax( EVENTS_TAX_SLUG ) ) {
			$params['eventtax'] = get_queried_object_id();
		}
		if ( get_query_var( 'eventtax' ) ) {
			$params['eventtax'] = get_query_var( 'eventtax' );
		}
		
		return $params;

	}


/*=====  FIN Lien  =====*/