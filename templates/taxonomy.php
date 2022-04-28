<?php
/**
 * 
 * [PC] Événements : template taxonomie
 * 
 */

get_header();

global $pc_term;
$metas = $pc_term->metas;

pc_display_main_start();

	pc_display_main_header_start();

		pc_display_breadcrumb();

		echo '<h1><span>';
			if ( isset( $metas['content-title'] ) ) {
				echo $metas['content-title'];
			} else {
				echo $pc_term->name;
			}
		echo '</span></h1>';

	pc_display_main_header_end();

	pc_display_main_content_start();

		if ( isset( $metas['content-desc'] ) && !get_query_var( 'paged' ) ) {
			echo pc_wp_wysiwyg( $metas['content-desc'] );
		}

		if ( have_posts() ) { 

			pc_events_display_filters( $pc_term->id );
			
			while ( have_posts() ) { 
				
				the_post();

				// données structurées
				$term_schema = array(
					'@context' => 'http://schema.org/',
					'@type'=> 'CollectionPage',
					'name' => $pc_term->get_seo_meta_title(),
					'headline' => $pc_term->get_seo_meta_title(),
					'description' => $pc_term->get_seo_meta_description(),
					'mainEntity' => array(
						'@type' => 'ItemList',
						'itemListElement' => array()
					),
					'isPartOf' => pc_get_schema_website()
				);
				// compteur position itemListElement
				$events_list_item_key = 1;
		
				echo '<ul class="st-list st-list--events reset-list">';	

					// début d'élément
					echo '<li class="st st--event">';
				
						$event_post = new PC_Post( $post );

						// affichage résumé
						$event_post->display_card();
						// données structurées
						$events_schema['mainEntity']['itemListElement'][] = $event_post->get_schema_list_item( $events_list_item_key );
						$events_list_item_key++;

					// fin d'élément
					echo '</li>';

				echo '</ul>';
		
				// affichage données structurées
				echo '<script type="application/ld+json">'.json_encode($term_schema,JSON_UNESCAPED_SLASHES).'</script>';

			}

		} else {

			$no_result = 'Il n\'y a <strong>pas d\'événements à venir dans cette catégorie</strong>.';
			echo pc_display_alert_msg( $no_result, 'error' );

			pc_events_display_filters( $pc_term->id );

		} // FIN have_post()

	pc_display_main_content_end();

	pc_display_main_footer_start();

		pc_get_pager();
		pc_display_share_links();

	pc_display_main_footer_end();


pc_display_main_end();

get_footer();