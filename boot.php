<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_news[]', rex_i18n::msg('d2u_news_rights'));
	rex_perm::register('d2u_news[edit_data]', rex_i18n::msg('d2u_news_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_news[edit_lang]', rex_i18n::msg('d2u_news_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_news[settings]', rex_i18n::msg('d2u_news_rights_settings'), rex_perm::OPTIONS);
}

if(rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_news_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_news_media_is_in_use');
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_news_article_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_news_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];

	// Prepare warnings
	// Settings
	$addon = rex_addon::get("d2u_news");
	if($addon->hasConfig("article_id") && $addon->getConfig("article_id") == $article_id) {
		$message = '<a href="index.php?page=d2u_news/settings">'.
			 rex_i18n::msg('d2u_news_rights') ." - ". rex_i18n::msg('d2u_news_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return "";
	}
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_news_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$news = D2U_News\News::getAll($clang_id, 0, FALSE);
	foreach ($news as $cur_news) {
		$cur_news->delete(FALSE);
	}

	// Delete language settings
	if(rex_config::has('d2u_news', 'lang_replacement_'. $clang_id)) {
		rex_config::remove('d2u_news', 'lang_replacement_'. $clang_id);
	}
	// Delete language replacements
	d2u_news_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_news_media_is_in_use(rex_extension_point $ep) {
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
	for($i = 0; $i < $sql_news->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_news/news&func=edit&entry_id='.
			$sql_news->getValue('news_id') .'\')">'. rex_i18n::msg('d2u_news_rights') ." - ". rex_i18n::msg('d2u_news_news') .': '. $sql_news->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	
	return $warning;
}