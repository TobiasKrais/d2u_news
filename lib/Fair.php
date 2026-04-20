<?php
/**
 * Redaxo D2U News Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace TobiasKrais\D2UNews;

use rex;
use rex_sql;

/**
 * Fair.
 */
class Fair
{
    /** @var int Database ID */
    public int $fair_id = 0;

    /** @var string name */
    public string $name = '';

    /** @var string city */
    public string $city = '';

    /** @var string 3 digit country code */
    public string $country_code = '';

    /** @var string start date, format YYYY-MM-DD */
    public string $date_start = '';

    /** @var string end date, format YYYY-MM-DD */
    public string $date_end = '';

    /** @var string picture */
    public string $picture = '';

    /**
     * Constructor. Reads a fair stored in database.
     * @param int $fair_id fair ID
     */
    public function __construct($fair_id)
    {
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_news_fairs '
                .'WHERE fair_id = '. $fair_id;
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            $this->fair_id = (int) $result->getValue('fair_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $this->city = stripslashes((string) $result->getValue('city'));
            $this->country_code = (string) $result->getValue('country_code');
            $this->date_start = (string) $result->getValue('date_start');
            $this->date_end = (string) $result->getValue('date_end');
            $this->picture = (string) $result->getValue('picture');
        }
    }

    /**
     * Deletes the object.
     */
    public function delete(): void
    {
        $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_news_fairs '
            .'WHERE fair_id = '. $this->fair_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
    }

    /**
     * Get all fairs.
     * @param bool $current_only only fairs in future, not in past
     * @return Fair[] array with Fair objects
     */
    public static function getAll($current_only = true)
    {
        $query = 'SELECT fair_id FROM '. rex::getTablePrefix() .'d2u_news_fairs ';
        if ($current_only) {
            $query .= "WHERE date_end > '". date('Y-m-d') ."'";
        }
        $query .= ' ORDER BY date_start, date_end';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $fairs = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $fairs[] = new self($result->getValue('fair_id'));
            $result->next();
        }

        return $fairs;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if error occurs
     */
    public function save()
    {
        $error = false;

        $query = rex::getTablePrefix() .'d2u_news_fairs SET '
                ."name = '". addslashes($this->name) ."', "
                ."city = '". addslashes($this->city) ."', "
                ."country_code = '". $this->country_code ."', "
                ."date_start = '". $this->date_start ."', "
                ."date_end = '". $this->date_end ."', "
                ."picture = '". $this->picture ."' ";

        if (0 === $this->fair_id) {
            $query = 'INSERT INTO '. $query;
        } else {
            $query = 'UPDATE '. $query .' WHERE fair_id = '. $this->fair_id;
        }

        $result = rex_sql::factory();
        $result->setQuery($query);
        if (0 === $this->fair_id) {
            $this->fair_id = (int) $result->getLastId();
            $error = $result->hasError();
        }

        return $error;
    }
}