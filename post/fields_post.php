<?php
/**
 * 
 * [PC] Événements :  champs du post
 * 
 ** Information générales
 ** Dates & heures
 ** Lieu
 * 
 */


if ( class_exists('PC_Add_metabox') ) {

/*==============================================
=            Informations générales            =
==============================================*/

$metabox_event_status_fields = array(
	'prefix'        => 'event-infos',
	'fields'        => array(
		array(
			'type'      => 'checkbox',
			'label'     => 'Annulé',
			'id'        => 'canceled'
		),
		array(
			'type'      => 'checkbox',
			'label'     => 'En distanciel',
			'id'        => 'online',
			'desc'		=> '<strong>Masque la carte</strong> aux internautes, pour autant <strong>l\'adresse reste obligatoire</strong> pour valider l\'événement auprès des moteurs de recherche.'
		),
		array(
			'type'      => 'number',
			'label'     => 'Tarif',
			'id'        => 'price',
			'attr'		=> 'min="0" step=".01" placeholder="0"'
		),
		array(
			'type'      => 'text',
			'label'     => 'Interprète',
			'id'        => 'performer',
			'css'		=> 'width:100%',
			'desc'		=> 'Facultatif : animateur, musicien, groupe musical, comédien,...<br/><strong>Si non renseigné, votre nom est utilisé.</strong> Si plusieurs, les séparer par un caractère "|" (séparateur vertical).'
		)
	)
);

$metabox_event_status_fields = apply_filters( 'pc_filter_metabox_event_status_fields', $metabox_event_status_fields );

$register_metabox_event_status = new PC_Add_Metabox( EVENTS_POST_SLUG, 'Informations générales', 'page-metabox-event-infos', $metabox_event_status_fields, 'normal', 'high' );


/*=====  FIN Informations générales  =====*/

/*======================================
=            Dates & heures            =
======================================*/

$metabox_event_dates_fields = array(
	'prefix'        => 'event-date',
	'desc'			=> '<p><strong>Important : saisissez des valeurs cohérentes</strong>, étant donné qu\'une date de fin est obligatoire pour valider l\'événement par les moteurs de recherche, nous ne sommes pas en mesure d\'anticiper leur analyse si ces 2 valeurs sont strictement exactes (date et heure) ou séparées d\'une minute.</p><p><strong>Important : 0h00 représente le début d\'une journée</strong> et non "minuit".</p>',	
	'fields'        => array(
		array(
			'type'      => 'datetime-local',
			'label'     => 'Début',
			'id'        => 'start',
			'required'	=> true
		),
		array(
			'type'      => 'datetime-local',
			'label'     => 'Fin',
			'id'        => 'end',
			'attr'		=> 'data-after="event-date-start"',
			'required'	=> true
		),
		array(
			'type'      => 'text',
			'label'     => 'Affichage',
			'id'        => 'display',
			'css'		=> 'width:100%',
			'desc'		=> 'Vous pouvez remplacer l\'affichage des dates dans l\'article par ce texte libre. Pour autant les dates seront toujours la référence pour les moteurs de recherche.'
		)
	)
);

$metabox_event_dates_fields = apply_filters( 'pc_filter_metabox_event_dates_fields', $metabox_event_dates_fields );

$register_metabox_event_dates = new PC_Add_Metabox( EVENTS_POST_SLUG, 'Dates & horaires', 'page-metabox-event-dates', $metabox_event_dates_fields, 'normal', 'high' );


/*=====  FIN Dates & heures  =====*/

} // FIN if class_exist();


/*============================
=            Lieu            =
============================*/

add_action( 'add_meta_boxes', 'pc_events_custom_metabox' );

	function pc_events_custom_metabox() {

		add_meta_box(
			'event-location',
			'Lieu',
			'pc_events_metabox_location_content',
			EVENTS_POST_SLUG,
			'normal',
			'high'
		);

	}


/*----------  Contenu  ----------*/

