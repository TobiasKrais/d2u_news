<?php
\rex_sql_table::get(\rex::getTable('d2u_news_fairs'))
	->ensureColumn(new rex_sql_column('fair_id', 'int(10) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('fair_id')
	->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('city', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('country_code', 'VARCHAR(3)'))
    ->ensureColumn(new \rex_sql_column('date_start', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('date_end', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)'))
    ->ensure();