<?php 

namespace Model;

use Model\Model;

/**
 * Class BasketModel
 * This model contains methods for basket
 */
class BasketModel extends Model {

	public function __construct() {
		parent::__construct();

		$this->table = 'basket';
	}

    public function get(String $sessionId) {
        $sql = 'SELECT * FROM `' . $this->table . '`
            WHERE session_id = \'' . $sessionId . '\'
            ORDER BY id';

        return $this->list($sql);
    }

    public function getCar(String $sessionId, $id) {
        $sql = 'SELECT * FROM `' . $this->table . '`
            WHERE session_id = \'' . $sessionId . '\' AND car_id = ' . $id;

        return $this->getRow($sql);
    }

    public function clear(String $sessionId) {
        $sql = 'DELETE FROM `' . $this->table . '`
            WHERE session_id = \'' . $sessionId . '\'';

        return $this->db->execSql($sql);
    }

    public function getSelectedDates(String $sessionId, $carId, $thisId = 0) {
        $sql = 'SELECT start_date, end_date 
            FROM `' . $this->table . '` 
            WHERE session_id = \'' . $sessionId . '\' AND car_id = ' . $carId 
                . ($thisId > 0 ? ' AND id != ' . $thisId : '') . ' 
            ORDER BY start_date';

        $dates = $this->list($sql);

        if($dates) {
            return $dates;
        }

        return [];
    }
}
?>