<?php
/**
 * 
 * [PC] Événements : filtres par taxonomies
 * 
 */


function pc_events_display_filters( $pc_post ) {

	$post_metas = $pc_post->metas;

	if ( isset( $post_metas['content-from'] ) && EVENTS_POST_SLUG == $post_metas['content-from'] ) {

	global $settings_pc;

	if ( in_array( $settings_pc['events-tax'], array( 'filters', 'pages' ) ) ) {

		$terms = get_terms( EVENTS_TAX_SLUG, array(
			'hide_empty' => true
		) );

		if ( count( $terms ) >  0 ) {

			echo '<nav role="nav" aria-label="Catégories des événements" class="event-filter">';

				echo '<button type="button" id="event-filter-btn" class="event-filter-btn button js-toggle" aria-controls="event-filter-list" aria-expanded="false" title="Afficher/masquer les catégories"><span class="ico">'.pc_svg('tag').'</span><span class="txt">Catégories</button>';

				echo '<ul id="event-filter-list" class="event-filter-list reset-list" aria-hidden="true" aria-labelledby="event-filter-btn" style="display:none">';

					foreach ( $terms as $term ) {

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
						
						echo '<li class="event-filter-item"><a href="'.$term_link.'" class="event-filter-link button button--red">'.$term->name.'</a></li>';

					}

				echo '</ul>';

			echo '</nav>';

		} // FIN if terms

	} // FIN if tax

	} // FIN if content-from 

}