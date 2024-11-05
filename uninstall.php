<?php

$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_news');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_news_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_categories');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_categories_lang');

// Delete language replacements
if (!class_exists(d2u_news_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_news_lang_helper.php';
}
d2u_news_lang_helper::factory()->uninstall();
