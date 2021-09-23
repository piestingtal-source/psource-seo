<?php



class WDS_Metabox {



	//function WDS_Metabox() {
	function __construct() {



		// WPSC integration

		add_action('wpsc_edit_product', array($this, 'rebuild_sitemap'));

		add_action('wpsc_rate_product', array($this, 'rebuild_sitemap'));



		add_action('admin_menu', array($this, 'wds_create_meta_box'));



		add_action('save_post', array($this, 'wds_save_postdata'));

		add_filter('attachment_fields_to_save', array($this, 'wds_save_attachment_postdata'));



		//add_filter('manage_page_posts_columns', array(&$this, 'wds_page_title_column_heading'), 10, 1);

		add_filter('manage_pages_columns', array($this, 'wds_page_title_column_heading'), 10, 1);

		//add_filter('manage_post_posts_columns', array(&$this, 'wds_page_title_column_heading'), 10, 1);

		add_filter('manage_posts_columns', array($this, 'wds_page_title_column_heading'), 10, 1);



		add_action('manage_pages_custom_column', array($this, 'wds_page_title_column_content'), 10, 2);

		add_action('manage_posts_custom_column', array($this, 'wds_page_title_column_content'), 10, 2);



		add_action('quick_edit_custom_box', array($this, 'wds_quick_edit_dispatch'), 10, 2);

		add_action('admin_footer-edit.php', array($this, 'wds_quick_edit_javascript'));

		add_action('wp_ajax_wds_get_meta_fields', array($this, 'json_wds_postmeta'));



		add_action('admin_print_scripts-post.php', array($this, 'js_load_scripts'));

		add_action('admin_print_scripts-post-new.php', array($this, 'js_load_scripts'));

	}



	function js_load_scripts () {

		wp_enqueue_script('wds_metabox_counter', WDS_PLUGIN_URL . '/js/wds-metabox-counter.js');

		wp_localize_script('wds_metabox_counter', 'l10nWdsCounters', array(

			"title_length" => __("{TOTAL_LEFT} Zeichen übrig", 'wds'),

			"title_longer" => __("Mehr als {MAX_COUNT} Zeichen ({CURRENT_COUNT})", 'wds'),

			"main_title_longer" => __("Mehr als {MAX_COUNT} Zeichen ({CURRENT_COUNT}) - Stelle sicher, dass der SEO-Titel kürzer ist", 'wds'),



			'title_limit' => WDS_TITLE_LENGTH_CHAR_COUNT_LIMIT,

			'metad_limit' => WDS_METADESC_LENGTH_CHAR_COUNT_LIMIT,

			'main_title_warning' => !(defined('WDS_MAIN_TITLE_LENGTH_WARNING_HIDE') && WDS_MAIN_TITLE_LENGTH_WARNING_HIDE),

		));

	}





