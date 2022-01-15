<?php
/**
 * 
 * [PC] Événements template : template archive
 * 
 */  

 
global $settings_pc, $settings_project, $pc_post, $events_query, $events_page_number;

$event_archive_permalink = $pc_post->permalink; // page courante

// page en cours (pager)
$events_page_number = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

/*=============================
=            Query            =
=============================*/

$today = date('Y-m-d');

$events_query_args = array(
    'post_type' => EVENTS_POST_SLUG,
    'posts_per_page' => get_option( 'posts_per_page' ),
    'paged' => $events_page_number,
	'order' => 'ASC',
	'orderby' => 'meta-value-date',
	'meta_key' => 'event-date-start',
);


/*----------  À venir / archive  ----------*/

if ( '' != get_query_var('eventarchive') ) {

	// événements passés
	$events_query_args['meta_query'] = array(
		array(
			'key'     => 'event-date-end',
			'value'   => $today,
			'type'	  => 'DATE',
			'compare' => '<',
		)
	);

} else {

	// événements à venir
	$events_query_args['meta_query'] = array(
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
	);

}

/*----------  Taxonomie (filtre)  ----------*/

if ( 'filters' == $settings_pc['events-tax'] && '' != get_query_var('eventtax') ) {

	$events_query_args['tax_query'] = array(
        array(
            'taxonomy' => EVENTS_TAX_SLUG,
            'field'    => 'term_id',
            'terms'    => sanitize_key( get_query_var('eventtax') ),
        ),
    );

}


/*=====  FIN Query  =====*/

/*=================================
=            Affichage            =
=================================*/

$events_query = new WP_Query( $events_query_args );

if ( $events_query->have_posts() ) {

	/*----------  Filtres  ----------*/
	
	if ( '' != $settings_pc['events-tax'] ) { pc_events_display_filters( $pc_post ); }


	/*----------  Liste  ----------*/

	// données structurées
	$events_schema = array(
		'@context' => 'http://schema.org/',
		'@type'=> 'CollectionPage',
		'name' => $pc_post->get_seo_meta_title(),
		'headline' => $pc_post->get_seo_meta_title(),
		'description' => $pc_post->get_seo_meta_description(),
		'mainEntity' => array(
			'@type' => 'ItemList',
			'itemListElement' => array()
		),
		'isPartOf' => pc_get_schema_website()
	);
	// compteur position itemListElement
	$events_list_item_key = 1;

	echo '<ul class="st-list st-list--events reset-list">';

	// affichage des actus
    while ( $events_query->have_posts() ) { $events_query->the_post();
		
		// début d'élément
		echo '<li class="st st--event">';

			$events_post = new PC_Post( $events_query->post );

			// affichage résumé
			$events_post->display_card();
			// données structurées
			$events_schema['mainEntity']['itemListElement'][] = $events_post->get_schema_list_item( $events_list_item_key );
			$events_list_item_key++;
		
		// fin d'élément
		echo '</li>';

	}
	
	echo '</ul>';

	echo '<div class="events-archive-toggle">';
		if ( '' == get_query_var('eventarchive') ) {
			echo '<a href="'.$pc_post->permalink.'?eventarchive=1" class="button" title="Afficher les événements passés"><span class="ico">'.pc_svg('more-s').'</span><span class="txt">Archives</span></a>';
		} else {
			echo '<a href="'.$pc_post->permalink.'" class="button"><span class="ico">'.pc_svg('more-s').'</span><span class="txt">Événements à venir</span></a>';
		}
	echo '</div>';

	// affichage données structurées
	echo '<script type="application/ld+json">'.json_encode($events_schema,JSON_UNESCAPED_SLASHES).'</script>';
	

	/*----------  Pagination  ----------*/
	
	add_action( 'pc_action_page_main_footer', 'pc_events_display_archive_pager', 65 );

		function pc_events_display_archive_pager() {
			
			global $events_query, $events_page_number;

			if ( $events_query->found_posts > get_option( 'posts_per_page' ) ) {
				pc_get_pager( $events_query, $events_page_number );
			}
			
		}
    

/*----------  Pas de résultat  ----------*/

} else {
	
	// rien d'archivé dans la catégorie
	if ( '' != get_query_var('eventarchive') && '' != get_query_var('eventtax') ) {
		$no_result_term = get_term_by( 'ID', get_query_var('eventtax'), EVENTS_TAX_SLUG );
		$no_result = 'Il n\'y a <strong>pas d\'événements</strong> archivés dans la catégorie <strong>'.$no_result_term->name.'</strong>, vous pouvez <a class="button button--inner-txt" href="'.$event_archive_permalink.'?eventtax='.get_query_var('eventtax').'">consulter les événements à venir</a>';

	// rien d'archivé
	} else if ( '' != get_query_var('eventarchive') && '' == get_query_var('eventtax') ) {
		$no_result = 'Il n\'y a <strong>pas d\'événements</strong> archivés, vous pouvez <a class="button button--inner-txt" href="'.$event_archive_permalink.'">consulter les événements à venir</a>.';

	// rien à venir dans la catégorie
	} else if ( '' == get_query_var('eventarchive') && '' != get_query_var('eventtax') ) {
		$no_result_term = get_term_by( 'ID', get_query_var('eventtax'), EVENTS_TAX_SLUG );
		$no_result = 'Il n\'y a <strong>pas d\'événements</strong> à venir dans la catégorie <strong>'.$no_result_term->name.'</strong>, vous pouvez <a class="button button--inner-txt" href="'.$event_archive_permalink.'?eventarchive=1&eventtax='.get_query_var('eventtax').'">consulter les archives</a>.';

	// rien à venir
	} else {
		$no_result = 'Il n\'y a <strong>pas d\'événements</strong> à venir, vous pouvez <a class="button button--inner-txt" href="'.$event_archive_permalink.'?eventarchive=1">consulter les archives</a>.';

	}

	echo pc_display_alert_msg( $no_result, 'success' );

}
 
 
 /*=====  FIN Affichage  =====*/
 
 // reset query
 wp_reset_postdata();