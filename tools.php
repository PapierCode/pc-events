<?php
/**
 * 
 * [PC] Événements : fonctions communes
 * 
 */


/*========================================
=            Afficher la date            =
========================================*/

function pc_event_display_date( $metas, $css ) {

	$date_start = new DateTime( $metas['event-date-start'] );
	$date_end = new DateTime( $metas['event-date-end'] );

	if ( $date_start->settime(0,0) == $date_end->settime(0,0) ) {

		echo '<time class="'.$css.'" datetime="'.$date_start->format('c').'">Le <span>'.date_i18n( 'j F Y', strtotime($metas['event-date-start']) ).'</span></time>';

	} else {

		echo '<p class="'.$css.'">Du <time datetime="'.$date_start->format('c').'"><span>'.date_i18n( 'j F Y', strtotime($metas['event-date-start']) ).'</span></time> au <time datetime="'.$date_end->format('c').'"><span>'.date_i18n( 'j F Y', strtotime($metas['event-date-end']) ).'</span></time>';

	}

}


/*=====  FIN Afficher la date  =====*/