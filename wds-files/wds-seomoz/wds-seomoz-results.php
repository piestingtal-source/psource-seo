<?php



require_once ( WDS_PLUGIN_DIR . 'wds-seomoz/class-seomozapi.php' );



add_action( 'add_meta_boxes', 'wds_seomoz_add_meta_boxes' );



/* Adds a box to the main column on the Post and Page edit screens */

function wds_seomoz_add_meta_boxes() {

	$show = user_can_see_urlmetrics_metabox();

	foreach( get_post_types() as $post_type ) {

		if ($show) add_meta_box( 'wds_seomoz_urlmetrics', __( 'SEOmoz URL Metriken' , 'wds'), 'wds_seomoz_urlmetrics_box', $post_type, 'normal', 'high' );

	}

}



/* Prints the box content */

function wds_seomoz_urlmetrics_box($post) {

	global $wds_options;



	$page = str_replace( '/', '%252F', untrailingslashit( str_replace( 'http://', '', get_permalink( $post->ID ) ) ) );



	$seomozapi = new SEOMozAPI( $wds_options['access-id'], $wds_options['secret-key'] );

	$urlmetrics = $seomozapi->urlmetrics( $page );

?>

<table class="widefat">

	<tbody>

		<tr class="alt">

			<th width="30%"><?php _e( 'Metriken' , 'wds'); ?></th>

			<th>Value</th>

		</tr>

		<tr>

			<th><?php _e( 'Externe Links' , 'wds'); ?></th>

			<td><p><a href="http://www.opensiteexplorer.org/links?site=<?php echo $page; ?>" target="_blank"><?php echo (!empty($urlmetrics->ueid) ? $urlmetrics->ueid : ''); ?></a></p></td>

		</tr>

		<tr>

			<th><?php _e( 'Links' , 'wds'); ?></th>

			<td><p><a href="http://www.opensiteexplorer.org/links?site=<?php echo $page; ?>" target="_blank"><?php echo (!empty($urlmetrics->uid) ? $urlmetrics->uid : ''); ?></a></p></td>

		</tr>

		<tr>

			<th><?php _e( 'mozRank' , 'wds'); ?></th>

			<td><p><?php echo '<b>' . __( '10 Punkte Skala:' , 'wds') . '</b> <a href="http://www.opensiteexplorer.org/links?site=' . $page . '" target="_blank">' . (!empty($urlmetrics->umrp) ? $urlmetrics->umrp : '') . '</a><br /><br /><b>' . __( 'Rohwert:' , 'wds') . '</b> <a href="http://www.opensiteexplorer.org/links?site=' . $page . '" target="_blank">' . (!empty($urlmetrics->umrr) ? $urlmetrics->umrr : ''); ?></a></p></td>

		</tr>

		<tr>

			<th><?php _e( 'Seitenautorität' , 'wds'); ?></th>

			<td><p><a href="http://www.opensiteexplorer.org/links?site=<?php echo $page; ?>" target="_blank"><?php echo (!empty($urlmetrics->upa) ? $urlmetrics->upa : ''); ?></a></p></td>

		</tr>

	</tbody>

</table>

<?php

	echo '<p><a href="http://moz.com/" target="_blank"><img src="' . WDS_PLUGIN_URL . 'images/linkscape-logo.png" title="Moz Linkscape API" /></a></p>';

}