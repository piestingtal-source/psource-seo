<?php



/* Add admin settings page */

function wds_settings() {

	$name = 'wds_settings';

	$title = __( 'Einstellungen' , 'wds');

	$default_roles = array(

		'manage_network' => __('Superadmin'),

		'list_users' => __('Seitenadmin'),

		'moderate_comments' => __('Editor'),

		'edit_published_posts' => __('Autor'),

		'edit_posts' => __('Teilnehmer'),

	);

	if (!is_multisite()) unset($default_roles['manage_network']);



	$seo_metabox_permission_levels = apply_filters('wds-seo_metabox_permission_levels', $default_roles);

	$seo_metabox_301_permission_levels = apply_filters('wds-seo_metabox_301_permission_levels', $default_roles);

	$urlmetrics_metabox_permission_levels = apply_filters('wds-urlmetrics_metabox_permission_level', $default_roles);

	$description = __( '

		<p>PSOURCE SEO zielt darauf ab, alle SEO-Optionen, die eine Webseite benötigt, in einem einfachen Paket zu erledigen.</p>
		<p>Zusätzlich bieten wir auf unserer Homepage Wissenswerte Quellen zum Thema SEO/SEA an, welche Dich unterstützen dieses Plugin optimal zu nutzen<p>
		<p>Halt es einfach - halt es simpel - arbeite effektiv - PSOURCE SEO!<p>

		<p>Es besteht aus mehreren Komponenten, die Du bei der Arbeit mit unserem einfachen SEO-Einrichtungsassistenten vervollständigst:</p>

		<ul>

			<li><b>Schritt 1 Einstellungen</b>: Hier kannst Du auswählen, welche Schritte Du im SEO-Einrichtungsassistenten aufnehmen möchtest. In den meisten Situationen solltest Du alle vier unten aufgeführten aktiven Komponenten aktiviert lassen.</li>

			<li><b>Schritt 2 XML Sitemap</b>: generiert eine XML-Sitemap, mit deren Hilfe Suchmaschinen Deine Website besser indizieren können.</li>

			<li><b>Schritt 3 Titel & Meta Optimierung</b>: Mit dieser Option kannst Du Titel- und Meta-Tags auf jeder Seite Deiner Webseite optimieren.</li>

			<li><b>Schritt 4 Moz Report</b>: bietet detaillierte und genaue SEO-Informationen zu Deinen Webseiten. Es verwendet die Moz Free API.</li>

			<li><b>Step 5 Automatic Links</b>: Mit dieser Option kannst Du Phrasen in Deinen Beiträgen, Seiten, benutzerdefinierten Beitragstypen und Kommentaren automatisch mit entsprechenden Beiträgen, Seiten, benutzerdefinierten Beitragstypen, Kategorien, Tags, benutzerdefinierten Taxonomien und externen URLs verknüpfen.</li>

		</ul>

	' , 'wds');

	$fields = array(

		'components' => array(

			'title' => __( 'Aktive Komponenten' , 'wds'),

			'intro' => __( 'In den meisten Situationen solltest Du alle vier dieser Komponenten aktiviert lassen.' , 'wds'),

			'options' => array(

				array(

					'type' => 'checkbox',

					'name' => 'active-components',

					'title' => __( 'Aktiviere/deaktiviere die Kontrollkästchen, um einen Schritt zum SEO-Einrichtungsassistenten hinzuzufügen oder daraus zu entfernen' , 'wds'),

					'items' => array(

						'autolinks' => __( 'Automatische Links' , 'wds'),

						'onpage' => __( 'Titel & Meta Optimierung' , 'wds'),

						'seomoz' => __( 'Moz Report' , 'wds'),

						'sitemap' => __( 'XML Sitemap' , 'wds'), // Added singular

					),

					'description' => ''

				)

			)

		),

	);



	$boxes = array();

	if (!(defined('WDS_SEO_METABOX_ROLE') && WDS_SEO_METABOX_ROLE)) {

		$boxes[] = array(

			'title' => __('Zeige SEO Metabox der Rolle', 'wds'),

			'type' => 'dropdown',

			'name' => 'seo_metabox_permission_level',

			'items' => $seo_metabox_permission_levels,

		);

	}

	if (!(defined('WDS_SEO_METABOX_301_ROLE') && WDS_SEO_METABOX_301_ROLE)) {

		$boxes[] = array(

			'title' => __('Zeige in der SEO-Metabox 301-Umleitung zur Rolle an', 'wds'),

			'type' => 'dropdown',

			'name' => 'seo_metabox_301_permission_level',

			'items' => $seo_metabox_301_permission_levels,

		);

	}

	if (!(defined('WDS_URLMETRICS_METABOX_ROLE') && WDS_URLMETRICS_METABOX_ROLE)) {

		$boxes[] = array(

			'title' => __('Zeige Moz Metabox der Rolle', 'wds'),

			'type' => 'dropdown',

			'name' => 'urlmetrics_metabox_permission_level',

			'items' => $urlmetrics_metabox_permission_levels,

		);

	}

	if ($boxes) {

		$fields[] = array(

			'title' => __('Zeige Benutzern Metaboxen an', 'wds'),

			'intro' => __('Dies gilt für das Erstellen/Bearbeiten von Beitragsseiten', 'wds'),

			'options' => $boxes,

		);

	}


	WDS_Core_Admin_Tabs::register('1', $name, $title, $description, $fields);

}

add_action( 'init', 'wds_settings' );



/* Default settings */

function wds_defaults() {

	if( is_multisite() && WDS_SITEWIDE == true ) {

		$defaults = get_site_option( 'wds_settings_options' );

	} else {

		$defaults = get_option( 'wds_settings_options' );

	}



	if( ! is_array( $defaults ) ) {

		$defaults = array(

			'onpage' => 'on', // 'on' instead of 1

			'seo_metabox_permission_level' => (is_multisite() ? 'manage_network_options' : 'list_users'), // Default to highest permission level available

			'autolinks' => 'on', // 'on' instead of 1

			'seomoz' => 'on', // 'on' instead of 1

			'urlmetrics_metabox_permission_level' => (is_multisite() ? 'manage_network_options' : 'list_users'), // Default to highest permission level available

			'seo_metabox_301_permission_level' => (is_multisite() ? 'manage_network_options' : 'list_users'), // Default to highest permission level available

			'sitemap' => 'on', // Added singular. Also, changed to 'on' instead of 1

		);

	}

	apply_filters( 'wds_defaults', $defaults );



	if( is_multisite() && WDS_SITEWIDE == true ) {

		update_site_option( 'wds_settings_options', $defaults );

	} else {

		update_option( 'wds_settings_options', $defaults );

	}

}

add_action( 'init', 'wds_defaults' );