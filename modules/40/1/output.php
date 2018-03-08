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

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">News</h1>
	Anzahl auszugebender News: REX_VALUE[1]
<?php
}
else if(rex_addon::get("d2u_news")->isAvailable()) {
	// FRONTEND
	$news = News::getAll(rex_clang::getCurrentId(), $counter_news, TRUE);
	if(count($news) > 0) {
	?>
		<div class="col-12">
			<div class="row">
				<div class="col-12">
					<h1><?php print $tag_open . 'd2u_news_news'. $tag_close; ?></h1>
				</div>
			</div>
			<?php
				foreach ($news as $nachricht) {
					print '<div class="row news">';
					
					$url = "";
					if($nachricht->link_type == "machine" && $nachricht->d2u_machines_machine_id > 0) {
						$machine = new Machine($nachricht->d2u_machines_machine_id, rex_clang::getCurrentId());
						$url = $machine->getURL();
					}
					else if($nachricht->link_type == "article" && $nachricht->article_id > 0) {
						$url = rex_getUrl($nachricht->article_id);
					}
					else if($nachricht->link_type == "url" && $nachricht->url != "") {
						$url = $nachricht->url;
					}

					if($nachricht->picture != "") {
						print '<div class="col-12 col-sm-4">';
						if($url != "") {
							print '<a href="'. $url .'">';
						}
						print '<img src="index.php?rex_media_type=news_preview&rex_media_file='. $nachricht->picture .'" alt='. $nachricht->name .' class="listpic">';
						if($url != "") {
							print '</a>';
						}
						print '</div>';
						
						print '<div class="col-12 col-sm-8">';
					}
					else {
						print '<div class="col-12">';
					}

					print '<h2 class="news">';
					if($url != "") {
						print '<a href="'. $url .'">';
					}
					print $nachricht->name;
					if($url != "") {
						print '</a>';
					}
					print '</h2>';
					print '<p><time pubdate="" datetime="'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'">'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'</time></p>';
						
					if($nachricht->teaser != "") {
						print '<p class="text">';
						if(rex_config::get('d2u_helper', 'editor', '') == 'markitup' && rex_addon::get('markitup')->isAvailable()) {
							print markitup::parseOutput ('markdown', $nachricht->teaser);
						}
						else if(rex_config::get('d2u_helper', 'editor', '') == 'markitup_textile' && rex_addon::get('markitup')->isAvailable()) {
							print markitup::parseOutput ('textile', $nachricht->teaser);
						}
						else {
							print $nachricht->teaser;
						}
						print '</p>';
					}
					else if($url != "") {
						print '<p class="text"><a href="'. $url .'">'. $url .'</a></p>';	
					}
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