<?php
if(!function_exists('formatDate')) {
	/**
	 * Formats the date for language specific options.
	 * @param string $date DAte in Format YYYY-MM-TT
	 * @param int $clang_id Redaxo clang id
	 * @return string Formated date
	 */
	function formatDate($datum, $clang_id) {
		if($datum != "") {
			$d = explode("-", $datum);
			$unix = mktime(0, 0, 0, $d[1], $d[2], $d[0]);

			return date("d.m.Y",$unix);
		}
	}
}

$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$counter_news = "REX_VALUE[1]" == "" ? "5" : "REX_VALUE[1]";
$link_id_overview = "REX_LINK[id=1 output=id]";

$category_id = "REX_VALUE[2]" > 0 ? "REX_VALUE[2]" : 0;
$category = $category_id > 0 ? new \D2U_News\Category($category_id, rex_clang::getCurrentId()) : false;

$heading = "REX_VALUE[4]" != "" ? "REX_VALUE[4]" : \Sprog\Wildcard::get('d2u_news_news');

// If News Types Plugin is activated
$selected_news_types = [];
if(rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
	$selected_news_type_ids = rex_var::toArray("REX_VALUE[3]");
	foreach ($selected_news_type_ids as $selected_news_type_id) {
		$selected_news_types[] = new \D2U_News\Type($selected_news_type_id, rex_clang::getCurrentId());
	}
}

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h2 style="font-size: 1.5em;"><?php print $heading; ?></h2>
	<p>Anzahl auszugebender News: REX_VALUE[1]</p>
	<p>Gewählte Kategorie: <?php print ($category !== false ? $category->name : 'Alle Kategorien'); ?></p>
	<p>Gewählte Nachrichtenarten:
		<?php
			$first_type = true;
			foreach($selected_news_types as $selected_news_type) {
				if($first_type)  {
					$first_type = false;
				}
				else {
					print ", ";
				}
				print $selected_news_type->name;
			}
		print ($category !== false ? $category->name : 'Alle Kategorien');
		?>
	</p>
<?php
}
else if(\rex_addon::get("d2u_news")->isAvailable()) {
	// FRONTEND
	$news = [];
	if($category !== false) {
		$news = $category->getNews(true);
	}
	else if(rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
		// If News Types Plugin is activated: filter
		$news = \D2U_News\News::getAll(rex_clang::getCurrentId());
		if(count($selected_news_types) > 0) {
			foreach ($news as $current_news) {
				foreach($selected_news_types as $selected_news_type) {
					if(!in_array($selected_news_type, $current_news->types)) {
						unset($news[$current_news->news_id]);
					}
				}
			}
		}
	}
	else {
		//
		$news = \D2U_News\News::getAll(rex_clang::getCurrentId(), $counter_news, true);
	}
	
	// Only predefined number of news
	$news = array_slice($news, 0, $counter_news);
	
	if(count($news) > 0) {
	?>
		<div class="col-12">
			<div class="row">
				<div class="col-12">
					<h2 class="h2-news"><?php print $heading; ?></h2>
				</div>
			</div>
			<?php
				foreach ($news as $nachricht) {
					print '<div class="row news">';
					print '<div class="col-12">';
					print '<div class="news-box">';
					print '<div class="row">';
					
					if($nachricht->picture != "") {
						print '<div class="col-12 col-sm-4">';
						if($nachricht->getUrl() != "") {
							print '<a href="'. $nachricht->getUrl() .'">';
						}
						print '<img src="index.php?rex_media_type=news_preview&rex_media_file='. $nachricht->picture .'" alt="'. $nachricht->name .'" class="listpic">';
						if($nachricht->getUrl() != "") {
							print '</a>';
						}
						print '</div>';
						
						print '<div class="col-12 col-sm-8">';
					}
					else {
						print '<div class="col-12">';
					}

					print '<h3 class="news">';
					if($nachricht->getUrl() != "") {
						print '<a href="'. $nachricht->getUrl() .'">';
					}
					print $nachricht->name;
					if($nachricht->getUrl() != "") {
						print '</a>';
					}
					print '</h3>';
					print '<time datetime="'. $nachricht->date .'">'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'</time>';
						
					if($nachricht->teaser != "") {
						print d2u_addon_frontend_helper::prepareEditorField($nachricht->teaser);
					}
					else if($nachricht->getUrl() != "") {
						print '<p class="text"><a href="'. $nachricht->getUrl() .'">'. $nachricht->getUrl() .'</a></p>';	
					}
					print '</div>';
					print '</div>';
					print '</div>';
					print '</div>';
					print '</div>';
				}

				if($link_id_overview > 0) {
					print '<div class="row">';
					print '<div class="col-12">';
					print '<a href="'. rex_getUrl($link_id_overview) .'">'. $tag_open . 'd2u_news_details'. $tag_close .'</a>';
					print '</div>';
					print '</div>';
				}
			?>
		</div>
	<?php
	}
}
?>