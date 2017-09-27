<?php
/**
 * Offers helper functions for language issues
 */
class d2u_news_lang_helper {
	/**
	 * @var string[] Array with english replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_news_details' => 'more Information',
		'd2u_news_fair_dates' => 'Fair dates',
		'd2u_news_fairs_all' => 'All Fair dates',
		'd2u_news_news' => 'News',
	];
	
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_news_details' => 'Weitere Informationen',
		'd2u_news_fair_dates' => 'Messetermine',
		'd2u_news_fairs_all' => 'Alle Messetermine',
		'd2u_news_news' => 'News',
	];
	
	/**
	 * @var string[] Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_french = [
		'd2u_news_details' => "Plus d'informations",
		'd2u_news_fair_dates' => 'les dates des salons internationaux',
		'd2u_news_fairs_all' => 'Toutes les dates des salons internationaux',
		'd2u_news_news' => 'News',
	];
	
	/**
	 * @var string[] Array with spanish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_spanish = [
		'd2u_news_details' => 'Para obtener más información',
		'd2u_news_fair_dates' => 'Calendario ferias',
		'd2u_news_fairs_all' => 'Todos Calendario ferias',
		'd2u_news_news' => 'Noticias',
	];

	/**
	 * @var string[] Array with italian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_italian = [
		'd2u_news_details' => 'Per ulteriori informazioni',
		'd2u_news_fair_dates' => 'Calendario fiere',
		'd2u_news_fairs_all' => 'Tutti Calendario fiere',
		'd2u_news_news' => 'Notizie',
	];

	/**
	 * @var string[] Array with polish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_polish = [
		'd2u_news_details' => 'Aby uzyskać więcej informacji',
		'd2u_news_fair_dates' => 'Daty godziwe',
		'd2u_news_fairs_all' => 'Wszystko Daty godziwe',
		'd2u_news_news' => 'Aktualności',
	];
	
	/**
	 * @var string[] Array with dutch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_dutch = [
		'd2u_news_details' => 'Voor meer informatie',
		'd2u_news_fair_dates' => 'Beursdata',
		'd2u_news_fairs_all' => 'Alle Beursdata',
		'd2u_news_news' => 'Nieuws',
	];

	/**
	 * @var string[] Array with czech replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_czech = [
		'd2u_news_details' => 'Pro více informací',
		'd2u_news_fair_dates' => 'Reálné data',
		'd2u_news_fairs_all' => 'Vše Reálné data',
		'd2u_news_news' => 'Novinky',
	];

	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_news_details' => 'Для получения дополнительной информации',
		'd2u_news_fair_dates' => 'даты выставки',
		'd2u_news_fairs_all' => 'все даты выставки',
		'd2u_news_news' => 'Новости',
	];

	/**
	 * @var string[] Array with portuguese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_portuguese = [
		'd2u_news_details' => 'Para mais informações',
		'd2u_news_fair_dates' => 'Datas Feira',
		'd2u_news_fairs_all' => 'Todos Datas Feira',
		'd2u_news_news' => 'Notícias',
	];

	/**
	 * @var string[] Array with chinese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_chinese = [
		'd2u_news_details' => '其他信息',
		'd2u_news_fair_dates' => '展会时间',
		'd2u_news_fairs_all' => '所有 展会时间',
		'd2u_news_news' => '新闻动态',
	];
	
	/**
	 * Factory method.
	 * @return d2u_immo_lang_helper Object
	 */
	public static function factory() {
		return new d2u_news_lang_helper();
	}
	
	/**
	 * Installs the replacement table for this addon.
	 */
	public function install() {
		$d2u_news = rex_addon::get('d2u_news');
		
		foreach($this->replacements_english as $key => $value) {
			$addWildcard = rex_sql::factory();

			foreach (rex_clang::getAllIds() as $clang_id) {
				// Load values for input
				if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'chinese'
					&& isset($this->replacements_chinese) && isset($this->replacements_chinese[$key])) {
					$value = $this->replacements_chinese[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'czech'
					&& isset($this->replacements_czech) && isset($this->replacements_czech[$key])) {
					$value = $this->replacements_czech[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'dutch'
					&& isset($this->replacements_dutch) && isset($this->replacements_dutch[$key])) {
					$value = $this->replacements_dutch[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'french'
					&& isset($this->replacements_french) && isset($this->replacements_french[$key])) {
					$value = $this->replacements_french[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'german'
					&& isset($this->replacements_german) && isset($this->replacements_german[$key])) {
					$value = $this->replacements_german[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'italian'
					&& isset($this->replacements_italian) && isset($this->replacements_italian[$key])) {
					$value = $this->replacements_italian[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'polish'
					&& isset($this->replacements_polish) && isset($this->replacements_polish[$key])) {
					$value = $this->replacements_polish[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'portuguese'
					&& isset($this->replacements_portuguese) && isset($this->replacements_portuguese[$key])) {
					$value = $this->replacements_portuguese[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'russian'
					&& isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
					$value = $this->replacements_russian[$key];
				}
				else if($d2u_machinery->hasConfig('lang_replacement_'. $clang_id) && $d2u_machinery->getConfig('lang_replacement_'. $clang_id) == 'spanish'
					&& isset($this->replacements_spanish) && isset($this->replacements_spanish[$key])) {
					$value = $this->replacements_spanish[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				if(rex_addon::get('sprog')->isAvailable()) {
					$select_pid_query = "SELECT pid FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND clang_id = ". $clang_id;
					$select_pid_sql = rex_sql::factory();
					$select_pid_sql->setQuery($select_pid_query);
					if($select_pid_sql->getRows() > 0) {
						// Update
						$query = "UPDATE ". rex::getTablePrefix() ."sprog_wildcard SET "
							."`replace` = '". addslashes($value) ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."' "
							."WHERE pid = ". $select_pid_sql->getValue('pid');
						$sql = rex_sql::factory();
						$sql->setQuery($query);						
					}
					else {
						$id = 1;
						// Before inserting: id (not pid) must be same in all langs
						$select_id_query = "SELECT id FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND id > 0";
						$select_id_sql = rex_sql::factory();
						$select_id_sql->setQuery($select_id_query);
						if($select_id_sql->getRows() > 0) {
							$id = $select_id_sql->getValue('id');
						}
						else {
							$select_id_query = "SELECT MAX(id) + 1 AS max_id FROM ". rex::getTablePrefix() ."sprog_wildcard";
							$select_id_sql = rex_sql::factory();
							$select_id_sql->setQuery($select_id_query);
							if($select_id_sql->getValue('max_id') != NULL) {
								$id = $select_id_sql->getValue('max_id');
							}
						}
						// Save
						$query = "INSERT INTO ". rex::getTablePrefix() ."sprog_wildcard SET "
							."id = ". $id .", "
							."clang_id = ". $clang_id .", "
							."wildcard = '". $key ."', "
							."`replace` = '". addslashes($value) ."', "
							."createdate = '". rex_sql::datetime() ."', "
							."createuser = '". rex::getUser()->getValue('login') ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."'";
						$sql = rex_sql::factory();
						$sql->setQuery($query);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls the replacement table for this addon.
	 */
	public function uninstall() {
		foreach($this->replacements_english as $key => $value) {
			if(rex_addon::get('sprog')->isAvailable()) {
				// Delete 
				$query = "DELETE FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."'";
				$select = rex_sql::factory();
				$select->setQuery($query);
			}
		}
	}
}