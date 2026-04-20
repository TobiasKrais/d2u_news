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
$fairs = \TobiasKrais\D2UNews\Fair::getAll();

if (count($fairs) > 0) {
    echo '<div class="col-12">';
    echo '<h2>'. \Sprog\Wildcard::get('d2u_news_fair_dates') .'</h2>';

    echo '<div class="row">';
    echo '<div class="col-sm-6">';
    echo '<ul class="dates hyphens">';
    $faircounter = 0;
    foreach ($fairs as $fair) {
        echo '<li>';
        echo formatDate($fair->date_start, rex_clang::getCurrentId()) .' - '. formatDate($fair->date_end, rex_clang::getCurrentId());
        echo ' <strong>'. $fair->name .' | '. $fair->city .', '. $fair->country_code .'</strong>';
        echo '</li>';

        ++$faircounter;
        if ($faircounter == round(count($fairs) / 2)) {
            echo '</ul>';
            echo '</div>';
            echo '<div class="col-sm-6">';
            echo '<ul class="dates hyphens">';
        }
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
