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
$link_id_overview = "REX_LINK[id=1 output=id]";

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">News</h1>
	Anzahl auszugebender News: REX_VALUE[1]
<?php
}
else if(rex_addon::get("d2u_news")->isAvailable()) {
	// Ausgabe im FRONTEND
	$show_pic = true;
	
	$news = News::getAll(rex_clang::getCurrentId(), $counter_news, TRUE);
	if(count($news) > 0) {
	?>
		<div class="small-12 columns">
			<div class="row">
				<div class="small-12 columns">
					<h1><?php print $tag_open . 'd2u_news_news'. $tag_close; ?></h1>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns">
					<?php
						foreach ($news as $nachricht) {
					?>
					<article class="row teaser teaser-fullwidth hyphens">
						<?php
						$machine = FALSE;
						if($nachricht->d2u_machines_machine_id > 0) {
							$machine = new Machine($nachricht->d2u_machines_machine_id, rex_clang::getCurrentId());
						}

						if($show_pic) {
							print '<aside class="large-2 small-2 columns">';
							if($nachricht->article_id > 0) {
								print '<a href="'. rex_getUrl($nachricht->article_id).'">';
							}
							else if($nachricht->d2u_machines_machine_id > 0) {
								print '<a href="'. $machine->getURL() .'">';
							}
							print '<img src="index.php?rex_media_type=news_preview&rex_media_file='. $nachricht->picture .'" alt='. $nachricht->name .' class="listpic">';
							if($nachricht->article_id > 0 || $nachricht->d2u_machines_machine_id > 0) {
								print '</a>';
							}
							print '</aside>';
						}
						?>
						<div class="large-10 small-10 columns">
							<header>
								<?php
									print '<h1>';
									if($nachricht->article_id > 0) {
										print '<a href="'. rex_getUrl($nachricht->article_id).'">';
									}
									else if($nachricht->d2u_machines_machine_id > 0) {
										print '<a href="'. $machine->getUrl() .'">';
									}
									print $nachricht->name;
									if($nachricht->article_id > 0 || $nachricht->d2u_machines_machine_id > 0) {
										print '</a>';
									}
									print '</h1>';
									print '<p><time pubdate="" datetime="'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'">'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'</time></p>';
									?>
							</header>
							<p class="text">
								<?php
									print $nachricht->teaser;
								?>
							</p>
						</div>
					</article>
					<div class="sp line w100p"></div>
					<?php
					}
				?>
				</div>
			</div>
			<?php
				if($link_id_overview > 0) {
					print '<div class="row">';
					print '<div class="small-12 columns">';
					print '<div class="sp h20"></div>';
					print '<a href="'. rex_getUrl($link_id_overview) .'" class="arrow">'. $tag_open . 'd2u_news_details'. $tag_close .'</a>';
					print '</div>';
					print '</div>';
				}
			?>
		</div>
		<div class="sp sections"></div>
	<?php
	}
}
?>