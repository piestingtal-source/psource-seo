<?php



/**

 * BuddyPress settings fields helper.

 */

function _wds_get_buddypress_fields () {

	// BuddyPress Groups

	if (function_exists('groups_get_groups')) { // We have BuddyPress groups, so let's get some settings

		$opts = array(

			'title' => __('BuddyPress', 'wds'),

			'intro' => __('BuddyPress Sitemaps Integration.', 'wds'),

			'options' => array(

				array(

					'type' => 'radio',

					'name' => 'sitemap-buddypress-groups',

					'title' => __('Nimm BuddyPress-Gruppen in meine Sitemaps auf', 'wds'),

					'description' => __('Durch Aktivieren dieser Option werden alle BuddyPress-Gruppen zu Deiner Sitemap hinzugefügt.', 'wds'),

					'items' => array(

						__('Nein', 'wds'), __('Ja', 'wds')

					),

				),

			),

		);

		$groups = groups_get_groups(array('per_page' => WDS_BP_GROUPS_LIMIT));

		$groups = @$groups['groups'] ? $groups['groups'] : array();

		$exclude = array();

		foreach ($groups as $group) {

			$exclude["exclude-buddypress-group-{$group->slug}"] = $group->name;

		}

		if ($exclude) {

			$opts['options'][] = array (

				'type' => 'checkbox',

				'name' => 'sitemap-buddypress',

				'title' => __( 'Schließe diese Gruppen von meiner Sitemap aus' , 'wds'),

				'items' => $exclude,

			);

		}

	}



	// BuddyPress profiles

	$opts['options'][] = array (

		'type' => 'radio',

		'name' => 'sitemap-buddypress-profiles',

		'title' => __('Füge BuddyPress-Profile in meine Sitemaps ein', 'wds'),

		'description' => __('Durch Aktivieren dieser Option werden alle BuddyPress-Profile zu Deiner Sitemap hinzugefügt.', 'wds'),

		'items' => array(

			__('Nein', 'wds'), __('Ja', 'wds')

		),

	);

	$wp_roles = new WP_Roles();

	$wp_roles = $wp_roles->get_names();

	$wp_roles = $wp_roles ? $wp_roles : array();

	$exclude = array();

	foreach ($wp_roles as $key=>$label) {

		$exclude["exclude-profile-role-{$key}"] = $label;

	}

	if ($exclude) {

		$opts['options'][] = array (

			'type' => 'checkbox',

			'name' => 'sitemap-buddypress-roles',

			'title' => __( 'Schließe Profile mit diesen Rollen von meiner Sitemap aus' , 'wds'),

			'items' => $exclude,

		);

	}





	return $opts;

}



/* Add settings page */

