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

			if($clang_id == 2) {
				return date("d.m.Y",$unix);
			}
			else {
				return strtoupper(date("d/m/Y",$unix));
			}
		}
	}
}

$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$counter_news = "REX_VALUE[1]" == "" ? "5" : "REX_VALUE[1]";
$link_id_fairs = "REX_LINK[id=1 output=id]";

$category_id = "REX_VALUE[2]" > 0 ? "REX_VALUE[2]" : 0;
$category = $category_id > 0 ? new \D2U_News\Category($category_id, rex_clang::getCurrentId()) : FALSE;

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
	<h1 style="font-size: 1.5em;">News</h1>
	Anzahl auszugebender News: REX_VALUE[1]
	<p>Gewählte Kategorie: <?php print ($category !== FALSE ? $category->name : 'Alle Kategorien'); ?></p>
	<p>Gewählte Nachrichtenarten:
		<?php
			$first_type = TRUE;
			foreach($selected_news_types as $selected_news_type) {
				if($first_type)  {
					$first_type = FALSE;
				}
				else {
					print ", ";
				}
				print $selected_news_type->name;
			}
		print ($category !== FALSE ? $category->name : 'Alle Kategorien');
		?>
	</p>
<?php
}
else if(\rex_addon::get("d2u_news")->isAvailable()) {
	// Ausgabe im FRONTEND
	$show_pic = true;
	
	$news = [];
	if($category !== FALSE) {
		$news = $category->getNews(TRUE);
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
		$news = \D2U_News\News::getAll(rex_clang::getCurrentId(), $counter_news, TRUE);
	}
	
	// Only predefined number of news
	$news = array_slice($news, 0, $counter_news);
	

	if(count($news) > 0) {
	?>
	<div class="col-12 col-lg-8">
		<div class="row">
			<div class="col-12">
				<h1><?php print $tag_open . 'd2u_news_news'. $tag_close; ?></h1>
			</div>
		</div>
		<div class="row">
			<?php
				foreach ($news as $nachricht) {
					$machine = FALSE;
					// In case link is set to a machine from D2U Machinery Addon
					if($nachricht->d2u_machines_machine_id > 0) {
						$machine = new Machine($nachricht->d2u_machines_machine_id, rex_clang::getCurrentId());
					}

					if($show_pic && $nachricht->picture != "") {
						print '<aside class="col-12 col-sm-2">';
						if($nachricht->article_id > 0) {
							print '<a href="'. rex_getUrl($nachricht->article_id).'">';
						}
						else if($nachricht->d2u_machines_machine_id > 0) {
							// In case link is set to a machine from D2U Machinery Addon
							print '<a href="'. $machine->getURL() .'">';
						}
						print '<img src="index.php?rex_media_type=news_preview&rex_media_file='. $nachricht->picture .'" alt="'. $nachricht->name .'" class="listpic">';
						if($nachricht->article_id > 0 || $nachricht->d2u_machines_machine_id > 0) {
							print '</a>';
						}
						print '</aside>';
					}
			?>
				<div class="col-12 col-sm-10">
					<?php
						print '<h1>';
						if($nachricht->link_type == "article" && $nachricht->article_id > 0) {
							print '<a href="'. rex_getUrl($nachricht->article_id).'">';
						}
						else if($nachricht->link_type == "machine" && $nachricht->d2u_machines_machine_id > 0) {
							// In case link is set to a machine from D2U Machinery Addon
							print '<a href="'. $machine->getURL() .'">';
						}
						else if($nachricht->link_type == "url" && $nachricht->url != "") {
							print '<a href="'. $nachricht->url .'">';
						}
						print $nachricht->name;
						if($nachricht->link_type != "none") {
							print '</a>';
						}
						print '</h1>';
						print '<p><time datetime="'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'">'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'</time></p>';
					?>
					<p class="text">
						<?php
							print d2u_addon_frontend_helper::prepareEditorField($nachricht->teaser);
						?>
					</p>
				</div>
			<?php
				}
			?>
		</div>
	</div>
	<?php
		if(rex_plugin::get('d2u_news', 'fairs')->isAvailable()) {
			// Messen ausgeben
			$fairs = \D2U_News\Fair::getAll(TRUE);

			if(count($fairs) > 0) {
				print '<div class="col-12 col-lg-4">';
				print '<div class="row">';
				print '<div class="col-12">';
				print '<h1>'. $tag_open . 'd2u_news_fair_dates'. $tag_close .'</h1>';
				print '</div>';
				print '</div>';
				print '<div class="row">';
				print '<div class="col-12">';
				print '<ul>';

				$fair_counter = 0;
				foreach($fairs as $fair) {
					print '<li>';
					print '<h5>'. $fair->name .' | '. $fair->city .', '. $fair->country_code .'</h5>';
					print formatDate($fair->date_start, rex_clang::getCurrentId()) .' - '. formatDate($fair->date_end, rex_clang::getCurrentId());
					print '</li>';
					$fair_counter++;
					if($fair_counter > 4) {
						break;
					}
				}
				print '</ul>';
				if($link_id_fairs > 0) {
					print '<a href="'. rex_getUrl($link_id_fairs) .'" class="arrow">'. $tag_open . 'd2u_news_fairs_all'. $tag_close .'</a>';
				}
				print '</div>';
				print '</div>';
				print '</div>';
				print '</div>';
			}
		}
	}
}
?>