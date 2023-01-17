<?php
/**
 * Offers helper functions for language issues
 */
class d2u_news_lang_helper extends \D2U_Helper\ALangHelper {
	/**
	 * @var array<string, string> Array with english replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	var $replacements_english = [
		'd2u_news_details' => 'more Information',
		'd2u_news_fair_dates' => 'Fair dates',
		'd2u_news_fairs_all' => 'All Fair dates',
		'd2u_news_news' => 'News',
	];
	
	/**
	 * @var array<string, string> Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_german = [
		'd2u_news_details' => 'Weitere Informationen',
		'd2u_news_fair_dates' => 'Messetermine',
		'd2u_news_fairs_all' => 'Alle Messetermine',
		'd2u_news_news' => 'News',
	];
	
	/**
	 * @var array<string, string> Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_french = [
		'd2u_news_details' => "Plus d'informations",
		'd2u_news_fair_dates' => 'les dates des salons internationaux',
		'd2u_news_fairs_all' => 'Toutes les dates des salons internationaux',
		'd2u_news_news' => 'News',
	];
	
	/**
	 * @var array<string, string> Array with spanish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_spanish = [
		'd2u_news_details' => 'Para obtener más información',
		'd2u_news_fair_dates' => 'Calendario de ferias',
		'd2u_news_fairs_all' => 'Fechas de todas las ferias',
		'd2u_news_news' => 'Noticias',
	];

	/**
	 * @var array<string, string> Array with italian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_italian = [
		'd2u_news_details' => 'Per ulteriori informazioni',
		'd2u_news_fair_dates' => 'Calendario fiere',
		'd2u_news_fairs_all' => 'Tutti Calendario fiere',
		'd2u_news_news' => 'Notizie',
	];

	/**
	 * @var array<string, string> Array with polish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_polish = [
		'd2u_news_details' => 'Aby uzyskać więcej informacji',
		'd2u_news_fair_dates' => 'Daty godziwe',
		'd2u_news_fairs_all' => 'Wszystko Daty godziwe',
		'd2u_news_news' => 'Aktualności',
	];
	
	/**
	 * @var array<string, string> Array with dutch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_dutch = [
		'd2u_news_details' => 'Voor meer informatie',
		'd2u_news_fair_dates' => 'Beursdata',
		'd2u_news_fairs_all' => 'Alle Beursdata',
		'd2u_news_news' => 'Nieuws',
	];

	/**
	 * @var array<string, string> Array with czech replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_czech = [
		'd2u_news_details' => 'Pro více informací',
		'd2u_news_fair_dates' => 'Reálné data',
		'd2u_news_fairs_all' => 'Vše Reálné data',
		'd2u_news_news' => 'Novinky',
	];

	/**
	 * @var array<string, string> Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_russian = [
		'd2u_news_details' => 'Для получения дополнительной информации',
		'd2u_news_fairs_all' => 'даты всех выставок',
		'd2u_news_fair_dates' => 'даты выставок',
		'd2u_news_news' => 'Новости',
	];

	/**
	 * @var array<string, string> Array with portuguese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_portuguese = [
		'd2u_news_details' => 'Para mais informações',
		'd2u_news_fair_dates' => 'Datas Feira',
		'd2u_news_fairs_all' => 'Todos Datas Feira',
		'd2u_news_news' => 'Notícias',
	];

	/**
	 * @var array<string, string> Array with chinese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_chinese = [
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
	public function install():void {
		foreach($this->replacements_english as $key => $value) {
			foreach (rex_clang::getAllIds() as $clang_id) {
				$lang_replacement = rex_config::get('d2u_news', 'lang_replacement_'. $clang_id, '');

				// Load values for input
				if($lang_replacement === 'chinese' && isset($this->replacements_chinese) && isset($this->replacements_chinese[$key])) {
					$value = $this->replacements_chinese[$key];
				}
				else if($lang_replacement === 'czech' && isset($this->replacements_czech) && isset($this->replacements_czech[$key])) {
					$value = $this->replacements_czech[$key];
				}
				else if($lang_replacement === 'dutch' && isset($this->replacements_dutch) && isset($this->replacements_dutch[$key])) {
					$value = $this->replacements_dutch[$key];
				}
				else if($lang_replacement === 'french' && isset($this->replacements_french) && isset($this->replacements_french[$key])) {
					$value = $this->replacements_french[$key];
				}
				else if($lang_replacement === 'german' && isset($this->replacements_german) && isset($this->replacements_german[$key])) {
					$value = $this->replacements_german[$key];
				}
				else if($lang_replacement === 'italian' && isset($this->replacements_italian) && isset($this->replacements_italian[$key])) {
					$value = $this->replacements_italian[$key];
				}
				else if($lang_replacement === 'polish' && isset($this->replacements_polish) && isset($this->replacements_polish[$key])) {
					$value = $this->replacements_polish[$key];
				}
				else if($lang_replacement === 'portuguese' && isset($this->replacements_portuguese) && isset($this->replacements_portuguese[$key])) {
					$value = $this->replacements_portuguese[$key];
				}
				else if($lang_replacement === 'russian' && isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
					$value = $this->replacements_russian[$key];
				}
				else if($lang_replacement === 'spanish' && isset($this->replacements_spanish) && isset($this->replacements_spanish[$key])) {
					$value = $this->replacements_spanish[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				$overwrite = rex_config::get('d2u_news', 'lang_wildcard_overwrite', false) === "true" ? true : false;
				parent::saveValue($key, $value, $clang_id, $overwrite);
			}
		}
	}
}