	function wds_meta_boxes() {

		global $post;



		echo '<script type="text/javascript">var lang = "'.substr(get_locale(),0,2).'";</script>';



		$date = '';

		if ($post->post_type == 'post') {

			if ( isset($post->post_date) )

				$date = date('M j, Y', strtotime($post->post_date));

			else

				$date = date('M j, Y');

		}



		echo '<table class="widefat">';



		$title = wds_get_value('title');

		if (empty($title))

			$title = $post->post_title;

		if (empty($title))

			$title = "temp title";



		$desc = wds_get_value('metadesc');

		if (empty($desc))

			$desc = substr(strip_tags($post->post_content), 0, 130).' ...';

		if (empty($desc))

			$desc = 'temp description';



		$slug = $post->post_name;

		if (empty($slug))

			$slug = sanitize_title($title);



?>

	<tr>

		<th><label>Preview:</label></th>

		<td>

<?php

		$video = wds_get_value('video_meta',$post->ID);

		if ( $video && $video != 'none' ) {

?>

			<div id="snippet" class="video">

				<h4 style="margin:0;font-weight:normal;"><a class="title" href="#"><?php echo $title; ?></a></h4>

				<div style="margin:5px 10px 10px 0;width:82px;height:62px;float:left;">

					<img style="border: 1px solid blue;padding: 1px;width:80px;height:60px;" src="<?php echo $video['thumbnail_loc']; ?>"/>

					<div style="margin-top:-23px;margin-right:4px;text-align:right"><img src="http://www.google.com/images/icons/sectionized_ui/play_c.gif" alt="" border="0" height="20" style="-moz-opacity:.88;filter:alpha(opacity=88);opacity:.88" width="20"></div>

				</div>

				<div style="float:left;width:440px;">

					<p style="color:#767676;font-size:13px;line-height:15px;"><?php echo number_format($video['duration']/60); ?> mins - <?php echo $date; ?></p>

					<p style="color:#000;font-size:13px;line-height:15px;" class="desc"><span><?php echo $desc; ?></span></p>

					<a href="#" class="url"><?php echo str_replace('http://','',get_bloginfo('url')).'/'.$slug.'/'; ?></a> - <a href="#" class="util">More videos &raquo;</a>

				</div>

			</div>



<?php

		} else {

			if (!empty($date))

				$date .= ' ... ';

?>

			<div id="snippet">

				<p><a style="color:#2200C1;font-weight:medium;font-size:16px;text-decoration:underline;" href="#"><?php echo $title; ?></a></p>

				<p style="font-size: 12px; color: #000; line-height: 15px;"><?php echo $date; ?><span><?php echo $desc ?></span></p>

				<p>

					<a href="#" style="font-size: 13px; color: #282; line-height: 15px;" class="url"><?php echo str_replace('http://','',get_bloginfo('url')).'/'.$slug.'/'; ?></a> - <a href="#" class="util">Cached</a> - <a href="#" class="util">Similar</a>

					<?php if (is_multisite() && (is_admin() || is_network_admin()) && class_exists('domain_map')) { ?>

						<small style="opacity:.5"><i><?php esc_html_e(__('Die URL-Vorschau kann durch Domain-Mapping vereitelt werden', 'wds')); ?></i></small>

					<?php } ?>

				</p>

			</div>

<?php } ?>

		</td>

	</tr>

<?php

		echo $this->show_title_row();

		echo $this->show_metadesc_row();

		echo $this->show_keywords_row();

		echo $this->show_robots_row();

		echo $this->show_canonical_row();

		if (user_can_see_seo_metabox_301_redirect()) echo $this->show_redirect_row();

		echo $this->show_sitemap_row();

		echo '</table>';

	}



	function wds_create_meta_box() {

		$show = user_can_see_seo_metabox();

		if ( function_exists('add_meta_box') ) {

			$metabox_title = is_multisite() ? __( 'PS SEO' , 'wds') : 'PS SEO'; // Show branding for singular installs.

			foreach (get_post_types() as $posttype) {

				if ($show) add_meta_box( 'wds-wds-meta-box', $metabox_title, array(&$this, 'wds_meta_boxes'), $posttype, 'normal', 'high' );

			}

		}

	}



	function wds_save_attachment_postdata ($data) {

		if (empty($_POST) || empty($data['post_ID']) || !is_numeric($data['post_ID'])) return $data;

		$this->wds_save_postdata($data['post_ID']);

		return $data;

	}



	function wds_save_postdata( $post_id ) {

		if ($post_id == null || empty($_POST)) return;



		global $post;

		if (empty($post)) $post = get_post($post_id);



		if ('page' == @$_POST['post_type'] && !current_user_can('edit_page', $post_id)) return $post_id;

		else if (!current_user_can( 'edit_post', $post_id )) return $post_id;



		foreach ($_POST as $key=>$value) {

			if (!preg_match('/^wds_/', $key)) continue;



			$id = "_{$key}";

			$data = $value;

			if (is_array($value)) $data = join(',', $value);



			if ($data) update_post_meta($post_id, $id, $data);

			else delete_post_meta($post_id, $id);

		}



		do_action('wds_saved_postdata');

	}



	function rebuild_sitemap() {

		require_once WDS_PLUGIN_DIR.'/wds-sitemaps/wds-sitemaps.php';

	}



	function wds_page_title_column_heading( $columns ) {

		return array_merge(

			array_slice( $columns, 0, 2 ),

			array( 'page-title' => __( 'Titel-Tag' , 'wds') ),

			array_slice($columns, 2, 6),

			array( 'page-meta-robots' => __( 'Robots Meta' , 'wds') )

		);

	}



