<?php
/**
 * 
 * [PC] Événements : champs taxonomie
 * 
 ** Contenu
 ** Visuel
 ** Résumé
 ** SEO
 * 
 */


if ( class_exists('PC_add_field_to_tax') ) {

/*===============================
=            Contenu            =
===============================*/

$events_tax_content_fields_args = array(	
	'title'     => 'Contenu',
	'desc'		=> '<p><strong>Saisissez le titre et l\'introduction (150 mots maximum conseillés) qui s\'afficheront avant les événements associés à cette catégorie.</strong></p><p><em><strong>Remarque :</strong> si ce titre n\'est pas saisi, le nom de la catégorie est utilisé. Si l\'introduction n\'est pas saisie, la liste des événements s\'affichera directement sous le titre.</em></p>',
	'prefix'    => 'content',
	'fields'    => array(
		array(
			'type'      => 'text',
			'id'        => 'title',
			'label'     => 'Titre',
			'css'		=> 'width:100%'
		),
		array(
			'type'      => 'wysiwyg',
			'id' 		=> 'desc', 
			'label'     => 'Introduction',
			'options'   => array(
				'media_buttons'	=> false,
				'tinymce'	=> array (
					'toolbar1'	=> 'fullscreen,undo,redo,removeformat,|,bold,italic,strikethrough,superscript,charmap,|,link,unlink',
				)
			)
		)
	)
);

$events_tax_content_fields = new PC_add_field_to_tax(
	EVENTS_TAX_SLUG,
	$events_tax_content_fields_args
);


/*=====  FIN Contenu  =====*/

/*==============================
=            Visuel            =
==============================*/

$events_tax_img_fields_args = array(	
	'title'     => 'Visuel',
	'desc'		=> '<p><strong>Sélectionnez l\'image associée à cette page pour le référencement et le partage sur les réseaux sociaux</strong>.</p><p><em><strong>Remarque :</strong> Si une image n\'est pas sélectionnée, le logo est utilisé.</em></p>',
	'prefix'    => 'visual',
	'fields'    => array(
		array(
			'type'      => 'img',
			'id'        => 'id',
			'label'     => 'Image',
			'options'   => array(
				'btnremove' => true
			)
		)					
	)
);

$events_tax_img_fields = new PC_add_field_to_tax(
	EVENTS_TAX_SLUG,
	$events_tax_img_fields_args
);


/*=====  FIN Visuel  =====*/

/*===========================
=            SEO            =
===========================*/

$events_tax_seo_fields_args = array(	
	'title'     => 'Référencement (SEO) & réseaux sociaux',
	'desc'		=> '<p><strong>Optimisez le titre et la description pour les moteurs de recherche et les réseaux sociaux.</strong></p><p><em><strong>Remarques :</strong> si ce titre n\'est pas saisi, le titre du contenu est utilisé, sinon le nom de la catégorie. Si cette description n\'est pas saisie, les premiers mots de l\'introduction sont utilisés, sinon la description par défaut (cf. Paramètres).</em></p>',
	'prefix'    => 'seo',
	'fields'    => array(
		array(
			'type'      => 'text',
			'id'        => 'title',
			'label'     => 'Titre',
			'attr'      => 'class="pc-counter" data-counter-max="70"',
			'css'		=> 'width:100%'
		),			
		array(
			'type'      => 'textarea',
			'id'        => 'desc',
			'label'     => 'Description',
			'attr'      => 'class="pc-counter" data-counter-max="200"',
			'css'		=> 'width:100%'
		)						
	)
);

$events_tax_seo_fields = new PC_add_field_to_tax(
	EVENTS_TAX_SLUG,
	$events_tax_seo_fields_args
);


/*=====  FIN SEO  =====*/

} // FIN if class_exists('PC_add_field_to_tax')