function wds_sitemaps_settings() {

	//$name = 'wds_sitemaps'; // Removed plural

	global $wds_options;



	$name = 'wds_sitemap'; // Added singular

	$title = __( 'Sitemaps' , 'wds');

	$description = __( '<p>Hier helfen wir Dir bei der Erstellung einer Sitemap, mit deren Hilfe Suchmaschinen alle Informationen auf Deiner Webseite finden können.</p>

	<p>Dies ist eine der Grundlagen von SEO. Eine Sitemap hilft Suchmaschinen wie Google, Bing und Yahoo, Ihr Blog besser zu indizieren. Suchmaschinen können Deine Webseite besser mit einer strukturierten Sitemap durchsuchen, die zeigt, wohin Deine Inhalte führen. Dieses Plugin unterstützt alle Arten von WordPress-generierten Seiten sowie benutzerdefinierte URLs. Wenn Du einen neuen Beitrag erstellest, werden wichtige Suchmaschinen benachrichtigt, damit diese neue Inhalte crawlen können.</p>

	<p>Du kannst auch festlegen, dass Beiträge, Seiten, benutzerdefinierte Beitragstypen, Kategorien oder Tags nicht in die Sitemap aufgenommen werden. In den meisten Situationen solltest Du diese jedoch belassen.</p>

	<p>(Wenn Du diese von einer Sitemap weglässt, wird nicht garantiert, dass eine Suchmaschine die Informationen nicht auf andere Weise findet!)</p>', 'wds' );



	$sitemap_options = get_option( 'wds_sitemap_options' );



	$fields = array();

	$fields['sitemap'] = array(

		'title' => __( 'XML Sitemap' , 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'text',

				'class' => 'widefat',

				'name' => 'sitemappath',

				'title' => __( 'Pfad zur XML Sitemap' , 'wds'),

				'description' => '',

				'text' => '<p><code>' . $sitemap_options['sitemappath'] . '</code></p>'

			),

			array(

				'type' => 'content',

				'name' => 'sitemapurl',

				'title' => __( 'URL zur XML Sitemap' , 'wds'),

				'description' => '',

				'text' => '<p><a href="' . wds_get_sitemap_url() . '" target="_blank">' . wds_get_sitemap_url() . '</a></p>' // Removed plain content type

			)

		)

	);



	foreach (get_post_types(array(

			'public' => true,

			'show_ui' => true,

		)) as $post_type) {

		if ( !in_array( $post_type, array('revision', 'nav_menu_item', 'attachment') ) ) {

			$pt = get_post_type_object($post_type);

			$post_types['post_types-' . $post_type . '-not_in_sitemap'] = $pt->labels->name;

		}

	}

	foreach (get_taxonomies(array(

			'public' => true,

			'show_ui' => true,

		)) as $taxonomy) {

		if ( !in_array( $taxonomy, array( 'nav_menu', 'link_category', 'post_format' ) ) ) {

			$tax = get_taxonomy($taxonomy);

			$taxonomies['taxonomies-' . $taxonomy . '-not_in_sitemap'] = $tax->labels->name;

		}

	}

	$fields['exclude'] = array(

		'title' => __('Ausschließen' , 'wds'),

		'intro' => '',

		'options' => array(

			array(

				'type' => 'checkbox',

				'name' => 'exclude_post_types',

				'title' => __( 'Beitragstypen ausschließen' , 'wds'),

				'items' => $post_types

			),

			array(

				'type' => 'checkbox',

				'name' => 'exclude_taxonomies',

				'title' => __( 'Taxonomien ausschließen' , 'wds'),

				'items' => $taxonomies

			)

		)

	);

	if (defined('BP_VERSION')) {

		$fields['buddypress'] = _wds_get_buddypress_fields();

	}

	$fields['options'] = array(

		'title' => __('Optionen', 'wds'),

		'intro' => __('Verschiedene Sitemap-bezogene Optionen.', 'wds'),

		'options' => array(

			array(

				'type' => 'radio',

				'name' => 'sitemap-images',

				'title' => __('Füge der Sitemap Bildelemente hinzu', 'wds'),

				'description' => __('Durch Aktivieren dieser Option wird der Speicherverbrauch des Plugins erheblich erhöht.', 'wds'),

				'items' => array(

					__('Nein', 'wds'), __('Ja', 'wds')

				),

			),

			array(

				'type' => 'radio',

				'name' => 'sitemap-stylesheet',

				'title' => __('Füge der generierten Sitemap ein Stylesheet hinzu', 'wds'),

				'description' => __('Stylesheet hat keinerlei Auswirkungen auf die Sitemap-Funktionalität.', 'wds'),

				'items' => array(

					__('Nein', 'wds'), __('Ja', 'wds')

				),

			),

			array(

				'type' => 'radio',

				'name' => 'sitemap-dashboard-widget',

				'title' => __('Dashboard-Widget anzeigen', 'wds'),

				'description' => __('Durch Aktivieren dieser Option wird ein Admin-Dashboard-Widget hinzugefügt, das Deine Sitemap-Informationen anzeigt.', 'wds'),

				'items' => array(

					__('Nein', 'wds'), __('Ja', 'wds')

				),

			),

			array(

				'type' => 'radio',

				'name' => 'sitemap-disable-automatic-regeneration',

				'title' => __('Deaktiviere automatische Sitemap-Updates', 'wds'),

				'description' => __('Aktiviere diese Option nur, wenn Du die Sitemaps manuell aktualisieren möchtest (indem Du das Dashboard-Widget verwendest oder diese Seite besuchst).', 'wds'),

				'items' => array(

					__('Nein', 'wds'), __('Ja', 'wds')

				),

			),

		)

	);

	$google_msg = @$wds_options['verification-google'] ? '<code>' . esc_html('<meta name="google-site-verification" value="') . esc_attr(@$wds_options['verification-google']) . esc_html('" />') . '</code>' : '<small>' . __('Es wird kein META-Tag hinzugefügt', 'wds') . '</small>';

	$bing_msg = @$wds_options['verification-bing'] ? '<code>' . esc_html('<meta name="msvalidate.01" value="') . esc_attr(@$wds_options['verification-bing']) . esc_html('" />') . '</code>' : '<small>' . __('Es wird kein META-Tag hinzugefügt', 'wds') . '</small>';

	$fields['search-engines'] = array(

		'title' => __('Suchmaschinen', 'wds'),

		'intro' => __('Optionen für die direkte Interaktion mit Suchmaschinen.', 'wds'),

		'options' => array(

			array(

				'type' => 'text',

				'class' => 'widefat',

				'name' => 'verification-google',

				'title' => __( 'Google-Bestätigungscode für die Webseite' , 'wds'),

				'description' => "<p>{$google_msg}</p>",

			),

			array(

				'type' => 'text',

				'class' => 'widefat',

				'name' => 'verification-bing',

				'title' => __( 'Bing Bestätigungscode für Webseite' , 'wds'),

				'description' => "<p>{$bing_msg}</p>",

			),

			array(

				'type' => 'radio',

				'name' => 'verification-pages',

				'title' => __('Bestätigungscode hinzufügen zu:', 'wds'),

				'items' => array(

					'' => __('Alle Seiten', 'wds'),

					'home' => __('Startseite', 'wds'),

				),

			),

			array(

				'type' => 'checkbox',

				'name' => 'engines',

				'title' => __('Benachrichtige Suchmaschinen automatisch, wenn meine Sitemap aktualisiert wird' , 'wds'),

				'items' => array(

					'ping-google' => __('Google', 'wds'),

					'ping-bing' => __('Bing', 'wds'),

				),

			),

		)

	);


	if (class_exists('WDS_Core_Admin_Tabs')) WDS_Core_Admin_Tabs::register('2', $name, $title, $description, $fields);



	require_once ( WDS_PLUGIN_DIR . 'wds-sitemaps/wds-sitemaps.php' );

}

