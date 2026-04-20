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

            if (2 == $clang_id) {
                return date('d.m.Y', $unix);
            }

            return strtoupper(date('d/m/Y', $unix));

        }
    }
}


// Messen ausgeben
$fairs = \D2U_News\Fair::getAll();

if (!is_array($fairs)) {
    $fairs = [];
}

if (count($fairs) > 0) {
    echo '<div class="col-12">';
    echo '<h2>'. \Sprog\Wildcard::get('d2u_news_fair_dates') .'</h2>';

    echo '<div class="row d2u-news-fair-grid">';
    foreach ($fairs as $fair) {
        echo '<div class="col-12 col-lg-6 d2u-news-fair-grid__item">';
        echo '<div class="d2u-news-fair-card">';
        echo '<div class="d2u-news-fair-card__date">'. formatDate($fair->date_start, rex_clang::getCurrentId()) .' - '. formatDate($fair->date_end, rex_clang::getCurrentId()) .'</div>';
        echo '<h2 class="d2u-news-fair-card__title">'. $fair->name .'</h2>';
        echo '<div class="d2u-news-fair-card__location">'. $fair->city .' | '. $fair->country_code .'</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}
