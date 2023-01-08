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
	var int $news_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var int $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var string $name = "";
	
	/**
	 * @var string Short description
	 */
	var string $teaser = "";
	
	/**
	 * @var string Online status. Either "online", "offline" or "archived".
	 */
	var string $online_status = "";

	/**
	 * @var string Picture file name
	 */
	var string $picture = "";
	
	/**
	 * @var array<int, \D2U_News\Category> Array containing category objects. 
	 */
	var array $categories = [];

	/**
	 * @var array<int, \D2U_News\Type> Array containing type objects. 
	 */
	var array $types = [];

	/**
	 * @var string Type of link, either "none" (default), "article", "url" or "machine"
	 */
	var string $link_type = 'none';
	
	/**
	 * @var string external URL
	 */
	var string $url = '';
	
	/**
	 * @var string News URL depending on news type
	 */
	private string $news_url = '';
	
	/**
	 * @var int Redaxo article id
	 */
	var int $article_id = 0;
	
	/**
	 * @var int machine ID if linktype is "machine"
	 */
	var int $d2u_machines_machine_id = 0;

	/**
	 * @var int machine ID if linktype is "machine"
	 */
	var int $d2u_courses_course_id = 0;

	/**
	 * @var string "yes" if translation needs update
	 */
	var string $translation_needs_update = "delete";

	/**
	 * @var string Date in format YYYY-MM-DD.
	 */
	var string $date = "";
	
	/**
	 * @var boolean Indicator for hiding news in this special language
	 */
	var $hide_this_lang = false;
	
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
			$this->news_id = (int) $result->getValue("news_id");
			$this->name = stripslashes((string) $result->getValue("name"));
			$this->teaser = stripslashes(htmlspecialchars_decode((string) $result->getValue("teaser")));
			$category_ids = preg_grep('/^\s*$/s', explode("|", (string) $result->getValue("category_ids")), PREG_GREP_INVERT);
			$category_ids = is_array($category_ids) ? array_map('intval', $category_ids) : [];
			foreach ($category_ids as $category_id) {
				$this->categories[$category_id] = new Category($category_id, $clang_id);
			}
			$this->link_type = (string) $result->getValue("link_type");
			$this->article_id = intval($result->getValue("article_id"));
			$this->url = (string) $result->getValue("url");
			$this->d2u_machines_machine_id = intval($result->getValue("d2u_machines_machine_id"));
			$this->d2u_courses_course_id = intval($result->getValue("d2u_courses_course_id"));
			$this->online_status = (string) $result->getValue("online_status");
			$this->hide_this_lang = $result->getValue("hide_this_lang") == 1 ? true : false;
			$this->picture = (string) $result->getValue("picture");
			if($result->getValue("translation_needs_update")) {
				$this->translation_needs_update = (string) $result->getValue("translation_needs_update");
			}
			$this->date = (string) $result->getValue("date");

			if(\rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
				$type_ids = preg_grep('/^\s*$/s', explode("|", (string) $result->getValue("type_ids")), PREG_GREP_INVERT);
				$type_ids = is_array($category_ids) ? array_map('intval', $category_ids) : [];
				foreach ($type_ids as $type_id) {
					$this->types[$type_id] = new Type($type_id, $clang_id);
				}
			}
		}
	}
	
	/**
	 * Changes the online status of this object
	 */
	public function changeStatus():void {
		if($this->online_status === "online") {
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
	 * @param bool $delete_all If true, all translations and main object are deleted. If 
	 * false, only this translation will be deleted.
	 */
	public function delete($delete_all = true):void {
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
		if(intval($result_main->getRows()) === 0) {
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
	public static function getAll($clang_id, $limit = 0, $online_only = true) {
		$query = "SELECT lang.news_id FROM ". \rex::getTablePrefix() ."d2u_news_news_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_news_news AS news "
				."ON lang.news_id = news.news_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($online_only) {
			$query .= "AND online_status = 'online' AND hide_this_lang = 0 ";
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
		if($type === 'missing') {
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
			else if($this->link_type == "machine" && \rex_addon::get('d2u_machinery')->isAvailable()) {
				$machine = new \Machine($this->d2u_machines_machine_id, $this->clang_id);
				$this->news_url = $machine->getURL();
			}
			else if($this->link_type == "course" && \rex_addon::get('d2u_courses')->isAvailable()) {
				$course = new \D2U_Courses\Course($this->d2u_machines_machine_id);
				$this->news_url = $course->getURL();
			}
		}
		return $this->news_url;
    }
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean true if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_news = new News($this->news_id, $this->clang_id);
	
		if($this->news_id === 0 || $pre_save_news != $this) {
			$query = \rex::getTablePrefix() ."d2u_news_news SET "
					."online_status = '". $this->online_status ."', "
					."category_ids = '|". implode("|", array_keys($this->categories)) ."|', "
					."picture = '". $this->picture ."', "
					."link_type = '". $this->link_type ."', "
					."article_id = ". (int)$this->article_id .", "
					."url = '". $this->url ."', "
					."d2u_machines_machine_id = ". $this->d2u_machines_machine_id .", "
					."d2u_courses_course_id = ". $this->d2u_courses_course_id .", "
					."`date` = '". $this->date ."' ";
			if(\rex_plugin::get("d2u_news", "news_types")->isAvailable()) {
				$query .= ", type_ids = '|". implode("|", array_keys($this->types)) ."|' ";
			}

			if($this->news_id === 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE news_id = ". $this->news_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->news_id === 0) {
				$this->news_id = intval($result->getLastId());
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_news = new News($this->news_id, $this->clang_id);
			if($pre_save_news != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_news_news_lang SET "
						."news_id = ". $this->news_id .", "
						."clang_id = ". $this->clang_id .", "
						."name = '". addslashes($this->name) ."', "
						."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
						."hide_this_lang = ". (int) $this->hide_this_lang .", "
						."translation_needs_update = '". $this->translation_needs_update ."' ";
				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		return $error;
	}
}