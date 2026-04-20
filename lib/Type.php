<?php
/**
 * Redaxo D2U News Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace TobiasKrais\D2UNews;

use rex;
use rex_addon;
use rex_config;
use rex_sql;

/**
 * News Type.
 */
class Type implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database ID */
    public int $type_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a type stored in database.
     * @param int $type_id type ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($type_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_news_types AS types '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_types_lang AS lang '
                    .'ON types.type_id = lang.type_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE types.type_id = '. $type_id;
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            $this->type_id = (int) $result->getValue('type_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->priority = (int) $result->getValue('priority');
            if ('' !== (string) $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_news_types_lang '
            .'WHERE type_id = '. $this->type_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_news_types_lang '
            .'WHERE type_id = '. $this->type_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_news_types '
                .'WHERE type_id = '. $this->type_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            $this->setPriority(true);
        }
    }

    /**
     * Get all types.
     * @param int $clang_id redaxo clang id
     * @return Type[] array with Type objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT lang.type_id FROM '. rex::getTablePrefix() .'d2u_news_types_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_types AS types '
                .'ON lang.type_id = types.type_id '
            .'WHERE clang_id = '. $clang_id .' ';
        if ('priority' == rex_addon::get('d2u_news')->getConfig('default_sort', 'name')) {
            $query .= 'ORDER BY priority';
        } else {
            $query .= 'ORDER BY name';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $types = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $types[] = new self($result->getValue('type_id'), $clang_id);
            $result->next();
        }

        return $types;
    }

    /**
     * Gets the news of the type.
     * @param bool $only_online Show only online news
     * @return News[] News of this type
     */
    public function getNews($only_online = false)
    {
        $query = 'SELECT lang.news_id FROM '. rex::getTablePrefix() .'d2u_news_news_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_news AS news '
                    .'ON lang.news_id = news.news_id '
            ."WHERE type_ids LIKE '%|". $this->type_id ."|%' AND clang_id = ". $this->clang_id .' ';
        if ($only_online) {
            $query .= "AND online_status = 'online' ";
        }
        $query .= 'ORDER BY `date` DESC';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $news = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $news[] = new News($result->getValue('news_id'), $this->clang_id);
            $result->next();
        }

        return $news;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Type[] array with Type objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT type_id FROM '. rex::getTablePrefix() .'d2u_news_types_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.type_id FROM '. rex::getTablePrefix() .'d2u_news_types AS main '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_types_lang AS target_lang '
                        .'ON main.type_id = target_lang.type_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_types_lang AS default_lang '
                        .'ON main.type_id = default_lang.type_id AND default_lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.type_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) rex_config::get('d2u_helper', 'default_lang');
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self($result->getValue('type_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = false;

        $pre_save_type = new self($this->type_id, $this->clang_id);

        if ($this->priority !== $pre_save_type->priority || 0 === $this->type_id) {
            $this->setPriority();
        }

        if (0 === $this->type_id || $pre_save_type !== $this) {
            $query = rex::getTablePrefix() .'d2u_news_types SET '
                    .'priority = '. $this->priority .' ';

            if (0 === $this->type_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE type_id = '. $this->type_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->type_id) {
                $this->type_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (!$error) {
            $pre_save_type = new self($this->type_id, $this->clang_id);
            if ($pre_save_type !== $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_news_types_lang SET '
                        ."type_id = '". $this->type_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."'";

                $result = rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
        }

        return $error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        $query = 'SELECT type_id FROM '. rex::getTablePrefix() .'d2u_news_types '
            .'WHERE type_id <> '. $this->type_id .' ORDER BY priority, type_id';
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = $result->getRows() + 1;
        }

        $types = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $types[] = (int) $result->getValue('type_id');
            $result->next();
        }
        array_splice($types, $this->priority - 1, 0, [$this->type_id]);

        foreach ($types as $prio => $type_id) {
            $query = 'UPDATE '. rex::getTablePrefix() .'d2u_news_types '
                    .'SET priority = '. ((int) $prio + 1) .' '
                    .'WHERE type_id = '. $type_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
        }
    }
}