	function wds_page_title_column_content( $column_name, $id ) {

		if ( $column_name == 'page-title' ) {

			echo $this->wds_page_title($id);



			// Show any 301 redirects

			$redirect = wds_get_value('redirect', $id);

			if (!empty($redirect)) {

				$href = esc_url($redirect);

				$link = "<a href='{$href}' target='_blank'>{$href}</a>";

				echo '<br /><em>' . sprintf(esc_html(__('Leitet zu %s um', 'wds')), $link) . '</em>';

			}

		}



		if ( $column_name == 'page-meta-robots' ) {

			$meta_robots_arr = array(

				(wds_get_value( 'meta-robots-noindex', $id ) ? 'noindex' : 'index'),

				(wds_get_value( 'meta-robots-nofollow', $id ) ? 'nofollow' : 'follow')

			);

			$meta_robots = join(',', $meta_robots_arr);

			if ( empty($meta_robots) )

				$meta_robots = 'index,follow';

			echo ucwords( str_replace( ',', ', ', $meta_robots ) );



			// Show additional robots data

			$advanced = array_filter(array_map('trim', explode(',', wds_get_value('meta-robots-adv', $id))));

			if (!empty($advanced) && 'none' !== $advanced) {

				$adv_map = array(

					'noodp' => __('Kein ODP', 'wds'),

					'noydir' => __('Kein YDIR', 'wds'),

					'noarchive' => __('Kein Archiv', 'wds'),

					'nosnippet' => __('Kein Snippet', 'wds'),

				);

				$additional = array();

				foreach ($advanced as $key) {

					if (!empty($adv_map[$key])) $additional[] = $adv_map[$key];

				}

				if (!empty($additional)) echo '<br /><small>' . esc_html(join(', ', $additional)) . '</small>';

			}

		}

	}



	function wds_page_title( $postid ) {

		$post = get_post($postid);

		$fixed_title = wds_get_value('title', $post->ID);

		if ($fixed_title) {

			return $fixed_title;

		} else {

			global $wds_options;

			if (!empty($wds_options['title-'.$post->post_type]))

				return wds_replace_vars($wds_options['title-'.$post->post_type], (array) $post );

			else

				return '';

		}

	}



/* ========== Display helpers ========== */



	function field_title ($str, $for) {

		return "<th valign='top'><label for='{$for}'>{$str}</label></th>";

	}

	function field_content ($str, $desc=false) {

		$desc = $desc ? "<p>$desc</p>" : '';

		return "<td valign='top'>{$str}\n{$desc}</td>";

	}



	function show_title_row () {

		$title = __('Titel-Tag' , 'wds');

		$desc = sprintf(__('Bis zu %d Zeichen empfohlen' , 'wds'), WDS_TITLE_LENGTH_CHAR_COUNT_LIMIT);

		$value = esc_html(wds_get_value('title'));

		$field = "<input type='text' class='widefat' id='wds_title' name='wds_title' value='{$value}' class='wds' />";



		return '<tr>' .

			$this->field_title($title, 'wds_title') .

			$this->field_content($field, $desc) .

		'</tr>';

	}



	function show_metadesc_row () {

		$title = __('Meta Beschreibung' , 'wds');

		$desc = sprintf(__('%d Zeichen maximal' , 'wds'), WDS_METADESC_LENGTH_CHAR_COUNT_LIMIT);

		$value = esc_html(wds_get_value('metadesc'));

		$field = "<textarea rows='2' class='widefat' name='wds_metadesc' id='wds_metadesc' class='wds'>{$value}</textarea>";



		return '<tr>' .

			$this->field_title($title, 'wds_metadesc') .

			$this->field_content($field, $desc) .

		'</tr>';

	}



	function show_keywords_row () {

		$title = __('Meta-Keywords' , 'wds');

		$desc = __('Trenne Schlüsselwörter durch Kommas' , 'wds');

		$desc .= '<br />' . __('Wenn Du die Verwendung von Tags aktivierst, werden Post-Tags mit allen anderen Schlüsselwörtern zusammengeführt, die Du in das Textfeld eingibst.', 'wds');

		$value = esc_html(wds_get_value('keywords'));

		$checked = wds_get_value('tags_to_keywords') ? 'checked="checked"' : '';

		$field = "<input type='text' class='widefat' id='wds_keywords' name='wds_keywords' value='{$value}' class='wds' />";

		$field .= '<br /><label for="wds_tags_to_keywords">' . __('Ich möchte zusätzlich zu meinen Keywords Beitrags-Tags verwenden', 'wds') . '</label> ' .

			"<input type='checkbox' name='wds_tags_to_keywords' id='wds_tags_to_keywords' value='1' {$checked} />";



		$news = esc_html(wds_get_value('news_keywords'));

		$field .= '<div><b>' . __('Neuigkeiten Keywords', 'wds') . '</b> <a href="http://support.google.com/news/publisher/bin/answer.py?hl=en&answer=68297" target="_blank">(?)</a></div>' .

			"<input type='text' class='widefat' id='wds_news_keywords' name='wds_news_keywords' value='{$news}' class='wds' />" .

		'';



		return '<tr>' .

			$this->field_title($title, 'wds_keywords') .

			$this->field_content($field, $desc) .

		'</tr>';

	}



