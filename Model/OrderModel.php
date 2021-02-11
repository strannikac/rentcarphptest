<?php 

namespace Model;

use Model\Model;

/**
 * Class OrderModel
 * This model contains methods for orders
 */
class OrderModel extends Model {

    private $tableCars = 'orders_cars';
    private $tableStatuses = 'orders_statuses';
    private $tableStatusesLangs = 'orders_statuses_langs';

	public function __construct() {
		parent::__construct();

		$this->table = 'orders';
	}

    public function get($customerId, int $lang) {
        $sql = 'SELECT o.*, sl.title AS `status`, sl.descr AS status_descr 
            FROM `' . $this->table . '` o 
            LEFT JOIN `' . $this->tableStatusesLangs . '` sl ON (sl.status_id = o.status_id AND sl.language_id = ' . $lang . ') 
            WHERE o.customer_id = \'' . $customerId . '\'
            ORDER BY o.id DESC';

        return $this->list($sql);
    }

    public function getOne($id, int $lang) {
        $sql = 'SELECT o.*, sl.title AS `status`, sl.descr AS status_descr 
            FROM `' . $this->table . '` o 
            LEFT JOIN `' . $this->tableStatusesLangs . '` sl ON (sl.status_id = o.status_id AND sl.language_id = ' . $lang . ') 
            WHERE o.id = ' . $id;

        return $this->getRow($sql);
    }

    public function addCars($cars, $orderId) {
        $count = count($cars);

        for($i = 0; $i < $count; $i++) {
            $cars[$i]['order_id'] = $orderId;
            $this->insert($cars[$i], $this->tableCars);
        }
    }

    public function updateCar($arr, $id) {
        return $this->update($arr, $id, $this->tableCars);
    }

    public function getCars($orderId) {
        $sql = 'SELECT * FROM `' . $this->tableCars . '`
            WHERE order_id = ' . $orderId . ' 
            ORDER BY id';

        return $this->list($sql);
    }

    public function check() {
        $today = date('Y-m-d');

        //close orders
        $sql = 'UPDATE `' . $this->table . '` 
            SET status_id = 5 
            WHERE status_id NOT IN (3, 5) AND end_date < \'' . $today . '\'';
        $this->db->execSql($sql);

        //in process orders
        $sql = 'UPDATE `' . $this->table . '` 
            SET status_id = 2 
            WHERE status_id = 1 AND start_date <= \'' . $today . '\'';
        $this->db->execSql($sql);
    }

    public function getRentedDates($carId) {
        $sql = 'SELECT oc.start_date, oc.end_date, oc.termination_date 
            FROM `' . $this->tableCars . '` oc 
            LEFT JOIN `' . $this->table . '` o ON (o.id = oc.order_id) 
            WHERE oc.car_id = ' . $carId . ' AND o.status_id NOT IN (3, 5) 
            ORDER BY oc.start_date';

        $dates = $this->list($sql);

        if($dates) {
            $count = count($dates);
            $periods = [];
            for($i = 0; $i < $count; $i++) {
                $periods[$i]['start_date'] = $dates[$i]['start_date'];
                $periods[$i]['end_date'] = empty($dates[$i]['termination_date']) ? $dates[$i]['end_date'] : $dates[$i]['termination_date'];
            }
            return $periods;
        }

        return [];
    }

    public function getRentDates($disabledDates = [], $minDays = 1, $startDate = '') {
        $startDate = $startDate == '' ? date('Y-m-d') : date('Y-m-d', strtotime($startDate));

        $endDate = $startDate;
        if($minDays > 1) {
            $endDate = date('Y-m-d', strtotime($startDate. '+' . ($minDays - 1) . ' days'));
        }

        $count = count($disabledDates);
        for($i = 0; $i < $count; $i++) {
            $period = $disabledDates[$i];

            if(($startDate >= $period['start_date'] && $startDate <= $period['end_date'])
                || ($endDate >= $period['start_date'] && $endDate <= $period['end_date'])
                || ($startDate <= $disabledDates[$i]['start_date'] && $endDate >= $disabledDates[$i]['end_date'])
            ) {
                $startDate = date('Y-m-d', strtotime($period['end_date'] . '+1 days'));
            }

            $endDate = $startDate;
            if($minDays > 1) {
                $endDate = date('Y-m-d', strtotime($startDate. '+' . ($minDays - 1) . ' days'));
            }
        }

        return ['start_date' => $startDate, 'end_date' => $endDate];
    }

    public function isFreePeriod($disabledDates = [], $startDate, $endDate) {
        $count = count($disabledDates);

        if($count > 0) {
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));

            for($i = 0; $i < $count; $i++) {
                if(($startDate >= $disabledDates[$i]['start_date'] && $startDate <= $disabledDates[$i]['end_date'])
                    || ($endDate >= $disabledDates[$i]['start_date'] && $endDate <= $disabledDates[$i]['end_date'])
                    || ($startDate <= $disabledDates[$i]['start_date'] && $endDate >= $disabledDates[$i]['end_date'])
                ) {
                    return false;
                }
            }
        }

        return true;
    }
}
?>