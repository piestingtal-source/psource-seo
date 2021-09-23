<?php



/* Add settings page */

function wds_autolinks_settings() {

	$name = 'wds_autolinks';

	$title = __( 'Automatische Links' , 'wds');

	$description = __( '<p>Manchmal möchtest Du immer bestimmte Schlüsselwörter mit einer Seite in Deinem Blog oder sogar einer ganz neuen Webseite verknüpfen.</p>

	<p>Wenn Du beispielsweise die Wörter "WordPress-Nachrichten" schreibst, möchtest Du möglicherweise automatisch einen Link zum WordPress-Nachrichtenblog wpmu.org erstellen. Ohne dieses Plugin müsstest Du diese Links jedes Mal manuell erstellen, wenn Du den Text in Deine Seiten und Beiträge schreiben - was überhaupt keinen Spaß machen kann.</p>

	<p>In diesem Abschnitt kannst Du diese Schlüsselwörter und Links festlegen. Wähle zunächst aus, ob Du Schlüsselwörter in Beiträgen, Seiten oder benutzerdefinierten Beitrags-Typen, die Du möglicherweise verwendest, automatisch verknüpfen möchtest. Wenn Du automatische Links verwendest, solltest Du normalerweise alle Optionen hier überprüfen.</p>', 'wds' );



	$fields = array();



	foreach ( get_post_types() as $post_type ) {

		if ( !in_array( $post_type, array('revision', 'nav_menu_item', 'attachment') ) ) {

			$pt = get_post_type_object($post_type);

			$key = strtolower( $pt->name );

			$post_types["l{$key}"] = $pt->labels->name;

			$insert["{$key}"] = $pt->labels->name;

		}

	}

	foreach ( get_taxonomies() as $taxonomy ) {

		if ( !in_array( $taxonomy, array( 'nav_menu', 'link_category', 'post_format' ) ) ) {

			$tax = get_taxonomy($taxonomy);

			$key = strtolower( $tax->labels->name );

			$taxonomies["l{$key}"] = $tax->labels->name;

		}

	}

	$linkto = array_merge( $post_types, $taxonomies );

	$insert['comment'] = __( 'Komentare' , 'wds');

	$fields['internal'] = array(

		'title' => '',

		'intro' => '',

		'options' => array(

			array(

				'type' => 'checkbox',

				'name' => 'insert',

				'title' => __('Füge Links ein' , 'wds'),

				'items' => $insert

			),

			array(

				'type' => 'checkbox',

				'name' => 'linkto',

				'title' => __('Link zu' , 'wds'),

				'items' => $linkto

			),

			array(

				'type' => 'dropdown',

				'name' => 'cpt_char_limit',

				'title' => __( 'Minimale Länge des Beitragstitels' , 'wds'),

				'description' => __('Dies ist die Mindestanzahl von Zeichen, die Dein Beitragstitel für das Einfügen als automatische Verknüpfung berücksichtigen muss.', 'wds'),

				'items' => array_combine(

					array_merge(array(0), range(1,25)),

					array_merge(array(__('Standard', 'wds')), range(1,25))

				),

			),

			array(

				'type' => 'dropdown',

				'name' => 'tax_char_limit',

				'title' => __( 'Minimale Taxonomietitellänge' , 'wds'),

				'description' => __('Dies ist die Mindestanzahl von Zeichen, die Dein Taxonomietitel für das Einfügen als automatische Verknüpfung berücksichtigen muss.', 'wds'),

				'items' => array_combine(

					array_merge(array(0), range(1,25)),

					array_merge(array(__('Standard', 'wds')), range(1,25))

				),

			),

			array(

				'type' => 'checkbox',

				'name' => 'allow_empty_tax',

				'title' => __('Leere Taxonomien' , 'wds'),

				'items' => array('allow_empty_tax' => __('Zulassen, dass Autolinks leere Taxonomien verlinken', 'wds')),

			),

			array(

				'type' => 'checkbox',

				'name' => 'excludeheading',

				'title' => __( 'Überschriften ausschließen' , 'wds'),

				'items' => array( 'excludeheading' => __( 'Verhindere das Verknüpfen in Überschriften-Tags' , 'wds') )

			),

			array(

				'type' => 'text',

				'name' => 'ignorepost',

				'title' => __( 'Beiträge und Seiten ignorieren' , 'wds'),

				'description' => __('Füge die IDs, Slugs oder Titel für die Beiträge /Seiten ein, die Du ausschließen möchtest, und trenne sie durch Kommas', 'wds'),

			),

			array(

				'type' => 'text',

				'name' => 'ignore',

				'title' => __( 'Schlüsselwörter ignorieren' , 'wds'),

				'description' => __('Füge die Schlüsselwörter ein, die Du ausschließen möchtest, und trenne sie durch Kommas', 'wds'),

			),

			array(

				'type' => 'dropdown',

				'name' => 'link_limit',

				'title' => __( 'Maximales Limit für die Anzahl der Autolinks' , 'wds'),

				'description' => __('Dies ist die maximale Anzahl von Autolinks, die Deinen Posts hinzugefügt werden.', 'wds'),

				'items' => array_combine(

					array_merge(array(0), range(1,20)),

					array_merge(array(__('Unlimitiert', 'wds')), range(1,20))

				),

			),

			array(

				'type' => 'dropdown',

				'name' => 'single_link_limit',

				'title' => __( 'Maximales Auftreten einzelner Autolinks' , 'wds'),

				'description' => __('Dies ist eine Anzahl von Vorkommen zum Ersetzen einzelner Links.', 'wds'),

				'items' => array_combine(

					array_merge(array(0), range(1,10)),

					array_merge(array(__('Unlimitiert', 'wds')), range(1,10))

				),

			),

			array(

				'type' => 'textarea',

				'name' => 'customkey',

				'title' => __( 'Benutzerdefinierte Schlüsselwörter' , 'wds'),

				'description' => __('Füge die zusätzlichen Schlüsselwörter ein, die Du automatisch verknüpfen möchtest. Verwende Komma, um Schlüsselwörter zu trennen und am Ende eine Ziel-URL hinzuzufügen. Verwende eine neue Zeile für eine neue URL und eine Reihe von Schlüsselwörtern.

				<br />Beispiel:<br />

				<code>WWMS N@W, plugins, themes, https://n3rds.work/piestingtal-source-project/<br />

				WordPress News, http://n3rds.work/<br /></code>', 'wds'),

			),

			array(

				'type' => 'checkbox',

				'name' => 'reduceload',

				'title' => __( 'Andere Einstellungen' , 'wds'),

				'items' => array(

					'onlysingle' => __( 'Verarbeite nur einzelne Beiträge und Seiten' , 'wds'),

					'allowfeed' => __( 'RSS-Feeds verarbeiten' , 'wds'),

					'casesens' => __( 'Groß- und Kleinschreibung beachten' , 'wds'),

					'customkey_preventduplicatelink' => __( 'Verhindere doppelte Links' , 'wds'),

					'target_blank' => __('Öffne Links in einem neuen Tab/Fenster', 'wds'),

					'rel_nofollow' => __('Autolinks <code>nofollow</code>', 'wds'),

				)

			)

		)

	);


	WDS_Core_Admin_Tabs::register('5', $name, $title, $description, $fields);

}

add_action( 'init', 'wds_autolinks_settings', 999 );



/* Default settings */

function wds_autolinks_defaults() {

	if( is_multisite() && WDS_SITEWIDE == true ) {

		$options = get_site_option( 'wds_autolinks_options' );

	} else {

		$options = get_option( 'wds_autolinks_options' );

	}



	if( is_multisite() && WDS_SITEWIDE == true ) {

		update_site_option( 'wds_autolinks_options', $options );

	} else {

		update_option( 'wds_autolinks_options', $options );

	}

}

add_action( 'init', 'wds_autolinks_defaults' );