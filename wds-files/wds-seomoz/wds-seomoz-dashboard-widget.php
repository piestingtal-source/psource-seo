<?php



function wds_seomoz_dashboard_widget () {

	global $wds_options;



	if( !isset($wds_options['access-id']) || !isset($wds_options['secret-key']) ) {

		_e('<p>Moz-Anmeldeinformationen nicht ordnungsgemäß eingerichtet.</p>');

		return;

	}



	$target_url = preg_replace('!http(s)?:\/\/!', '', get_bloginfo('url'));

	$seomozapi = new SEOMozAPI( $wds_options['access-id'], $wds_options['secret-key'] );

	$urlmetrics = $seomozapi->urlmetrics( $target_url );



	$attribution = str_replace( '/', '%252F', untrailingslashit( $target_url ) );

	//$attribution = "http://www.opensiteexplorer.org/$attribution/a";

	$attribution = "http://www.opensiteexplorer.org/links?site={$attribution}";



	if (!is_object($urlmetrics)) {

		printf( __('Daten können nicht von der Moz-API abgerufen werden. Error: %s.' , 'wds'), $urlmetrics );

		return;

	}



	echo '<h4>' . __( 'Domänenmetriken' , 'wds') . ' (' . $target_url . ')</h4>

<table class="widefat">

	<thead>

		<tr>

			<th width="75%">' . __( 'Metrik' , 'wds') . '</th>

			<th>' . __( 'Wert' , 'wds') . '</th>

		</tr>

	</thead>

	<tfoot>

		<tr>

			<th>' . __( 'Metrik' , 'wds') . '</th>

			<th>' . __( 'Wert' , 'wds') . '</th>

		</tr>

	</tfoot>

	<tbody>

		<tr>

			<td><b>' . __( 'Domain mozRank' , 'wds') . '</b><br />Maß des MozRank <a href="http://www.opensiteexplorer.org/About#faq_5" target="_blank">(?)</a> der Domain im Linkscape-Index</td>

			<td>' . sprintf( __( '10 Punkte Skala: %s' , 'wds'), "<a href='$attribution'>$urlmetrics->fmrp</a>" ) . '<br />' . sprintf( __( 'Rohwert: %s' , 'wds'), "<a href='$attribution' target='_blank'>$urlmetrics->fmrr</a>" ) . '

			</td>

		</tr>

		<tr class="alt">

			<td><b>' . __( 'Domain Autorität' , 'wds') . '</b> <a href="http://apiwiki.seomoz.org/w/page/20902104/Domain-Authority/" target="_blank">(?)</a></td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->pda) ? $urlmetrics->pda : '') . '</a></td>

		</tr>

		<tr>

			<td><b>' . __( 'Externe Links zur Homepage' , 'wds') . '</b><br />Die Anzahl der externen (von anderen Subdomains), Saft passierenden Links <a href="http://apiwiki.seomoz.org/w/page/13991139/Juice-Passing" target="_blank">(?)</a> zur Ziel-URL im Linkscape-Index </td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->ueid) ? $urlmetrics->ueid : '') . '</a></td>

		</tr>

		<tr>

			<td><b>' . __( 'Links zur Homepage' , 'wds') . '</b><br />Die Anzahl der internen und externen Links, die Saft und Nicht-Saft weiterleiten <a href="http://apiwiki.seomoz.org/w/page/13991139/Juice-Passing" target="_blank">(?)</a> zur Ziel-URL im Linkscape-Index</td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->uid) ? $urlmetrics->uid : '') . '</a></td>

		</tr>

		<tr>

			<td><b>' . __( 'Startseite mozRank' , 'wds') . '</b><br />Maß des MozRank <a href="http://www.opensiteexplorer.org/About#faq_5" target="_blank">(?)</a> der Homepage-URL im Linkscape-Index</td>

			<td>' . sprintf( __( '10 Punkte Skala: %s' , 'wds'), "<a href='$attribution'>$urlmetrics->umrp</a>" ) . '<br />' . sprintf( __( 'Rohwert: %s' , 'wds'), "<a href='$attribution' target='_blank'>$urlmetrics->umrr</a>" ) . '</td>

		</tr>

		<tr>

			<td><b>' . __( 'Startseite Autorität' , 'wds') . '</b> <a href="http://apiwiki.seomoz.org/Page-Authority" target="_blank">(?)</a></td>

			<td><a href="' . $attribution . '" target="_blank">' . (!empty($urlmetrics->upa) ? $urlmetrics->upa : '') . '</a></td>

		</tr>

	</tbody>

</table>

<p>' . __( 'Informationen zu Beitrags/Seiten-spezifischen Metriken findest Du im Modul Moz URL-Metriken im Bildschirm Beitrag/Seite bearbeiten' , 'wds') . '</p>' .

'<p><a href="http://moz.com/" target="_blank"><img src="' . WDS_PLUGIN_URL . 'images/linkscape-logo.png" title="Moz Linkscape API" /></a></p>';

}



function wds_add_seomoz_dashboard_widget () {

	if (!current_user_can('edit_posts')) return false;

	wp_add_dashboard_widget('wds_seomoz_dashboard_widget', 'Moz', 'wds_seomoz_dashboard_widget');

}

add_action('wp_dashboard_setup', 'wds_add_seomoz_dashboard_widget' );