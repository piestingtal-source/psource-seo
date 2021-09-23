<?php



/* Add settings page */

function wds_seoopt_settings() {

	$name = 'wds_onpage';

	$title =  __( 'Titel & Meta' , 'wds' );

	$description = sprintf( __( '<p>Ändere die Felder unten, um die Titel und Meta-Beschreibungen Deiner Webseitenseiten anzupassen. <a href="https://n3rds.work/docs/ps-seo-handbuch#ps-seo-makros" target="_blank">Klicke hier, um die Liste der unterstützten Makros anzuzeigen.</a></p>

	<p>Suchmaschinen lesen den Titel und die Beschreibung für jedes Element Deiner Webseite. Die folgenden Felder werden durch Makros festgelegt, um Standardinformationen einzugeben. Du kannst sie nach Deinen Wünschen anpassen und auf die unterstützten Makros verweisen, indem Du auf die Schaltfläche Hilfe klickst.</p>

	<p>Es scheint allgemein anerkannt zu sein, dass die Meta-Tags "Titel" und "Beschreibung" wichtig sind, um effektiv zu schreiben, da sie von mehreren großen Suchmaschinen in ihren Indizes verwendet werden. Verwende relevante Schlüsselwörter in Deinem Titel und variiere die Titel auf den verschiedenen Seiten Deiner Webseite, um so viele Schlüsselwörter wie möglich zu finden. Das Meta-Tag "Beschreibung" wird von einigen Suchmaschinen als kurze Zusammenfassung Ihrer URL verwendet. Stelle daher sicher, dass Deine Beschreibung Suchende auf Deine Webseite lockt.</p>

	<p>Das Meta-Tag "Beschreibung" wird im Allgemeinen als das wertvollste und am wahrscheinlichsten indizierte angesehen. Achte daher besonders auf dieses.</p>

	<p>Hier ist ein Beispiel, wie es in WMS N@W angepasst wurde.</p>

	<p>Die Seiten-Beschreibung (Slogan) lautet:

	<blockquote>WMS N@W, Multisite & BuddyPress</blockquote>

	Es wurde jedoch so angepasst, dass die Home-Meta-Beschreibung "WMS N@W Premium" lautet:

	<blockquote>widmet sich Plugins, Themen, Ressourcen und Support, um Dich bei der Erstellung der absolut besten WordPress MU (WPMU)-Seite zu unterstützen, die Du kannst.</blockquote>

	<p><img src="%s" alt="title and description sample" /></p>

	<p>Dieses Plugin fügt außerdem ein PSOURCE SEO-Modul unter dem Editor "Beitrag schreiben/Seite schreiben" hinzu, mit dem Du die SEO-Optionen für einzelne Beiträge und Seiten anpassen kannst.</p>' , 'wds' ), WDS_PLUGIN_URL . 'images/onpagesample.png' );



	$fields = array();

	if ( WDS_SITEWIDE || 'posts' == get_option('show_on_front') ) {

		$fields['home'] = array(

			'title' =>  __( 'Startseite' , 'wds' ),

			'intro' =>  '',

			'options' => array(

				array(

					'type' => 'text',

					'name' => 'title-home',

					'title' =>  __( 'Startseiten Titel' , 'wds' ),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-home',

					'title' =>  __( 'Startseiten Meta Beschreibung' , 'wds' ),

					'description' => ''

				),

				array(

					'type' => 'text',

					'name' => 'keywords-home',

					'title' =>  __( 'Schlüsselwörter für die Startseite' , 'wds' ),

					'description' => __('Durch Kommas getrennte Schlüsselwörter, z.B. <code>wort1,wort2</code>', 'wds'),

				),

				array(

					'type' => 'checkbox',

					'name' => 'meta_robots-main_blog_archive',

					'title' => __( 'Hauptblog-Archiv Meta Robots' , 'wds'),

					'items' => array(

						"meta_robots-noindex-main_blog_archive" => __('Kein Index', 'wds'),

						"meta_robots-nofollow-main_blog_archive" => __('Nicht Folgen', 'wds'),

						"meta_robots-main_blog_archive-subsequent_pages" => __('Lasse die erste Seite in Ruhe, aber wende sie auf nachfolgende Seiten an', 'wds'),

					),

				)

			)

		);

	} else {

		$intro = '<p>' . __('Du scheinst eine statische Startseite zu verwenden. Du kannst die SEO-Einstellungen mithilfe der PSOURCE SEO-Metabox in Deinem Seiteneditor anpassen.', 'wds') . '</p>';

		if ((int)get_option('page_for_posts')) {

			$intro .= '<p>' . __('Du kannst dasselbe für Deine ausgewählte Beitragsseite tun', 'wds') . '</p>';

		}

		$fields['home'] = array(

			'title' => __('Startseite', 'wds'),

			'intro' => $intro,

			'options' => array(),

		);

	}

	foreach (get_post_types() as $posttype) {

		if (in_array($posttype, array('revision','nav_menu_item'))) continue;

		if (isset($wds_options['redirectattachment']) && $wds_options['redirectattachment'] && $posttype == 'attachment') continue;



		$type_obj = get_post_type_object($posttype);

		if (!is_object($type_obj)) continue;



		$fields[$posttype] = array(

			'title' => $type_obj->labels->name,

			'intro' => '',

			'options' => array(

				array(

					'type' => 'text',

					'name' => 'title-' . $posttype,

					'title' => sprintf(__( '%s Titel' , 'wds'), $type_obj->labels->singular_name ),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-' . $posttype,

					'title' => sprintf(__( '%s Meta Beschreibung' , 'wds'), $type_obj->labels->singular_name),

					'description' => ''

				)

			)

		);

	}



	foreach (get_taxonomies(array('_builtin'=>false),'objects') as $taxonomy) {

		$fields[$taxonomy->name] = array(

			'title' => $taxonomy->label,

			'intro' => '',

			'options' => array(

				array(

					'type' => 'text',

					'name' => 'title-' . $taxonomy->name,

					'title' => sprintf( __( '%s Titel' , 'wds'), ucfirst( $taxonomy->label ) ),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-' . $taxonomy->name,

					'title' => sprintf( __( '%s Meta Beschreibung' , 'wds'), ucfirst( $taxonomy->label ) ),

					'description' => ''

				),

				array(

					'type' => 'checkbox',

					'name' => 'meta_robots-' . $taxonomy->name,

					'title' => sprintf( __( '%s Meta Robots' , 'wds'), ucfirst( $taxonomy->label ) ),

					'items' => array(

						"meta_robots-noindex-{$taxonomy->name}" => __('Kein Index', 'wds'),

						"meta_robots-nofollow-{$taxonomy->name}" => __('Nicht folgen', 'wds'),

						"meta_robots-{$taxonomy->name}-subsequent_pages" => __('Lasse die erste Seite in Ruhe, aber wende sie auf nachfolgende Seiten an', 'wds'),

					),

				)

			)

		);

	}

	// Adding the builtin ones we need

	$fields['category'] = array(

		'title' => __('Beitrags-Kategorien', 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'name' => 'title-category',

				'title' => __( 'Kategorietitel' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'textarea',

				'name' => 'metadesc-category',

				'title' => __( 'Kategorie Meta Beschreibung' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'checkbox',

				'name' => 'meta_robots-category',

				'title' => sprintf(__( 'Kategorie Metaroboter' , 'wds')),

				'items' => array(

					"meta_robots-noindex-category" => __('Kein Index', 'wds'),

					"meta_robots-nofollow-category" => __('Nicht folgen', 'wds'),

					"meta_robots-category-subsequent_pages" => __('Lasse die erste Seite in Ruhe, aber wende sie auf nachfolgende Seiten an', 'wds'),

				),

			)

		)

	);

	$fields['post_tag'] = array(

		'title' => __('Post Tags', 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'name' => 'title-post_tag',

				'title' => __( 'Tag Title' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'textarea',

				'name' => 'metadesc-post_tag',

				'title' => __( 'Tag Meta Description' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'checkbox',

				'name' => 'meta_robots-post_tag',

				'title' => sprintf(__( 'Tag Meta Robots' , 'wds')),

				'items' => array(

					"meta_robots-noindex-post_tag" => __('Noindex', 'wds'),

					"meta_robots-nofollow-post_tag" => __('Nofollow', 'wds'),

					"meta_robots-post_tag-subsequent_pages" => __('Lasse die erste Seite in Ruhe, aber wende sie auf nachfolgende Seiten an', 'wds'),

				),

			)

		)

	);



	$fields['author'] = array(

		'title' => __( 'Author Archive' , 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'name' => 'title-author',

				'title' => __( 'Author Archive Title' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'textarea',

				'name' => 'metadesc-author',

				'title' => __( 'Author Archive Meta Description' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'checkbox',

				'name' => 'meta_robots-author',

				'title' => sprintf(__( 'Author Meta Robots' , 'wds')),

				'items' => array(

					"meta_robots-noindex-author" => __('Noindex', 'wds'),

					"meta_robots-nofollow-author" => __('Nofollow', 'wds'),

					"meta_robots-author-subsequent_pages" => __('Lasse die erste Seite in Ruhe, aber wende sie auf nachfolgende Seiten an', 'wds'),

				),

			)

		)

	);

	$fields['date'] = array(

		'title' => __( 'Date Archives' , 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'name' => 'title-date',

				'title' => __( 'Date Archives Title' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'textarea',

				'name' => 'metadesc-date',

				'title' => __( 'Date Archives Description' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'checkbox',

				'name' => 'meta_robots-date',

				'title' => sprintf(__( 'Date Meta Robots' , 'wds')),

				'items' => array(

					"meta_robots-noindex-date" => __('Noindex', 'wds'),

					"meta_robots-nofollow-date" => __('Nofollow', 'wds'),

					"meta_robots-date-subsequent_pages" => __('Lasse die erste Seite in Ruhe, aber wende sie auf nachfolgende Seiten an', 'wds'),

				),

			)

		)

	);

	$fields['search'] = array(

		'title' => __( 'Search Page' , 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'name' => 'title-search',

				'title' => __( 'Search Page Title' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'textarea',

				'name' => 'metadesc-search',

				'title' => __( 'Search Page Description' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'checkbox',

				'name' => 'meta_robots-search',

				'title' => __( 'Search results Meta Robots' , 'wds'),

				'items' => array(

					"meta_robots-noindex-search" => __('Noindex', 'wds'),

					"meta_robots-nofollow-search" => __('Nofollow', 'wds'),

				),

			)

		)

	);

	$fields['404'] = array(

		'title' => __( '404 Page' , 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'name' => 'title-404',

				'title' => __( '404 Page Title' , 'wds'),

				'description' => ''

			),

			array(

				'type' => 'textarea',

				'name' => 'metadesc-404',

				'title' => __( '404 Page Description' , 'wds'),

				'description' => ''

			)

		)

	);

	// BuddyPress groups

	if (function_exists('groups_get_groups') && (is_network_admin() || is_main_site())) {

		$fields['bp_groups'] = array(

			'title' => __( 'BuddyPress Groups' , 'wds'),

			'intro' => '',

			'options' => array(

				array(

					'type' => 'text',

					'name' => 'title-bp_groups',

					'title' => __( 'BuddyPress Group Title' , 'wds'),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-bp_groups',

					'title' => __( 'BuddyPress Group Description' , 'wds'),

					'description' => ''

				)

			)

		);

	}

	// BuddyPress profiles

	if (defined('BP_VERSION') && (is_network_admin() || is_main_site())) {

		$fields['bp_profile'] = array(

			'title' => __( 'BuddyPress Profile' , 'wds'),

			'intro' => '',

			'options' => array(

				array(

					'type' => 'text',

					'name' => 'title-bp_profile',

					'title' => __( 'BuddyPress Profile Title' , 'wds'),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-bp_profile',

					'title' => __( 'BuddyPress Profile Description' , 'wds'),

					'description' => ''

				)

			)

		);

	}

	// PSeCommerce global products/taxonomies

	if (class_exists('PSeCommerce_MS') && (is_network_admin() || is_main_site())) {

		$fields['mp_psecommerce'] = array(

			'title' => __( 'PSeCommerce Marktplatz' , 'wds'),

			'intro' => '',

			'options' => array(

			// Base

				array(

					'type' => 'text',

					'name' => 'title-mp_psecommerce-base',

					'title' => __( 'Marktplatz-Basistitel' , 'wds'),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-mp_marketplace-base',

					'title' => __( 'Beschreibung der Marktplatzbasis' , 'wds'),

					'description' => ''

				),

			// Global Categories

				array(

					'type' => 'text',

					'name' => 'title-mp_marketplace-categories',

					'title' => __( 'Marktplatzkategorien Titel' , 'wds'),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-mp_marketplace-categories',

					'title' => __( 'Beschreibung der Marktplatzkategorien' , 'wds'),

					'description' => ''

				),

				// ...

			/// Global Tags

				array(

					'type' => 'text',

					'name' => 'title-mp_marketplace-tags',

					'title' => __( 'Marktplatz Tags Titel' , 'wds'),

					'description' => ''

				),

				array(

					'type' => 'textarea',

					'name' => 'metadesc-mp_marketplace-tags',

					'title' => __( 'Marktplatz Tags Beschreibung' , 'wds'),

					'description' => ''

				),

				// ...

			)

		);

	}



		WDS_Core_Admin_Tabs::register('3', $name, $title, $description, $fields);

}

add_action( 'init', 'wds_seoopt_settings', 999 ); // Ensure we're registered late enough



/* Default settings */

function wds_seoopt_defaults() {

	if( is_multisite() && WDS_SITEWIDE == true ) {

		$onpage_options = get_site_option( 'wds_onpage_options' );

	} else {

		$onpage_options = get_option( 'wds_onpage_options' );

	}



	if ( empty($onpage_options['title-home']) )

		$onpage_options['title-home'] = '%%sitename%%';



	if ( empty($onpage_options['metadesc-home']) )

		$onpage_options['metadesc-home'] = '%%sitedesc%%';



	if ( empty($onpage_options['title-post']) )

		$onpage_options['title-post'] = '%%title%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-post']) )

		$onpage_options['metadesc-post'] = '%%excerpt%%';



	if ( empty($onpage_options['title-page']) )

		$onpage_options['title-page'] = '%%title%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-page']) )

		$onpage_options['metadesc-page'] = '%%excerpt%%';



	if ( empty($onpage_options['title-attachment']) )

		$onpage_options['title-attachment'] = '%%title%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-attachment']) )

		$onpage_options['metadesc-attachment'] = '%%caption%%';



	if ( empty($onpage_options['title-category']) )

		$onpage_options['title-category'] = '%%category%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-category']) )

		$onpage_options['metadesc-category'] = '%%category_description%%';



	if ( empty($onpage_options['title-post_tag']) )

		$onpage_options['title-post_tag'] = '%%tag%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-post_tag']) )

		$onpage_options['metadesc-post_tag'] = '%%tag_description%%';



	if ( empty($onpage_options['title-author']) )

		$onpage_options['title-author'] = '%%name%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-author']) )

		$onpage_options['metadesc-author'] = '';



	if ( empty($onpage_options['title-date']) )

		$onpage_options['title-date'] = '%%currentdate%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-date']) )

		$onpage_options['metadesc-date'] = '';



	if ( empty($onpage_options['title-search']) )

		$onpage_options['title-search'] = '%%searchphrase%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-search']) )

		$onpage_options['metadesc-search'] = '';



	if ( empty($onpage_options['title-404']) )

		$onpage_options['title-404'] = 'Page not found | %%sitename%%';



	if ( empty($onpage_options['metadesc-404']) )

		$onpage_options['metadesc-404'] = '';



	if ( empty($onpage_options['title-bp_groups']) )

		$onpage_options['title-bp_groups'] = '%%bp_group_name%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-bp_groups']) )

		$onpage_options['metadesc-bp_groups'] = '%%bp_group_description%%';



	if ( empty($onpage_options['title-bp_profile']) )

		$onpage_options['title-bp_profile'] = '%%bp_user_username%% | %%sitename%%';



	if ( empty($onpage_options['metadesc-bp_profile']) )

		$onpage_options['metadesc-bp_profile'] = '%%bp_user_full_name%%';



	if( is_multisite() && WDS_SITEWIDE == true ) {

		update_site_option( 'wds_onpage_options', $onpage_options );

	} else {

		update_option( 'wds_onpage_options', $onpage_options );

	}



}

add_action( 'init', 'wds_seoopt_defaults' );