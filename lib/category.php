<?php
/**
 * Redaxo D2U Immo Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_News;

use rex;
use rex_addon;
use rex_config;
use rex_sql;

/**
 * News Category.
 */
class Category implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public int $category_id = 0;

    /** @var int Redaxo clang id */
    public int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /** @var string Preview picture file name */
    public string $picture = '';

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /**
     * Constructor. Reads a category stored in database.
     * @param int $category_id category ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($category_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_news_categories AS categories '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_categories_lang AS lang '
                    .'ON categories.category_id = lang.category_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE categories.category_id = '. $category_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->category_id = (int) $result->getValue('category_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->picture = (string) $result->getValue('picture');
            $this->priority = (int) $result->getValue('priority');
            if ('' !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_news_categories_lang '
            .'WHERE category_id = '. $this->category_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_news_categories_lang '
            .'WHERE category_id = '. $this->category_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_news_categories '
                .'WHERE category_id = '. $this->category_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

    /**
     * Get all categories.
     * @param int $clang_id redaxo clang id
     * @return Category[] array with Category objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT lang.category_id FROM '. rex::getTablePrefix() .'d2u_news_categories_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_categories AS categories '
                .'ON lang.category_id = categories.category_id '
            .'WHERE clang_id = '. $clang_id .' ';
        if ('priority' === rex_addon::get('d2u_news')->getConfig('default_sort', 'name')) {
            $query .= 'ORDER BY priority';
        } else {
            $query .= 'ORDER BY name';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $categories = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $categories[] = new self((int) $result->getValue('category_id'), $clang_id);
            $result->next();
        }
        return $categories;
    }

    /**
     * Gets the news of the category.
     * @param bool $only_online Show only online news
     * @return News[] News of this category
     */
    public function getNews($only_online = false)
    {
        $query = 'SELECT lang.news_id FROM '. rex::getTablePrefix() .'d2u_news_news_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_news AS news '
                    .'ON lang.news_id = news.news_id '
            ."WHERE category_ids LIKE '%|". $this->category_id ."|%' AND clang_id = ". $this->clang_id .' ';
        if ($only_online) {
            $query .= "AND online_status = 'online' ";
        }
        $query .= 'ORDER BY `date` DESC';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $news = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $news[$result->getValue('news_id')] = new News($result->getValue('news_id'), $this->clang_id);
            $result->next();
        }
        return $news;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Category[] array with Category objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT category_id FROM '. rex::getTablePrefix() .'d2u_news_categories_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.category_id FROM '. rex::getTablePrefix() .'d2u_news_categories AS main '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_categories_lang AS target_lang '
                        .'ON main.category_id = target_lang.category_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_categories_lang AS default_lang '
                        .'ON main.category_id = default_lang.category_id AND default_lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.category_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) rex_config::get('d2u_helper', 'default_lang');
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('category_id'), $clang_id);
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

        // Save the not language specific part
        $pre_save_category = new self($this->category_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_category->priority || 0 === $this->category_id) {
            $this->setPriority();
        }

        if (0 === $this->category_id || $pre_save_category !== $this) {
            $query = rex::getTablePrefix() .'d2u_news_categories SET '
                    .'priority = '. $this->priority .', '
                    ."picture = '". $this->picture ."' ";

            if (0 === $this->category_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE category_id = '. $this->category_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->category_id) {
                $this->category_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (!$error) {
            // Save the language specific part
            $pre_save_category = new self($this->category_id, $this->clang_id);
            if ($pre_save_category !== $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_news_categories_lang SET '
                        ."category_id = '". $this->category_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."' ";

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
        // Pull prios from database
        $query = 'SELECT category_id, priority FROM '. rex::getTablePrefix() .'d2u_news_categories '
            .'WHERE category_id <> '. $this->category_id .' ORDER BY priority';
        $result = rex_sql::factory();
        $result->setQuery($query);

        // When priority is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = (int) $result->getRows() + 1;
        }

        $categories = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $categories[$result->getValue('priority')] = $result->getValue('category_id');
            $result->next();
        }
        array_splice($categories, $this->priority - 1, 0, [$this->category_id]);

        // Save all prios
        foreach ($categories as $prio => $category_id) {
            $query = 'UPDATE '. rex::getTablePrefix() .'d2u_news_categories '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE category_id = '. $category_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
