<?php



require_once ( WDS_PLUGIN_DIR . 'wds-seomoz/class-seomozapi.php' );



/* Add settings page */

function wds_seomoz_settings() {

	global $wds_options;



	$name = 'wds_seomoz';

	$title = 'Moz';

	$description = __( '<p>Wir machen die Integration mit Moz - dem Branchenführer für SEO-Berichte - einfach.</p>

	<p><a href="http://moz.com/products/api" target="_blank">Melde Dich für ein kostenloses Konto</a> an, um Zugriff auf Berichte zu erhalten, aus denen hervorgeht, wie sich Deine Website mit allen wichtigen SEO-Messwerkzeugen - Ranking, Links und vielem mehr - gegen die Konkurrenz behauptet.</p>' , 'wds');



	$fields = array(

		'authentication' => array(

			'title' => __( 'Authentifizierung' , 'wds'),

			'intro' => '',

			'options' => array(

				array(

					'type' => 'text',

					'name' => 'access-id',

					'title' => __( 'Zugangs-ID' , 'wds'),

					'description' => ''

				),

				array(

					'type' => 'text',

					'name' => 'secret-key',

					'title' => __( 'Geheimer Schlüssel' , 'wds'),

					'description' => ''

				)

			)

		)

	);



	$target_url = str_replace( 'http://', '', get_bloginfo( 'url' ) );



	//if( $pagenow = 'wds_seomoz' && isset( $_GET['updated'] ) ) { // <-- This is the way it was before. It doesn't really work.

	if( wds_is_wizard_step( '4' ) && isset( $_GET['settings-updated'] ) ) { // Changed how we determine settings being saved

		delete_transient( "seomoz_urlmetrics_$target_url" );

	}



	$additional = '';

	if( isset( $wds_options['access-id'] ) && isset( $wds_options['secret-key'] ) ) {



		$seomozapi = new SEOMozAPI( $wds_options['access-id'], $wds_options['secret-key'] );

		$urlmetrics = $seomozapi->urlmetrics( $target_url );



		$attribution = str_replace( '/', '%252F', untrailingslashit( $target_url ) );

		$attribution = "http://www.opensiteexplorer.org/links?site={$attribution}";



		$additional = is_object( $urlmetrics ) ? '

<h3>' . __( 'Domänenmetriken' , 'wds') . '</h3>

<table class="widefat" style="width:500px">

	<thead>

		<tr>

			<th width="75%">' . __( 'Metriken' , 'wds') . '</th>

			<th>' . __( 'Wert' , 'wds') . '</th>

		</tr>

	</thead>

	<tfoot>

		<tr>

			<th>' . __( 'Metriken' , 'wds') . '</th>

			<th>' . __( 'Wert' , 'wds') . '</th>

		</tr>

	</tfoot>

	<tbody>

		<tr>

			<td><b>' . __( 'Domain mozRank' , 'wds') . '</b><br />Maß des MozRank <a href="http://www.opensiteexplorer.org/About#faq_5" target="_blank">(?)</a> der Domain im Linkscape-Index</td>

			<td>' . sprintf( __( '10 Punkte Skala: %s' , 'wds'), "<a href='$attribution'>" . (!empty($urlmetrics->fmrp) ? $urlmetrics->fmrp : '') . "</a>" ) . '<br />' . sprintf( __( 'Rohwert: %s' , 'wds'), "<a href='$attribution' target='_blank'>" . (!empty($urlmetrics->fmrr) ? $urlmetrics->fmrr : '') . "</a>" ) . '

			</td>

		</tr>

		<tr class="alt">

			<td><b>' . __( 'Domain-Autorität' , 'wds') . '</b> <a href="http://apiwiki.seomoz.org/w/page/20902104/Domain-Authority/" target="_blank">(?)</a></td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->pda) ? $urlmetrics->pda : '') . '</a></td>

		</tr>

		<tr>

			<td><b>' . __( 'Externe Links zur Homepage' , 'wds') . '</b><br />Die Anzahl der externen (von anderen Subdomains),  passierenden Links <a href="http://apiwiki.seomoz.org/w/page/13991139/Juice-Passing" target="_blank">(?)</a> zur Ziel-URL im Linkscape Index </td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->ueid) ? $urlmetrics->ueid : '') . '</a></td>

		</tr>

		<tr>

			<td><b>' . __( 'Links zur Homepage' , 'wds') . '</b><br />Die Anzahl der internen und externen Links, die Saft- und Nicht-Saft-Links <a href="http://apiwiki.seomoz.org/w/page/13991139/Juice-Passing" target="_blank">(?)</a> zur Ziel-URL im Linkscape-Index übergeben</td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->uid) ? $urlmetrics->uid : '') . '</a></td>

		</tr>

		<tr>

			<td><b>' . __( 'Startseite mozRank' , 'wds') . '</b><br />Maß des MozRank <a href="http://www.opensiteexplorer.org/About#faq_5" target="_blank">(?)</a> der Homepage-URL im Linkscape-Index</td>

			<td>' . sprintf( __( '10 Punkte Skala: %s' , 'wds'), "<a href='$attribution'>" . (!empty($urlmetrics->umrp) ? $urlmetrics->umrp : '') . "</a>" ) . '<br />' . sprintf( __( 'Rohwert: %s' , 'wds'), "<a href='$attribution' target='_blank'>" . (!empty($urlmetrics->umrr) ? $urlmetrics->umrr : '') . "</a>" ) . '</td>

		</tr>

		<tr>

			<td><b>' . __( 'Startseiten Autorität' , 'wds') . '</b> <a href="http://apiwiki.seomoz.org/Page-Authority" target="_blank">(?)</a></td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->upa) ? $urlmetrics->upa : '') . '</a></td>

		</tr>

	</tbody>

</table>

<p>' . __( 'Informationen zu Beiträgen/Seiten-spezifischen Metriken findest Du im Modul Moz URL-Metriken im Bildschirm Beitrag/Seite bearbeiten,' , 'wds') . '</p>

' : '<p>' . sprintf( __( 'Daten können nicht von der Moz-API abgerufen werden. Error: %s.' , 'wds'), $urlmetrics ) . '</p>';



	}



	$additional .= '<p><a href="http://moz.com/" target="_blank"><img src="' . WDS_PLUGIN_URL . 'images/linkscape-logo.png" title="Moz Linkscape API" /></a></p>';


	WDS_Core_Admin_Tabs::register('4', $name, $title, $description, $fields, $additional);

}

add_action( 'init', 'wds_seomoz_settings' );



/* Default settings */

function wds_seomoz_defaults() {

}

add_action( 'init', 'wds_seomoz_defaults' );