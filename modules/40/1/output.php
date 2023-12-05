<?php
if (!function_exists('formatDate')) {
    /**
     * Formats the date for language specific options.
     * @param string $date DAte in Format YYYY-MM-TT
     * @param int $clang_id Redaxo clang id
     * @return string Formated date
     */
    function formatDate($datum, $clang_id)
    {
        if ('' != $datum) {
            $d = explode('-', $datum);
            $unix = mktime(0, 0, 0, $d[1], $d[2], $d[0]);

            return date('d.m.Y', $unix);
        }
    }
}

$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$counter_news = 'REX_VALUE[1]' == '' ? '5' : 'REX_VALUE[1]';
$link_id_overview = (int) 'REX_LINK[id=1 output=id]';

$category_id = 'REX_VALUE[2]' > 0 ? 'REX_VALUE[2]' : 0;
$category = $category_id > 0 ? new \D2U_News\Category($category_id, rex_clang::getCurrentId()) : false;

$heading = 'REX_VALUE[4]' != '' ? 'REX_VALUE[4]' : \Sprog\Wildcard::get('d2u_news_news');

// If News Types Plugin is activated
$selected_news_types = [];
if (rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
    $selected_news_type_ids = rex_var::toArray('REX_VALUE[3]');
    foreach ($selected_news_type_ids as $selected_news_type_id) {
        $selected_news_types[] = new \D2U_News\Type($selected_news_type_id, rex_clang::getCurrentId());
    }
}

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h2 style="font-size: 1.5em;"><?= $heading ?></h2>
	<p>Anzahl auszugebender News: REX_VALUE[1]</p>
	<p>Gewählte Kategorie: <?= false !== $category ? $category->name : 'Alle Kategorien' ?></p>
	<p>Gewählte Nachrichtenarten:
		<?php
            $first_type = true;
            foreach ($selected_news_types as $selected_news_type) {
                if ($first_type) {
                    $first_type = false;
                } else {
                    echo ', ';
                }
                echo $selected_news_type->name;
            }
        echo false !== $category ? $category->name : 'Alle Kategorien';
        ?>
	</p>
<?php
} elseif (\rex_addon::get('d2u_news') instanceof rex_addon && \rex_addon::get('d2u_news')->isAvailable()) {
    // FRONTEND
    $news = [];
    if (false !== $category) {
        $news = $category->getNews(true);
    } elseif (rex_plugin::get('d2u_news', 'news_types') instanceof rex_plugin && \rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
        // If News Types Plugin is activated: filter
        $news = \D2U_News\News::getAll(rex_clang::getCurrentId());
        if (count($selected_news_types) > 0) {
            foreach ($news as $current_news) {
                if (is_array($current_news->types) && count($current_news->types) > 0) {
                    foreach ($selected_news_types as $selected_news_type) {
                        if (!in_array($selected_news_type, $current_news->types, true)) {
                            unset($news[$current_news->news_id]);
                        }
                    }
                }
            }
        }
    } else {

        $news = \D2U_News\News::getAll(rex_clang::getCurrentId(), $counter_news, true);
    }

    // Only predefined number of news
    $news = array_slice($news, 0, $counter_news);

    if (count($news) > 0) {
    ?>
		<div class="col-12">
			<div class="row">
				<div class="col-12">
					<h2 class="h2-news"><?= $heading ?></h2>
				</div>
			</div>
			<?php
                foreach ($news as $nachricht) {
                    echo '<div class="row news">';
                    echo '<div class="col-12">';
                    echo '<div class="d2u_module_40-1_news_container news-box">';
                    echo '<div class="row">';

                    if ('' != $nachricht->picture) {
                        echo '<div class="col-12 col-sm-4">';
                        if ('' != $nachricht->getUrl()) {
                            echo '<a href="'. $nachricht->getUrl() .'">';
                        }
                        echo '<img src="index.php?rex_media_type=news_preview&rex_media_file='. $nachricht->picture .'" alt="'. $nachricht->name .'" class="listpic">';
                        if ('' != $nachricht->getUrl()) {
                            echo '</a>';
                        }
                        echo '</div>';

                        echo '<div class="col-12 col-sm-8">';
                    } else {
                        echo '<div class="col-12">';
                    }

                    echo '<h3 class="news">';
                    if ('' != $nachricht->getUrl()) {
                        echo '<a href="'. $nachricht->getUrl() .'">';
                    }
                    echo $nachricht->name;
                    if ('' != $nachricht->getUrl()) {
                        echo '</a>';
                    }
                    echo '</h3>';
                    echo '<time datetime="'. $nachricht->date .'">'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'</time>';

                    if ('' != $nachricht->teaser) {
                        echo d2u_addon_frontend_helper::prepareEditorField($nachricht->teaser);
                        if ('' != $nachricht->getUrl()) {
                            echo '<a href="'. $nachricht->getUrl() .'" class="d2u_module_40-1_more">['. $tag_open . 'd2u_news_details'. $tag_close .']</a>';
                        }
                    } elseif ('' != $nachricht->getUrl()) {
                        echo '<p class="text"><a href="'. $nachricht->getUrl() .'">'. $nachricht->getUrl() .'</a></p>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }

                if ($link_id_overview > 0 && $link_id_overview !== rex_article::getCurrentId()) {
                    echo '<div class="row">';
                    echo '<div class="col-12">';
                    echo '<a href="'. rex_getUrl($link_id_overview) .'">'. $tag_open . 'd2u_news_details'. $tag_close .'</a>';
                    echo '</div>';
                    echo '</div>';
                }
            ?>
		</div>
	<?php
    }
}
?>