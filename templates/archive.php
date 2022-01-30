<?php
/**
 * 
 * [PC] Événements template : template archive
 * 
 */  

 
global $settings_pc, $pc_post, $events_archive_query, $events_page_number;
$today = date('Y-m-d');

// page en cours (pager)
$events_page_number = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

/*=============================
=            Query            =
=============================*/


$events_archive_query_args = array(
    'post_type' => EVENTS_POST_SLUG,
    'posts_per_page' => get_option( 'posts_per_page' ),
    'paged' => $events_page_number,
	'order' => 'ASC',
	'orderby' => 'meta-value-date',
	'meta_key' => 'event-date-start',
);


/*----------  À venir / archive  ----------*/

if ( get_query_var('eventpast') ) {

	// événements passés
	$events_archive_query_args['meta_query'] = array(
		array(
			'key'     => 'event-date-end',
			'value'   => $today,
			'type'	  => 'DATE',
			'compare' => '<',
		)
	);

} else {

	// événements à venir
	$events_archive_query_args['meta_query'] = array(
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

if ( 'filters' == $settings_pc['events-tax'] && get_query_var('eventtax') ) {

	$events_archive_query_args['tax_query'] = array(
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

$events_archive_query = new WP_Query( $events_archive_query_args );

if ( $events_archive_query->have_posts() ) {

	/*----------  Filtres  ----------*/
	
	pc_events_display_filters( sanitize_key( get_query_var('eventtax'), $pc_post->permalink ),  );


	/*----------  Liste  ----------*/

	// données structurées
	$events_archive_schema = array(
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
    while ( $events_archive_query->have_posts() ) { $events_archive_query->the_post();
		
		// début d'élément
		echo '<li class="st st--event">';

			$pc_post_card = new PC_Post( $events_archive_query->post );

			// affichage résumé
			$pc_post_card->display_card();
			// données structurées
			$events_archive_schema['mainEntity']['itemListElement'][] = $pc_post_card->get_schema_list_item( $events_list_item_key );
			$events_list_item_key++;
		
		// fin d'élément
		echo '</li>';

	}
	
	echo '</ul>';

	// affichage données structurées
	echo '<script type="application/ld+json">'.json_encode($events_archive_schema,JSON_UNESCAPED_SLASHES).'</script>';
	

	/*----------  Pagination  ----------*/
	
	add_action( 'pc_action_page_main_footer', 'pc_events_display_archive_pager', 65 );

		function pc_events_display_archive_pager() {
			
			global $events_archive_query, $events_page_number;

			if ( $events_archive_query->found_posts > get_option( 'posts_per_page' ) ) {
				pc_get_pager( $events_archive_query, $events_page_number );
			}
			
		}
    

/*----------  Pas de résultat  ----------*/

} else {
	
	// pas d'événements passés
	if ( get_query_var('eventpast') ) {
		$no_result = 'Il n\'y a <strong>pas d\'événements passés</strong>, vous pouvez <a class="button button--inner-txt" href="'.$pc_post->permalink.'">consulter les événements à venir</a>.';

	// pas d'événéments à venir
	} else {
		$no_result = 'Il n\'y a <strong>pas d\'événements</strong> à venir, vous pouvez <a class="button button--inner-txt" href="'.$pc_post->permalink.'?eventpast=1">consulter les archives</a>.';

	}

	echo pc_display_alert_msg( $no_result, 'error' );

}
 
 
 /*=====  FIN Affichage  =====*/
 
 // reset query
 wp_reset_postdata();