	function show_robots_row () {

		// Index

		$ri_value = (int)wds_get_value('meta-robots-noindex');

		$robots_index = '<input type="radio" name="wds_meta-robots-noindex" id="wds_meta-robots-noindex-index" ' . (!$ri_value ? 'checked="checked"' : '') . ' value="0" /> ' .

			'<label for="wds_meta-robots-noindex-index">' . __( 'Index' , 'wds') . '</label>' .

			'<br />' .

			'<input type="radio" name="wds_meta-robots-noindex" id="wds_meta-robots-noindex-noindex" ' . ($ri_value ? 'checked="checked"' : '') . ' value="1" /> ' .

			'<label for="wds_meta-robots-noindex-noindex">' . __( 'Kein Index' , 'wds') . '</label>'

		;

		$row_index = '<tr>' .

			$this->field_title( __('Index', 'wds'), 'wds_robots_follow' ) .

			$this->field_content($robots_index) .

		'</tr>';



		// Follow

		$rf_value = (int)wds_get_value('meta-robots-nofollow');

		$robots_follow = '<input type="radio" name="wds_meta-robots-nofollow" id="wds_meta-robots-nofollow-follow" ' . (!$rf_value ? 'checked="checked"' : '') . ' value="0" /> ' .

			'<label for="wds_meta-robots-nofollow-follow">' . __( 'Folgen' , 'wds') . '</label>' .

			'<br />' .

			'<input type="radio" name="wds_meta-robots-nofollow" id="wds_meta-robots-nofollow-nofollow" ' . ($rf_value ? 'checked="checked"' : '') . ' value="1" /> ' .

			'<label for="wds_meta-robots-nofollow-nofollow">' . __( 'Nicht folgen' , 'wds') . '</label>'

		;

		$row_follow = '<tr>' .

			$this->field_title( __('Folgen', 'wds'), 'wds_robots_follow' ) .

			$this->field_content($robots_follow) .

		'</tr>';



		// Advanced

		$adv_value = explode(',', wds_get_value('meta-robots-adv'));

		$advanced = array(

			//"" => '',

			"noodp" => __( 'KEIN ODP (Block Open Directory Projektbeschreibung der Seite)' , 'wds'),

			"noydir" => __( 'KEIN YDIR (Das Yahoo! Verzeichnistitel und Abstracts)' , 'wds'),

			"noarchive" => __( 'Kein Archiv' , 'wds'),

			"nosnippet" => __( 'Kein Snippet' , 'wds'),

		);

		/*

		$robots_advanced = '<select name="wds_meta-robots-adv[]" id="wds_meta-robots-adv" multiple="multiple" size="' . count($advanced) . '" style="height:' . count($advanced) * 1.2 . 'em;">';

		foreach ($advanced as $key => $label) {

			$robots_advanced .= "<option value='{$key}' " . (in_array($key, $adv_value) ? 'selected="selected"' : '') . ">{$label}</option>";

		}

		$robots_advanced .= '</select>';

		*/

		$robots_advanced = '';

		foreach($advanced as $rkey => $rlbl) {

			$checked = in_array($rkey, $adv_value) ? 'checked="checked"' : '';

			$robots_advanced .= '' .

				"<input type='hidden' name='wds_meta-robots-adv[{$rkey}]' value='' />" .

				"<input type='checkbox' name='wds_meta-robots-adv[{$rkey}]' value='{$rkey}' id='wds_meta-robots-adv-{$rkey}' {$checked} />" .

				'&nbsp;' .

				'<label for="wds_meta-robots-adv-' . $rkey . '">' . $rlbl . '</label>' .

			'<br />';

		}

		$row_advanced = '<tr>' .

			$this->field_title( __('Erweitert', 'wds'), 'wds_meta-robots-adv' ) .

			$this->field_content($robots_advanced) .

		'</tr>';



		// Overall

		$title = __('Meta Robots' , 'wds');

		$content = "<table class='wds_subtable' broder='0'>{$row_index}\n{$row_follow}\n{$row_advanced}</table>";

		$desc = __('<code>meta</code> Robotereinstellungen für diese Seite.', 'wds');

		return '<tr>' .

			$this->field_title($title, 'wds-metadesc') .

			$this->field_content($content, $desc) .

		'</tr>';

	}



