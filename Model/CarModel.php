<?php 

namespace Model;

use Model\Model;
use Helper\System;
use Helper\Validator;

/**
 * Class CarModel
 * This model contains methods for cars
 */
class CarModel extends Model {
    private $tableLangs = 'cars_langs';
    private $tableCategories = 'cars_categories';
    private $tableCategoriesLangs = 'cars_categories_langs';
    private $tableProducers = 'cars_producers';
    private $tableProducersLangs = 'cars_producers_langs';
    private $tableColors = 'cars_colors';
    private $tableColorsLangs = 'cars_colors_langs';
    private $tableStatuses = 'cars_statuses';
    private $tableStatusesLangs = 'cars_statuses_langs';

	public function __construct() {
		parent::__construct();
		$this->table = 'cars';
	}

    public function get(int $lang, array $params = [], $sortby = 'price', $isDesc = 0) {
        $where = [];
        $whereStr = '';
        $count = count($params);

        $sortby = $sortby == 'price' ? 'cat.day_price' : 'c.' . $sortby;

        if($count > 0) {
            foreach($params as $field => $value) {
                $field = System::cleanString($field);

                if(!empty($field)) {
                    if(is_array($value)) {
                        $in = [];
                        $isNull = false;
                        foreach($value as $v) {
                            $v = addslashes(strip_tags($v));
                            if(!empty($v)) {
                                $in[] = $v;
                            } elseif($v == NULL) {
                                $isNull = true;
                            }
                        }
                        if(count($in) > 0) {
                            $where[] = '(' . ($isNull ? 'c.`' . $field . '` IS NULL OR ' : '') . 'c.`' . $field . '` IN (' . implode(',', $in) . '))';
                        }
                    } else {
                        $value = addslashes(strip_tags($value));
                        if(!empty($value)) {
                            $where[] = 'c.`' . $field . '` = \'' . $value . '\'';
                        } elseif($value == NULL) {
                            $where[] = 'c.`' . $field . '` IS NULL';
                        }
                    }
                }
            }

            $whereStr = implode(' AND ', $where);
            if($whereStr != '') {
                $whereStr = ' AND ' . $whereStr;
            }
        }

        $sql = 'SELECT c.id, c.sku, c.model, c.year, c.status_id, cl.title, 
            cat.day_price AS `price`, cat.one_time_fee, 
            cat.renewal_fee, cat.holiday_coef_percent, cat.max_days, cat.min_days, 
            cat.discount_days, cat.discount_add_days, cat.termination_penalty_percent, 
            catl.title AS `category`, pl.title AS `producer`, 
            colorl.title AS `color`, sl.title AS `status`, sl.descr AS status_descr 
            FROM `' . $this->table . '` c 
            LEFT JOIN `' . $this->tableLangs . '` cl ON (cl.car_id = c.id AND cl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableCategories . '` cat ON (cat.id = c.category_id) 
            LEFT JOIN `' . $this->tableCategoriesLangs . '` catl ON (catl.category_id = c.category_id AND catl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableProducersLangs . '` pl ON (pl.producer_id = c.producer_id AND pl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableColorsLangs . '` colorl ON (colorl.color_id = c.color_id AND colorl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableStatusesLangs . '` sl ON (sl.status_id = c.status_id AND sl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 ' . $whereStr . '
            ORDER BY ' . $sortby . ($isDesc == 1 ? ' DESC' : '');

        return $this->list($sql);
    }

    public function getOne($id, int $lang) {
        $sql = 'SELECT c.id, c.sku, c.model, c.year, c.status_id, cl.title, 
            cat.day_price AS `price`, cat.one_time_fee, 
            cat.renewal_fee, cat.holiday_coef_percent, cat.max_days, cat.min_days, 
            cat.discount_days, cat.discount_add_days, cat.termination_penalty_percent, 
            catl.title AS `category`, pl.title AS `producer`, 
            colorl.title AS `color`, sl.title AS `status`, sl.descr AS status_descr 
            FROM `' . $this->table . '` c 
            LEFT JOIN `' . $this->tableLangs . '` cl ON (cl.car_id = c.id AND cl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableCategories . '` cat ON (cat.id = c.category_id) 
            LEFT JOIN `' . $this->tableCategoriesLangs . '` catl ON (catl.category_id = c.category_id AND catl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableProducersLangs . '` pl ON (pl.producer_id = c.producer_id AND pl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableColorsLangs . '` colorl ON (colorl.color_id = c.color_id AND colorl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableStatusesLangs . '` sl ON (sl.status_id = c.status_id AND sl.language_id = ' . $lang . ') 
            WHERE c.id = ' . $id . ' AND c.status_id = 1';

        return $this->getRow($sql);
    }

    public function getCategories(int $lang, array $cars = []) {
        $where = $this->getWhere($cars);

        $sql = 'SELECT cat.id, catl.title 
            FROM `' . $this->table . '` c 
            LEFT JOIN `' . $this->tableCategories . '` cat ON (cat.id = c.category_id) 
            LEFT JOIN `' . $this->tableCategoriesLangs . '` catl ON (catl.category_id = c.category_id AND catl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 ' . $where . ' 
            GROUP BY cat.id, catl.title 
            ORDER BY cat.pos';

        return $this->list($sql);
    }

    public function getProducers(int $lang, array $cars = []) {
        $where = $this->getWhere($cars);

        $sql = 'SELECT p.id, pl.title 
            FROM `' . $this->table . '` c 
            LEFT JOIN `' . $this->tableProducers . '` p ON (p.id = c.producer_id) 
            LEFT JOIN `' . $this->tableProducersLangs . '` pl ON (pl.producer_id = c.producer_id AND pl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 ' . $where . ' 
            GROUP BY p.id, pl.title 
            ORDER BY p.pos';

        return $this->list($sql);
    }

    public function getColors(int $lang, array $cars = []) {
        $where = $this->getWhere($cars);

        $sql = 'SELECT cr.id, crl.title 
            FROM `' . $this->table . '` c 
            LEFT JOIN `' . $this->tableColors . '` cr ON (cr.id = c.color_id) 
            LEFT JOIN `' . $this->tableColorsLangs . '` crl ON (crl.color_id = c.color_id AND crl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 ' . $where . ' 
            GROUP BY cr.id, crl.title 
            ORDER BY cr.pos';

        return $this->list($sql);
    }

    public function getYears(array $cars = []) {
        $where = $this->getWhere($cars);

        $sql = 'SELECT c.year 
            FROM `' . $this->table . '` c 
            WHERE c.status_id = 1 ' . $where . ' 
            GROUP BY c.year 
            ORDER BY c.year';

        return $this->list($sql);
    }

    public function calculatePrice(array $item) {
        $price = [
            'total' => 0, 
            'one_time_fee' => 0, 
            'holidays' => '', 
            'holidays_fee' => 0, 
            'discount_days' => 0, 
            'discount_days_fee' => 0, 
            'final' => 0
        ];

        $startDate = new \DateTime($item['start_date']);
        $endDate = new \DateTime($item['end_date']);

        $interval = $startDate->diff($endDate);
        $days = $interval->format('%a') + 1;

        $price['total'] = round($item['price'] * $days, 2); 
        $price['final'] = $price['total'];

        if($item['holiday_coef_percent'] > 0) {
            $holidays = System::getHolidays($days, $startDate);
            $count = count($holidays);

            if($count > 0) {
                $price['holidays_fee'] = round($item['price'] * $count * $item['holiday_coef_percent'] / 100, 2);
                $price['holidays'] = implode(',', $holidays);
                $price['final'] += $price['holidays_fee'];
            }
        }

        $price['one_time_fee'] = round($item['one_time_fee'], 2);
        $price['final'] += $price['one_time_fee'];

        if($item['discount_days'] > 0 && $item['discount_add_days'] > 0 && $days > $item['discount_days']) {
            $price['discount_days'] = $item['discount_add_days'];
            $price['discount_days_fee'] = round($item['discount_add_days'] * $item['price'], 2);
            $price['final'] -= $price['discount_days_fee'];
        }

        $price['final'] = round($price['final'], 2);

        return $price;
    }

    public function calculateRenewPrice(array $item) {
        $price = [
            'total' => 0, 
            'renewal_fee' => 0, 
            'holidays' => '', 
            'holidays_fee' => 0, 
            'final' => 0
        ];

        $startDate = new \DateTime($item['start_date']);
        $endDate = new \DateTime($item['end_date']);

        $interval = $startDate->diff($endDate);
        $days = $interval->format('%a') + 1;

        $price['total'] = round($item['price'] * $days, 2); 
        $price['final'] = $price['total'];

        if($item['holiday_coef_percent'] > 0) {
            $holidays = System::getHolidays($days, $startDate);
            $count = count($holidays);

            if($count > 0) {
                $price['holidays_fee'] = round($item['price'] * $count * $item['holiday_coef_percent'] / 100, 2);
                $price['holidays'] = implode(',', $holidays);
                $price['final'] += $price['holidays_fee'];
            }
        }

        $price['renewal_fee'] = round($item['renewal_fee'], 2);
        $price['final'] += $price['renewal_fee'];

        $price['final'] = round($price['final'], 2);

        return $price;
    }

    public function calculateTerminationPrice(array $item) {
        $price = [
            'total' => 0, 
            'one_time_fee' => 0, 
            'renewal_fee' => 0, 
            'holidays' => '', 
            'holidays_fee' => 0, 
            'discount_days' => 0, 
            'discount_days_fee' => 0, 
            'final' => 0
        ];

        $startDate = new \DateTime($item['start_date']);
        $endDate = new \DateTime($item['end_date']);

        $interval = $startDate->diff($endDate);
        $days = $interval->format('%a') + 1;

        $price['total'] = round($item['price'] * $days, 2); 
        $price['final'] = $price['total'];

        if($item['holiday_coef_percent'] > 0) {
            $holidays = System::getHolidays($days, $startDate);
            $count = count($holidays);

            if($count > 0) {
                $price['holidays_fee'] = round($item['price'] * $count * $item['holiday_coef_percent'] / 100, 2);
                $price['holidays'] = implode(',', $holidays);
                $price['final'] += $price['holidays_fee'];
            }
        }

        if(isset($item['is_renew'])) {
            $price['final'] += $item['renewal_fee']; 
            $price['renewal_fee'] = $item['renewal_fee'];
        } else {
            $price['final'] += $item['one_time_fee'];
            $price['one_time_fee'] = $item['one_time_fee'];

            if($item['discount_days'] > 0 && $item['discount_add_days'] > 0 && $days > $item['discount_days']) {
                $price['discount_days'] = $item['discount_add_days'];
                $price['discount_days_fee'] = $item['discount_add_days'] * $item['price'] * (-1);
                $price['final'] += $price['discount_days_fee'];
            }
        }

        $price['final'] = round($price['final'], 2);

        return $price;
    }

    private function getWhere(array $cars = []) {
        $where = [];
        $whereStr = '';
        $count = count($cars);

        $validator = new Validator();

        if($count > 0) {
            for($i = 0; $i < $count; $i++) {
                if( $validator->isPositiveInteger($cars[$i]) ) {
                    $where[] = 'c.id = ' . $cars[$i];
                }
            }

            $whereStr = implode(' OR ', $where);
            if($whereStr != '') {
                $whereStr = ' AND (' . $whereStr . ')';
            }
        }

        return $whereStr;
    }
}
?>