<?php
/**
 * Redaxo D2U Immo Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_News;

/**
 * News Category
 */
class Category implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $category_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";

	/**
	 * @var string Preview picture file name 
	 */
	var $picture = "";
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var int Unix timestamp containing the last update date
	 */
	var $updatedate = 0;
	
	/**
	 * @var string Redaxo update user name
	 */
	var $updateuser = "";

	/**
	 * Constructor. Reads a category stored in database.
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($category_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_categories AS categories "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_categories_lang AS lang "
					."ON categories.category_id = lang.category_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE categories.category_id = ". $category_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->category_id = $result->getValue("category_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->picture = $result->getValue("picture");
			$this->priority = $result->getValue("priority");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_categories_lang "
			."WHERE category_id = ". $this->category_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_categories_lang "
			."WHERE category_id = ". $this->category_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_categories "
				."WHERE category_id = ". $this->category_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(TRUE);			
		}
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @return Category[] Array with Category objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.category_id FROM ". \rex::getTablePrefix() ."d2u_news_categories_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_categories AS categories "
				."ON lang.category_id = categories.category_id "
			."WHERE clang_id = ". $clang_id ." ";
		if(\rex_addon::get('d2u_news')->getConfig('default_sort', 'name') == 'priority') {
			$query .= 'ORDER BY priority';
		}
		else {
			$query .= 'ORDER BY name';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[] = new Category($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		return $categories;
	}
	
	/**
	 * Gets the news of the category.
	 * @param boolean $only_online Show only online news
	 * @return News[] News of this category
	 */
	public function getNews($only_online = FALSE) {
		$query = "SELECT lang.news_id FROM ". \rex::getTablePrefix() ."d2u_news_news_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_news AS news "
					."ON lang.news_id = news.news_id "
			."WHERE category_ids LIKE '%|". $this->category_id ."|%' AND clang_id = ". $this->clang_id ." ";
		if($only_online) {
			$query .= "AND online_status = 'online' ";
		}
		$query .= 'ORDER BY date ASC';
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$news = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$news[$result->getValue("news_id")] = new News($result->getValue("news_id"), $this->clang_id);
			$result->next();
		}
		return $news;
	}

	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Category[] Array with Category objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT category_id FROM '. \rex::getTablePrefix() .'d2u_news_categories_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.category_id FROM '. \rex::getTablePrefix() .'d2u_news_categories AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_news_categories_lang AS target_lang '
						.'ON main.category_id = target_lang.category_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_news_categories_lang AS default_lang '
						.'ON main.category_id = default_lang.category_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.category_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Category($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_category = new Category($this->category_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority != $pre_save_category->priority || $this->category_id == 0) {
			$this->setPriority();
		}

		if($this->category_id == 0 || $pre_save_category != $this) {
			$query = \rex::getTablePrefix() ."d2u_news_categories SET "
					."priority = ". $this->priority .", "
					."picture = '". $this->picture ."' ";

			if($this->category_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id == 0) {
				$this->category_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_category = new Category($this->category_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_news_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		// Update URLs
		if(\rex_addon::get("url")->isAvailable()) {
			\UrlGenerator::generatePathFile([]);
		}
		
		return $error;
	}
	
	/**
	 * Reassigns priorities in database.
	 * @param boolean $delete Reorder priority after deletion
	 */
	private function setPriority($delete = FALSE) {
		// Pull prios from database
		$query = "SELECT category_id, priority FROM ". \rex::getTablePrefix() ."d2u_news_categories "
			."WHERE category_id <> ". $this->category_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high or was deleted, simply add at end 
		if($this->priority > $result->getRows() || $delete) {
			$this->priority = $result->getRows() + 1;
		}

		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[$result->getValue("priority")] = $result->getValue("category_id");
			$result->next();
		}
		array_splice($categories, ($this->priority - 1), 0, [$this->category_id]);

		// Save all prios
		foreach($categories as $prio => $category_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_news_categories "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE category_id = ". $category_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}