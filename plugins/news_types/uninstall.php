<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_types');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_types_lang');
$sql->setQuery('ALTER TABLE ' . \rex::getTablePrefix() . 'd2u_news_news DROP type_ids;');