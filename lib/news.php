<?php
/**
 * Redaxo D2U News Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_News;

/**
 * News
 */
class News implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $news_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Short description
	 */
	var $teaser = "";
	
	/**
	 * @var string Online status. Either "online", "offline" or "archived".
	 */
	var $online_status = "";

	/**
	 * @var string Picture file name
	 */
	var $picture = "";
	
	/**
	 * @var Category[] Array containing category objects. 
	 */
	var $categories = [];

	/**
	 * @var Type[] Array containing type objects. 
	 */
	var $types = [];

	/**
	 * @var string Type of link, either "none" (default), "article", "url" or "machine"
	 */
	var $link_type = 'none';
	
	/**
	 * @var string external URL
	 */
	var $url = '';
	
	/**
	 * @var string News URL depending on news type
	 */
	private $news_url = '';
	
	/**
	 * @var int Redaxo article id
	 */
	var $article_id = 0;
	
	/**
	 * @var int machine ID if linktype is "machine"
	 */
	var $d2u_machines_machine_id = 0;
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string Date in format YYYY-MM-DD.
	 */
	var $date = "";
	
	/**
	 * Constructor. Reads the object stored in database.
	 * @param int $news_id News ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($news_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_news AS news "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_news_lang AS lang "
					."ON news.news_id = lang.news_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE news.news_id = ". $news_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->news_id = $result->getValue("news_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->teaser = stripslashes(htmlspecialchars_decode($result->getValue("teaser")));
			$category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
			foreach ($category_ids as $category_id) {
				$this->categories[$category_id] = new Category($category_id, $clang_id);
			}
			$this->link_type = $result->getValue("link_type");
			$this->article_id = $result->getValue("article_id");
			$this->url = $result->getValue("url");
			$this->d2u_machines_machine_id = $result->getValue("d2u_machines_machine_id");
			$this->online_status = $result->getValue("online_status");
			$this->picture = $result->getValue("picture");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->date = $result->getValue("date");

			if(\rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
				$type_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("type_ids")), PREG_GREP_INVERT);
				foreach ($type_ids as $type_id) {
					$this->types[$type_id] = new Type($type_id, $clang_id);
				}
			}
		}
	}
	
	/**
	 * Changes the online status of this object
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->news_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_news_news "
					."SET online_status = 'offline' "
					."WHERE news_id = ". $this->news_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->news_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_news_news "
					."SET online_status = 'online' "
					."WHERE news_id = ". $this->news_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}

	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_news_lang "
			."WHERE news_id = ". $this->news_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_news_lang "
			."WHERE news_id = ". $this->news_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_news "
				."WHERE news_id = ". $this->news_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all news.
	 * @param int $clang_id Redaxo clang id.
	 * @param int $limit maximum number of news, 0 = all
	 * @param boolean $online_only only online news
	 * @return News[] Array with News objects.
	 */
	public static function getAll($clang_id, $limit = 0, $online_only = TRUE) {
		$query = "SELECT lang.news_id FROM ". \rex::getTablePrefix() ."d2u_news_news_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_news AS news "
				."ON lang.news_id = news.news_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($online_only) {
			$query .= "AND online_status = 'online' ";
		}
		$query .='ORDER BY `date` DESC';
		if($limit > 0) {
			$query .= " LIMIT 0, ". $limit;
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$news = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$news[$result->getValue("news_id")] = new News($result->getValue("news_id"), $clang_id);
			$result->next();
		}
		return $news;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return News[] Array with News objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT news_id FROM '. \rex::getTablePrefix() .'d2u_news_news_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.news_id FROM '. \rex::getTablePrefix() .'d2u_news_news AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_news_news_lang AS target_lang '
						.'ON main.news_id = target_lang.news_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_news_news_lang AS default_lang '
						.'ON main.news_id = default_lang.news_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.news_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new News($result->getValue("news_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }

	/**
	 * Get URL, depending on news type
	 * @return string News URL
	 */
	public function getUrl() {
		if($this->news_url == "") {
			if($this->link_type == "article" && $this->article_id > 0) {
				$this->news_url = rex_getUrl($this->article_id);
			}
			else if($this->link_type == "url") {
				$this->news_url = $this->url;
			}
			else if($this->link_type == "machine") {
				$machine = new \Machine($this->d2u_machines_machine_id, $this->clang_id);
				$this->news_url = $machine->getURL();
			}
		}
		return $this->news_url;
    }
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_news = new News($this->news_id, $this->clang_id);
	
		if($this->news_id == 0 || $pre_save_news != $this) {
			$query = \rex::getTablePrefix() ."d2u_news_news SET "
					."online_status = '". $this->online_status ."', "
					."category_ids = '|". implode("|", array_keys($this->categories)) ."|', "
					."picture = '". $this->picture ."', "
					."link_type = '". $this->link_type ."', "
					."article_id = ". $this->article_id .", "
					."url = '". $this->url ."', "
					."d2u_machines_machine_id = ". $this->d2u_machines_machine_id .", "
					."`date` = '". $this->date ."' ";
			if(\rex_plugin::get("d2u_news", "news_types")->isAvailable()) {
				$query .= ", type_ids = '|". implode("|", array_keys($this->types)) ."|' ";
			}

			if($this->news_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE news_id = ". $this->news_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->news_id == 0) {
				$this->news_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_news = new News($this->news_id, $this->clang_id);
			if($pre_save_news != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_news_news_lang SET "
						."news_id = '". $this->news_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". addslashes($this->name) ."', "
						."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";

				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		return $error;
	}
}