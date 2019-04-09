<?php
/**
 * Redaxo D2U News Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_News;
/**
 * Fair
 */
class Fair {
	/**
	 * @var int Database ID
	 */
	var $fair_id = 0;
	
	/**
	 * @var string name
	 */
	var $name = "";
	
	/**
	 * @var string city
	 */
	var $city = "";
	
	/**
	 * @var string 3 digit country code
	 */
	var $country_code = "";
	
	/**
	 * @var string start date, format YYYY-MM-DD
	 */
	var $date_start = "";
	
	/**
	 * @var string end date, format YYYY-MM-DD
	 */
	var $date_end = "";
	
	/**
	 * Constructor. Reads a contact stored in database.
	 * @param int $fair_id Contact ID.
	 */
	 public function __construct($fair_id) {
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_news_fairs "
				."WHERE fair_id = ". $fair_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->fair_id = $result->getValue("fair_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->city = stripslashes($result->getValue("city"));
			$this->country_code = $result->getValue("country_code");
			$this->date_start = $result->getValue("date_start");
			$this->date_end = $result->getValue("date_end");
		}
	}
	
	/**
	 * Deletes the object.
	 */
	public function delete() {
		$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_news_fairs "
			."WHERE fair_id = ". $this->fair_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
	}

	/**
	 * Get all contacts.
	 * @param boolean $current_only only fairs in future, not in past
	 * @return Fair[] Array with Contact objects.
	 */
	public static function getAll($current_only = TRUE) {
		$query = "SELECT fair_id FROM ". \rex::getTablePrefix() ."d2u_news_fairs ";
		if($current_only) {
			$query .= "WHERE date_end > '". date('Y-m-d') ."'";
		}
		$query .= "ORDER BY date_start, date_end";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$fairs = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$fairs[] = new Fair($result->getValue("fair_id"));
			$result->next();
		}
		return $fairs;
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return in error code if error occurs
	 */
	public function save() {
		$error = 0;

		$query = \rex::getTablePrefix() ."d2u_news_fairs SET "
				."name = '". addslashes($this->name) ."', "
				."city = '". addslashes($this->city) ."', "
				."country_code = '". $this->country_code ."', "
				."date_start = '". $this->date_start ."', "
				."date_end = '". $this->date_end ."' ";

		if($this->fair_id == 0) {
			$query = "INSERT INTO ". $query;
		}
		else {
			$query = "UPDATE ". $query ." WHERE fair_id = ". $this->fair_id;
		}

		$result = \rex_sql::factory();
		$result->setQuery($query);
		if($this->fair_id == 0) {
			$this->fair_id = $result->getLastId();
			$error = $result->hasError();
		}
		
		return $error;
	}
}