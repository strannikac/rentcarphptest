<?php 

namespace Model;

use Model\Model;

/**
 * Class LanguageModel
 * This model contains methods for languages
 */
class LanguageModel extends Model {

	public function __construct() {
		parent::__construct();

		$this->table = 'languages';
	}

    public function get() {
        $sql = 'SELECT * FROM `' . $this->table . '`
            WHERE status_id = 1 
            ORDER BY pos';

        return $this->list($sql);
    }
}
?>