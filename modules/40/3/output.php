<?php
if (!function_exists('formatDate')) {
    /**
     * Formats the date for language specific options.
     * @param string $datum DAte in Format YYYY-MM-TT
     * @param int $clang_id Redaxo clang id
     * @return string Formated date
     */
    function formatDate($datum, $clang_id)
    {
        if ('' != $datum) {
            $d = explode('-', $datum);
            $unix = mktime(0, 0, 0, $d[1], $d[2], $d[0]);

            if (2 == $clang_id) {
                return date('d.m.Y', $unix);
            }

                return strtoupper(date('d/m/Y', $unix));

        }
    }
}


$counter_news = 'REX_VALUE[1]' == '' ? '5' : 'REX_VALUE[1]';
$link_id_fairs = 'REX_LINK[id=1 output=id]';

$category_id = 'REX_VALUE[2]' > 0 ? 'REX_VALUE[2]' : 0;
$category = $category_id > 0 ? new \TobiasKrais\D2UNews\Category($category_id, rex_clang::getCurrentId()) : false;

$selected_news_types = [];
$selected_news_type_ids = rex_var::toArray('REX_VALUE[3]');
if (!is_array($selected_news_type_ids)) {
    $selected_news_type_ids = [];
}
foreach ($selected_news_type_ids as $selected_news_type_id) {
    $selected_news_types[] = new \TobiasKrais\D2UNews\Type($selected_news_type_id, rex_clang::getCurrentId());
}

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
    <h2 style="font-size: 1.5em;">News</h2>
	Anzahl auszugebender News: REX_VALUE[1]
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
} elseif (\rex_addon::get('d2u_news')->isAvailable()) {
    // Ausgabe im FRONTEND
    $show_pic = true;

    $news = [];
    if (false !== $category) {
        $news = $category->getNews(true);
    } elseif (count($selected_news_types) > 0) {
        $news = \TobiasKrais\D2UNews\News::getAll(rex_clang::getCurrentId());
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

        $news = \TobiasKrais\D2UNews\News::getAll(rex_clang::getCurrentId(), $counter_news, true);
    }

    // Only predefined number of news
    $news = array_slice($news, 0, $counter_news);

    if (count($news) > 0) {
    ?>
	<div class="col-12 col-lg-8">
		<div class="row">
			<div class="col-12">
                <h2><?= \Sprog\Wildcard::get('d2u_news_news') ?></h2>
			</div>
		</div>
		<div class="row">
			<?php
                foreach ($news as $nachricht) {
                    if ($show_pic && '' != $nachricht->picture) {
                        echo '<aside class="col-12 col-sm-2">';
                        if ('' != $nachricht->getUrl()) {
                            echo '<a href="'. $nachricht->getUrl() .'">';
                        }
                        echo '<img src="index.php?rex_media_type=news_preview&rex_media_file='. $nachricht->picture .'" alt="'. $nachricht->name .'" class="listpic">';
                        if ('' != $nachricht->getUrl()) {
                            echo '</a>';
                        }
                        echo '</aside>';
                    }
            ?>
				<div class="col-12 col-sm-10">
					<?php
                        echo '<h2>';
                        if ('' != $nachricht->getUrl()) {
                            echo '<a href="'. $nachricht->getUrl() .'">';
                        }
                        echo $nachricht->name;
                        if ('' != $nachricht->getUrl()) {
                            echo '</a>';
                        }
                        echo '</h2>';
                        echo '<p><time datetime="'. $nachricht->date .'">'. formatDate($nachricht->date, rex_clang::getCurrentId()) .'</time></p>';
                    ?>
					<p class="text">
						<?= TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($nachricht->teaser);
                        ?>
					</p>
				</div>
			<?php
                }
            ?>
		</div>
	</div>
	<?php
        $fairs = \TobiasKrais\D2UNews\Fair::getAll(true);

        if (count($fairs) > 0) {
            echo '<div class="col-12 col-lg-4">';
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<h2>'. \Sprog\Wildcard::get('d2u_news_fair_dates') .'</h2>';
            echo '</div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<ul>';

            $fair_counter = 0;
            foreach ($fairs as $fair) {
                echo '<li>';
                echo '<h5>'. $fair->name .' | '. $fair->city .', '. $fair->country_code .'</h5>';
                echo formatDate($fair->date_start, rex_clang::getCurrentId()) .' - '. formatDate($fair->date_end, rex_clang::getCurrentId());
                echo '</li>';
                ++$fair_counter;
                if ($fair_counter > 4) {
                    break;
                }
            }
            echo '</ul>';
            if ($link_id_fairs > 0 && count($fairs) > 5) {
                echo '<a href="'. rex_getUrl($link_id_fairs) .'" class="arrow">'. \Sprog\Wildcard::get('d2u_news_fairs_all') .'</a>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
}
