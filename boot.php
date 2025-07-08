<?php

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_news[]', rex_i18n::msg('d2u_news_rights'));
    rex_perm::register('d2u_news[edit_data]', rex_i18n::msg('d2u_news_rights_edit_data'), rex_perm::OPTIONS);
    rex_perm::register('d2u_news[edit_lang]', rex_i18n::msg('d2u_news_rights_edit_lang'), rex_perm::OPTIONS);
    rex_perm::register('d2u_news[settings]', rex_i18n::msg('d2u_news_rights_settings'), rex_perm::OPTIONS);

    rex_extension::register('ART_PRE_DELETED', rex_d2u_news_article_is_in_use(...));
    rex_extension::register('CLANG_DELETED', rex_d2u_news_clang_deleted(...));
    rex_extension::register('D2U_HELPER_TRANSLATION_LIST', rex_d2u_news_translation_list(...));
    rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_news_media_is_in_use(...));
}

/**
 * Checks if article is used by this addon.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @throws rex_api_exception If article is used
 * @return array<string> Warning message as array
 */
function rex_d2u_news_article_is_in_use(rex_extension_point $ep)
{
    $warning = [];
    $params = $ep->getParams();
    $article_id = $params['id'];

    // Prepare warnings
    // Settings
    $addon = rex_addon::get('d2u_news');
    if ($addon->hasConfig('article_id') && (int) $addon->getConfig('article_id') === $article_id) {
        $message = '<a href="index.php?page=d2u_news/settings">'.
             rex_i18n::msg('d2u_news_rights') .' - '. rex_i18n::msg('d2u_news_settings') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
    }

    if (count($warning) > 0) {
        throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') .'<ul><li>'. implode('</li><li>', $warning) .'</li></ul>');
    }

    return '';

}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_news_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $news = D2U_News\News::getAll($clang_id, 0, false);
    foreach ($news as $cur_news) {
        $cur_news->delete(false);
    }

    // Delete language settings
    if (rex_config::has('d2u_news', 'lang_replacement_'. $clang_id)) {
        rex_config::remove('d2u_news', 'lang_replacement_'. $clang_id);
    }
    // Delete language replacements
    d2u_news_lang_helper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_news_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes($params['filename']);

    // News
    $sql_news = rex_sql::factory();
    $sql_news->setQuery('SELECT lang.news_id, name FROM `' . rex::getTablePrefix() . 'd2u_news_news_lang` AS lang '
        .'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_news_news` AS news ON lang.news_id = news.news_id '
        .'WHERE picture = "'. $filename .'"');

    // Prepare warnings
    // News
    for ($i = 0; $i < $sql_news->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_news/news&func=edit&entry_id='.
            $sql_news->getValue('news_id') .'\')">'. rex_i18n::msg('d2u_news_rights') .' - '. rex_i18n::msg('d2u_news_news') .': '. $sql_news->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_news->next();
    }

    return $warning;
}

/**
 * Addon translation list.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<array<string,array<int,array<string,string>>|string>|string> Addon translation list
 */
function rex_d2u_news_translation_list(rex_extension_point $ep) {
    $params = $ep->getParams();
    $source_clang_id = (int) $params['source_clang_id'];
    $target_clang_id = (int) $params['target_clang_id'];
    $filter_type = (string) $params['filter_type'];

    $list = $ep->getSubject();
    $list_entry = [
        'addon_name' => rex_i18n::msg('d2u_news'),
        'pages' => []
    ];

    $news_categories = \D2U_News\Category::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($news_categories) > 0) {
        $html_news_categories = '<ul>';
        foreach ($news_categories as $current_news_category) {
            if ('' === $current_news_category->name) {
                $current_news_category = new \D2U_News\Category($current_news_category->category_id, $source_clang_id);
            }
            $html_news_categories .= '<li><a href="'. rex_url::backendPage('d2u_news/categories', ['entry_id' => $current_news_category->category_id, 'func' => 'edit']) .'">'. $current_news_category->name .'</a></li>';
        }
        $html_news_categories .= '</ul>';
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_helper_categories'),
            'icon' => 'rex-icon rex-icon-open-category',
            'html' => $html_news_categories
        ];
    }

    $news = \D2U_News\News::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($news) > 0) {
        $html_news = '<ul>';
        foreach ($news as $current_news) {
            if ('' === $current_news->name) {
                $current_news = new \D2U_News\News($current_news->news_id, $source_clang_id);
                if ('' === $current_news->name) {
                    foreach (rex_clang::getAllIds() as $clang_id) {
                        $current_news = new \D2U_News\News($current_news->news_id, $clang_id);
                        if ('' !== $current_news->name) {
                            break;
                        }
                    }
                }
            }
            $html_news .= '<li><a href="'. rex_url::backendPage('d2u_news/news', ['entry_id' => $current_news->news_id, 'func' => 'edit']) .'">'. $current_news->name .'</a></li>';
        }
        $html_news .= '</ul>';
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_news_news_title'),
            'icon' => 'rex-icon fa-newspaper-o',
            'html' => $html_news
        ];
    }

    if (rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
        $news_types = \D2U_News\Type::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($news_types) > 0) {
            $html_news_types = '<ul>';
            foreach ($news_types as $news_type) {
                if ('' === $news_type->name) {
                    $news_type = new \D2U_News\Type($news_type->type_id, $source_clang_id);
                }
                $html_news_types .= '<li><a href="'. rex_url::backendPage('d2u_news/news_types', ['entry_id' => $news_type->type_id, 'func' => 'edit']) .'">'. $news_type->name .'</a></li>';
            }
            $html_news_types .= '</ul>';
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_news_types'),
                'icon' => 'rex-icon fa-file-text-o',
                'html' => $html_news_types
            ];
        }
    }
    
    $list[] = $list_entry;

    return $list;
}