	function show_canonical_row () {

		$title = __('Kanonische URL' , 'wds');

		$value = wds_get_value('canonical');

		$field = "<input type='text' id='wds_canonical' name='wds_canonical' value='{$value}' class='wds' />";

		return '<tr>' .

			$this->field_title($title, 'wds_canonical') .

			$this->field_content($field) .

		'</tr>';

	}



	function show_redirect_row () {

		$title = __('301 Umleitung' , 'wds');

		$value = wds_get_value('redirect');

		$field = "<input type='text' id='wds_redirect' name='wds_redirect' value='{$value}' class='wds' />";

		return '<tr>' .

			$this->field_title($title, 'wds_redirect') .

			$this->field_content($field) .

		'</tr>';

	}



	function show_sitemap_row () {

		global $wds_options;



		$options = array(

			"" => __( 'Automatische Priorisierung' , 'wds'),

			"1" => __( '1 - Höchste Priorität' , 'wds'),

			"0.9" => "0.9",

			"0.8" => "0.8 - " . __( 'Hohe Priorität (Standard für Stammseiten)' , 'wds'),

			"0.7" => "0.7",

			"0.6" => "0.6 - " . __( 'Sekundäre Priorität (Unterseiten Standard)' , 'wds'),

			"0.5" => "0.5 - " . __( 'Mittlere Priorität' , 'wds'),

			"0.4" => "0.4",

			"0.3" => "0.3",

			"0.2" => "0.2",

			"0.1" => "0.1 - " . __( 'Niedrigste Priorität' , 'wds'),

		);

		$title = __('Sitemap-Priorität' , 'wds');

		$desc = __('Die Priorität, die dieser Seite in der XML-Sitemap zugewiesen wird.' , 'wds');

		$value = wds_get_value('sitemap-priority');



		$field = "<select name='wds_sitemap-priority' id='wds_sitemap-priority'>";

		foreach ($options as $key=>$label) {

			$field .= "<option value='{$key}' " . (($key==$value) ? 'selected="selected"' : '') . ">{$label}</option>";

		}

		$field .= '</select>';



		return '<tr>' .

			$this->field_title($title, 'wds_sitemap-priority') .

			$this->field_content($field, $desc) .

		'</tr>';

	}



	function wds_quick_edit_dispatch ($column, $type) {

		switch ($column) {

			case "page-title": return $this->_title_qe_box($type);

			case "page-meta-robots": return $this->_robots_qe_box();

		}

	}



	function wds_quick_edit_javascript () {

		?>

<script type="text/javascript">

(function ($) {



$("td.column-title").on('click', 'a.editinline', function () {

	var id = inlineEditPost.getId(this);

	$.post(ajaxurl, {"action": "wds_get_meta_fields", "id": id}, function (data) {

		if (!data) return false;

		if ("title" in data && data.title) $(".wds_title:visible").val(data.title);

		if ("description" in data && data.description) $(".wds_metadesc:visible").val(data.description);

	}, "json");

});



})(jQuery);

</script>

		<?php

	}



	function json_wds_postmeta () {

		$id = (int)$_POST['id'];

		die(json_encode(array(

			"title" => wds_get_value('title', $id),

			"description" => wds_get_value('metadesc', $id),

		)));

	}



	private function _title_qe_box ($t) {

		global $post;

		?>

<fieldset class="inline-edit-col-left" style="clear:left">

	<div class="inline-edit-col">

		<h4><?php _e('PSOURCE SEO', 'wds'); ?></h4>

		<label>

			<span class="title"><?php _e('Titel-Tag', 'wds'); ?></span>

			<span class="input-text-wrap">

				<input class="ptitle wds_title" type="text" value="" name="wds_title" />

			</span>

		</label>

	</div>

</fieldset>

		<?php

	}



	private function _robots_qe_box () {

		global $post;

		?>

<fieldset class="inline-edit-col-left">

	<div class="inline-edit-col">

		<label>

			<span class="title"><?php _e('Meta Beschreibung', 'wds'); ?></span>

			<span class="input-text-wrap">

				<textarea class="ptitle wds_metadesc" name="wds_metadesc"></textarea>

				<!--<input class="ptitle wds_metadesc" type="text" value="" name="wds_metadesc" />-->

			</span>

		</label>

	</div>

</fieldset>

		<?php

	}





}

$wds_metabox = new WDS_Metabox();