function pc_events_metabox_location_content( $post ) {

	$event_location_name = get_post_meta( $post->ID, 'event-location-name', true );
	$event_address = get_post_meta( $post->ID, 'event-address', true );
	$event_cp = get_post_meta( $post->ID, 'event-cp', true );
	$event_city = get_post_meta( $post->ID, 'event-city', true );
	$event_lat = get_post_meta( $post->ID, 'event-lat', true );
	$event_lng = get_post_meta( $post->ID, 'event-lng', true );
	
    // input hidden de vérification pour la sauvegarde
	wp_nonce_field( basename( __FILE__ ), 'none-event-place-metaboxe' );

	echo '<table class="form-table pc-address-to-gps"><tbody>';
    // nom
    echo '<tr><th><label for="event-location-name">Nom <span class="label-required"> *</span></label></th><td>';
    echo '<input type="text" id="event-location-name" name="event-location-name" value="'.$event_location_name.'" required style="width:100%" />';
    echo '</td></tr>';
    // adresse
    echo '<tr><th><label for="event-address">Adresse <span class="label-required"> *</span></label></th><td>';
    echo '<input type="text" class="address" id="event-address" name="event-address" value="'.$event_address.'" required style="width:100%" />';
    echo '</td></tr>';
    // code postal
    echo '<tr><th><label for="event-cp">Code Postal <span class="label-required"> *</span></label></th><td>';
    echo '<input type="number" class="cp" id="event-cp" min="0" max="99999" name="event-cp" value="'.$event_cp.'" required />';
    echo '</td></tr>';
    // Ville
    echo '<tr><th><label for="event-city">Ville <span class="label-required"> *</span></label></th><td>';
    echo '<input type="text" class="city" id="event-city" name="event-city" value="'.$event_city.'" required style="width:100%" />';
    echo '</td></tr>';
    // Map
    echo '<tr><td colspan="2" style="padding:15px 10px 5px 0">';
    echo '<div style="display:flex;justify-content:space-between;flex-wrap:wrap"><div style="margin-bottom:10px"><button type="button" class="button">Générer les coordonnées GPS</button></div><div><label for="event-lat">Latitude</label> <input type="number" step="any" class="lat" id="event-lat" name="event-lat" value="'.$event_lat.'" required /> <label for="event-lng">Longitude</label> <input type="number" step="any" class="lng" id="event-lng" name="event-lng" value="'.$event_lng.'" required /></div></div>';
	echo '<p class="description"><em>Déplacez le marqueur pour affiner sa position.</em></p>';
    echo '</td></tr>';

    echo '</tbody></table>';


}


/*----------  Sauvegarde  ----------*/

add_action( 'save_post', 'pc_events_metabox_dates_save' );

    function pc_events_metabox_dates_save( $post_ID ) {

        // check input hidden de vérification
        if ( isset($_POST['none-event-place-metaboxe']) && wp_verify_nonce( $_POST['none-event-place-metaboxe'], basename( __FILE__ ) ) ) {

            $fields = array(
                'event-location-name' => $_POST['event-location-name'],	
                'event-address' => $_POST['event-address'],
                'event-cp' => $_POST['event-cp'],
                'event-city' => $_POST['event-city'],
                'event-lat' => $_POST['event-lat'],
                'event-lng' => $_POST['event-lng']
            );

            foreach ($fields as $name => $value) {

                // valeur renvoyé par le form
                $temp = $value;
                // valeur en bdd
                $save = get_post_meta( $post_ID, $name, true );

                // si une valeur arrive & si rien en bdd
                if ( $temp && '' == $save ) {
                    add_post_meta( $post_ID, $name, $temp, true );

                // si une valeur arrive & différente de la bdd
                } elseif ( $temp && $temp != $save ) {
                    update_post_meta( $post_ID, $name, $temp );

                // si rien n'arrive & si un truc en bdd
                } elseif ( '' == $temp && $save ) {
                    delete_post_meta( $post_ID, $name );
                }

            };
        }

    } // FIN save_metabox_fields()


/*=====  FIN Lieu  =====*/