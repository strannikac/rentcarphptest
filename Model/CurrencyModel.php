<?php 

namespace Model;

use Model\Model;

/**
 * Class LocaleModel
 * This model contains methods for locales (translations)
 */
class CurrencyModel extends Model {
    private $tableLangs = 'currencies_langs';

	public function __construct() {
		parent::__construct();

		$this->table = 'currencies';
	}

    public function get(int $lang) {
        $sql = 'SELECT c.id, c.iso, c.sign, c.pattern, c.in_euro, c.is_def, cl.title 
            FROM `' . $this->table . '` c 
            LEFT JOIN `' . $this->tableLangs . '` cl ON (cl.currency_id = c.id AND cl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 
            ORDER BY c.pos';

        return $this->list($sql);
    }
}
?>