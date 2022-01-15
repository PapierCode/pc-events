<?php
/**
*
* [PC] Événements : création post & taxonomie
*
**/


if ( class_exists( 'PC_Add_Custom_Post' ) ) {

	/*============================
	=            Post            =
	============================*/

	/*----------  Labels  ----------*/

	$events_post_labels = array (
		'name'                  => 'Événements',
		'singular_name'         => 'Événement',
		'menu_name'             => 'Événements',
		'add_new'               => 'Ajouter un événement',
		'add_new_item'          => 'Ajouter un événement',
		'new_item'              => 'Ajouter un événement',
		'edit_item'             => 'Modifier l\'événement',
		'all_items'             => 'Tous les événements',
		'not_found'             => 'Aucun événement',
		'search_items'			=> 'Rechercher'
	);


	/*----------  Configuration  ----------*/

	$events_post_args = apply_filters( 'pc_filter_events_post_args', array(
		'menu_position'     => 27,
		'menu_icon'         => 'dashicons-calendar-alt',
		'show_in_nav_menus' => false,
		'supports'          => array( 'title', 'editor' ),
		'rewrite'			=> array( 'slug' => 'events-evenements'),
		'has_archive'		=> false
	));


	/*----------  Déclaration  ----------*/

	$events_post_declaration = new PC_Add_Custom_Post( EVENTS_POST_SLUG, $events_post_labels, $events_post_args );
	
	
	/*=====  FIN Post  =====*/

	/*=================================
	=            Taxonomie            =
	=================================*/
			
	global $settings_pc;	
	
	if ( in_array( $settings_pc['events-tax'], array( 'filters', 'pages' ) ) ) {

		/*----------  Labels  ----------*/

		$events_tax_labels = array(
			'name'                          => 'Catégories',
			'singular_name'                 => 'Catégories',
			'menu_name'                     => 'Catégories',
			'all_items'                     => 'Toutes les catégories',
			'edit_item'                     => 'Modifier la catégorie',
			'view_item'                     => 'Voir la catégorie',
			'update_item'                   => 'Mettre à jour la catégorie',
			'add_new_item'                  => 'Ajouter une catégorie',
			'new_item_name'                 => 'Ajouter une catégorie',
			'search_items'                  => 'Rechercher une catégorie',
			'popular_items'                 => 'Catégories les plus utilisées',
			'separate_items_with_commas'    => 'Séparer les catégories avec une virgule',
			'add_or_remove_items'           => 'Ajouter/supprimer une catégorie',
			'choose_from_most_used'         => 'Choisir parmis les plus utilisées',
			'not_found'                     => 'Aucune catégorie définie'
		);

		/*----------  Paramètres  ----------*/

		// vide = paramètres par défaut
		$events_tax_args = apply_filters( 'pc_filter_events_tax_args', array(
			'rewrite'   => array( 'slug' => 'evenements-categories' ),
			'show_in_nav_menus' => false,
			'publicly_queryable' => ( 'filters' == $settings_pc['events-tax'] ) ? false : true,
			'hierarchical' => false,
			'meta_box_cb' => 'post_categories_meta_box'
		));


		/*----------  Déclaration  ----------*/

		$events_post_declaration->add_custom_tax(
			EVENTS_TAX_SLUG,
			$events_tax_labels,
			$events_tax_args
		);


	}
	
	
	
	/*=====  FIN Taxonomie  =====*/


} // FIN if class_exists(PC_Add_Custom_Post)
