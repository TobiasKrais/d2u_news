<?php
/**
 * Redaxo D2U Immo Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_News;

/**
 * News Type
 */
class Type implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $type_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";

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
	 * @param int $type_id Type ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($type_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_types AS types "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_types_lang AS lang "
					."ON types.type_id = lang.type_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE types.type_id = ". $type_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->type_id = $result->getValue("type_id");
			$this->name = stripslashes($result->getValue("name"));
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
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_types_lang "
			."WHERE type_id = ". $this->type_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_types_lang "
			."WHERE type_id = ". $this->type_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_types "
				."WHERE type_id = ". $this->type_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(TRUE);			
		}
	}
	
	/**
	 * Get all types.
	 * @param int $clang_id Redaxo clang id.
	 * @return Type[] Array with Type objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.type_id FROM ". \rex::getTablePrefix() ."d2u_news_types_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_types AS types "
				."ON lang.type_id = types.type_id "
			."WHERE clang_id = ". $clang_id ." ";
		if(\rex_addon::get('d2u_news')->getConfig('default_sort', 'name') == 'priority') {
			$query .= 'ORDER BY priority';
		}
		else {
			$query .= 'ORDER BY name';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$types = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$types[] = new Type($result->getValue("type_id"), $clang_id);
			$result->next();
		}
		return $types;
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
			."WHERE type_ids LIKE '%|". $this->type_id ."|%' AND clang_id = ". $this->clang_id ." ";
		if($only_online) {
			$query .= "AND online_status = 'online' ";
		}
		if(\rex_addon::get('d2u_news')->getConfig('default_sort', 'name') == 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY name ASC';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$news = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$news[] = new News($result->getValue("news_id"), $this->clang_id);
			$result->next();
		}
		return $news;
	}

	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Type[] Array with Type objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT type_id FROM '. \rex::getTablePrefix() .'d2u_news_types_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.type_id FROM '. \rex::getTablePrefix() .'d2u_news_types AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_news_types_lang AS target_lang '
						.'ON main.type_id = target_lang.type_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_news_types_lang AS default_lang '
						.'ON main.type_id = default_lang.type_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.type_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Type($result->getValue("type_id"), $clang_id);
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
		$pre_save_category = new Type($this->type_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority != $pre_save_category->priority || $this->type_id == 0) {
			$this->setPriority();
		}

		if($this->type_id == 0 || $pre_save_category != $this) {
			$query = \rex::getTablePrefix() ."d2u_news_types SET "
					."priority = ". $this->priority ." ";

			if($this->type_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE type_id = ". $this->type_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->type_id == 0) {
				$this->type_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_category = new Type($this->type_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_news_types_lang SET "
						."type_id = '". $this->type_id ."', "
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
		$query = "SELECT type_id, priority FROM ". \rex::getTablePrefix() ."d2u_news_types "
			."WHERE type_id <> ". $this->type_id ." ORDER BY priority";
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

		$types = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$types[$result->getValue("priority")] = $result->getValue("type_id");
			$result->next();
		}
		array_splice($types, ($this->priority - 1), 0, [$this->type_id]);

		// Save all prios
		foreach($types as $prio => $type_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_news_types "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE type_id = ". $type_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}