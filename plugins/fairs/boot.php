<?php

if (rex::isBackend()) {
    rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_news_fairs_media_is_in_use');
}

/**
 * Checks if media is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_news_fairs_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    // Fairs
    $sql = rex_sql::factory();
    $sql->setQuery('SELECT fair_id, name FROM `' . rex::getTablePrefix() . 'd2u_news_fairs` '
        .'WHERE picture = "'. $filename .'"');

    // Prepare warnings
    // Fairs
    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_news/fairs&func=edit&entry_id='.
            $sql->getValue('fair_id') .'\')">'. rex_i18n::msg('d2u_news_rights') .' - '. rex_i18n::msg('d2u_news_fairs') .': '. $sql->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql->next();
    }

    return $warning;
}
