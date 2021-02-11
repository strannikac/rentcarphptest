<?php 

namespace Model;

use Model\Model;

/**
 * Class LocaleModel
 * This model contains methods for locales (translations)
 */
class LocaleModel extends Model {

	public function __construct() {
		parent::__construct();

		$this->table = 'locales';
	}

    public function get(int $lang) {
        $sql = 'SELECT id, `value` FROM `' . $this->table . '`
            WHERE language_id = ' . $lang;

        $items = $this->list($sql);

        if($items) {
            $arr = [];
            $count = count($items);
            
            for($i = 0; $i < $count; $i++) {
                $arr[$items[$i]['id']] = $items[$i]['value'];
            }

            return $arr;
        }
        

        return false;
    }
}
?>