add_action( 'init', 'wds_sitemaps_settings', 999 );



/* Default settings */

function wds_sitemaps_defaults() {

	$sitemap_options = get_option( 'wds_sitemap_options' );



	$dir = wp_upload_dir();

	$path = trailingslashit( $dir['basedir'] );



	if ( empty($sitemap_options['sitemappath']) )

		$sitemap_options['sitemappath'] = $path . 'sitemap.xml';



	if ( empty($sitemap_options['sitemapurl']) )

		$sitemap_options['sitemapurl'] = get_bloginfo( 'url' ) . '/sitemap.xml';



	if ( empty($sitemap_options['newssitemappath']) )

		$sitemap_options['newssitemappath'] = $path . 'news_sitemap.xml';



	if ( empty($sitemap_options['newssitemapurl']) )

		$sitemap_options['newssitemapurl'] = get_bloginfo( 'url' ) . '/news_sitemap.xml';



	if ( empty($sitemap_options['enablexmlsitemap']) )

		$sitemap_options['enablexmlsitemap'] = 1;



	update_option( 'wds_sitemap_options', $sitemap_options );

	/*

	if( is_multisite() && WDS_SITEWIDE == true ) {

		update_site_option( 'wds_sitemap_options', $sitemap_options );

	} else {

		update_option( 'wds_sitemap_options', $sitemap_options );

	}

	*/

}

add_action( 'init', 'wds_sitemaps_defaults', 999 );