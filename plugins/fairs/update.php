<?php
$sql = rex_sql::factory();

// Update database to 1.1.2
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_news_fairs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

if (rex_version::compare($this->getVersion(), '1.1.2', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_news_fairs DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_news_fairs DROP updateuser;");
}