<?php
/**
 * 
 * [PC] Événements : template résumé
 * 
 */


/*----------  Date  ----------*/

add_action( 'pc_post_card_after_title', 'pc_events_display_card_date', 10 );

    function pc_events_display_card_date( $pc_post ) {

		if ( EVENTS_POST_SLUG == $pc_post->type ) {

			$metas = $pc_post->metas;

			$date_start = new DateTime( $metas['event-date-start'] );
			$date_end = new DateTime( $metas['event-date-end'] );

			if ( $date_start == $date_end ) {
				echo '<time class="st-date" datetime="'.$date_start->format('c').'">Le <span>'.date_i18n( 'j F Y', strtotime($metas['event-date-start']) ).'</span></time>';
			} else {
				echo '<p class="st-date">Du <time datetime="'.$date_start->format('c').'"><span>'.date_i18n( 'j F Y', strtotime($metas['event-date-start']) ).'</span></time> au <time datetime="'.$date_end->format('c').'"><span>'.date_i18n( 'j F Y', strtotime($metas['event-date-end']) ).'</span></time>';
			}


		}

	}

/*----------  Catégories  ----------*/

add_action( 'pc_post_card_before_end', 'pc_events_display_card_tax', 10 );

    function pc_events_display_card_tax( $pc_post ) {		

		if ( EVENTS_POST_SLUG == $pc_post->type ) {

			$terms = wp_get_post_terms( $pc_post->id, EVENTS_TAX_SLUG );

			if ( count( $terms ) > 0 ) {

				global $settings_pc;

				echo '<p class="st-tax">'.pc_svg('tag');
					foreach ( $terms as $key => $term ) {

						$archive_filter = ( isset( $_GET['eventarchive'] ) ) ? '&eventarchive=1' : '' ;

						switch ( $settings_pc['events-tax'] ) {
							case 'filters':
								global $event_archive_permalink;
								$term_link = $event_archive_permalink.'?eventtax='.$term->term_id.$archive_filter;
								break;
							case 'pages':
								$term_link = get_term_link( $term->term_id ).$archive_filter;
								break;
						}

						if ( $key > 0 ) { echo ', '; }
						echo '<a href="'.$term_link.'" title="Catégorie '.$term->name.'">'.$term->name.'</a>';

					}
				echo '</p>';

			}

		}

	}