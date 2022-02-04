<?php

/**
 * 
 * [PC] Événements : administration
 * 
 ** Options du plugin (settings)
 ** Option de page (arhive)
 ** Accueil (settings)
 ** Liste d'article
 * 
 */



/*=======================================================
=            Options du plugin (PC Réglages)            =
=======================================================*/

add_filter( 'pc_filter_settings_pc_fields', 'pc_events_edit_settings_pc_fields' );

	function pc_events_edit_settings_pc_fields( $settings_pc_fields ) {

		$settings_pc_fields[] = array(
			'title'     => 'Événements',
			'id'        => 'events',
			'prefix'    => 'events',
			'fields'    => array(
				array(
					'type'      => 'radio',
					'label_for' => 'tax',
					'label'     => 'Catégories',
					'options'   => array(
						'Sans' => 'none',
						'Filtres' => 'filters',
						'Pages' => 'pages'
					),
					'default' => 'none'
				)
			)
		);

		return $settings_pc_fields;

	}


/*=====  FIN Options du plugin (PC Réglages)  =====*/

/*======================================
=            Option de page            =
======================================*/
    
add_filter( 'pc_filter_settings_project', 'pc_events_edit_settings_project' );

    function pc_events_edit_settings_project( $settings ) {

		$settings['page-content-from'][EVENTS_POST_SLUG] = array(
			'Événements',
			dirname( __FILE__ ).'/templates/archive.php'
		);

        return $settings;
        
	}
	
// sauf si déjà publié
add_filter( 'pc_filter_page_metabox_select_content_from', 'pc_events_edit_page_metabox_select_content_from', 10, 2 );

	function pc_events_edit_page_metabox_select_content_from( $select, $post ) {

		$events_archive = pc_get_page_by_custom_content( EVENTS_POST_SLUG, 'object' );

		if( is_object( $events_archive ) && $events_archive->ID != $post->ID ) {

			unset( $select[EVENTS_POST_SLUG] );

		}

		return $select;

	}


/*=====  FIN Archive (option de page)  =====*/

/*===============================
=            Accueil            =
===============================*/

add_filter( 'pc_filter_settings_home_fields', 'pc_events_edit_settings_home_fields', 20 );

	function pc_events_edit_settings_home_fields( $fields ) {

		$events_title = array(
			'type'      => 'text',
			'label_for' => 'events-title',
			'label'     => 'Titre des événements',
			'css'       => 'width:100%'
		);

		$fields[0]['fields'][] = $events_title;

		return $fields;

	}


/*=====  FIN Accueil  =====*/

/*=======================================
=            Liste d'article            =
=======================================*/

/*----------  Actions groupées  ----------*/

add_filter( 'bulk_actions-edit-'.EVENTS_POST_SLUG, 'pc_events_edit_bluk_actions' );

	function pc_events_edit_bluk_actions( $actions ) {

		unset($actions['edit']);
		return $actions;

	}

/*----------  Colonne visuel  ----------*/

// reprise WPréform
add_action( 'manage_'.EVENTS_POST_SLUG.'_posts_columns', 'pc_page_edit_manage_posts_columns', 10, 2);
add_action( 'manage_'.EVENTS_POST_SLUG.'_posts_custom_column', 'pc_page_manage_posts_custom_column', 10, 2);


/*=====  FIN Liste d'article  =====*/