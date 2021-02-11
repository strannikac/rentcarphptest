<?php 

namespace App;

use Helper\System;
use Model\CarModel;
use Model\BasketModel;
use Model\OrderModel;

/**
 * Class CarController
 * This controller contains methods for car 
 */
class CarController extends Controller {

    private $tplPath = _ROOT_TPL_ . 'car/';
    private $imgPath = _ROOT_IMAGES_ . 'cars/';
    private $imgUrl = _URL_IMAGES_ . 'cars/';

    private $sortBy = ['price', 'year'];

    private $carModel;
    private $orderModel;

    public function __construct(array $request) {
        parent::__construct($request);

        $this->carModel = new CarModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Method main (default) - show table of cars with filters
     * @param void
     */
    public function main() {
        $this->content['html'] = '';
        $this->content['js'][] = 'js/car.js';

        $this->template->set( $this->tplPath . 'main.html');

        $where = $this->whereLocation();

        $items = $this->carModel->get($this->language['id'], $where);

        $cars = '';
        $categories = '';
        $producers = '';
        $years = '';
        $colors = '';
        $models = '';

        $hidden = '';

        if($items) {
            $cars = $this->htmlCars($items);

            $this->template->set( $this->tplPath . 'filter.html', false);

            $rows = $this->carModel->getCategories($this->language['id']);
            $count = count($rows);

            for($i = 0; $i < $count; $i++) {
                $tplVars = [
                    'TITLE' => $rows[$i]['title'], 
                    'NAME' => 'category_id[]', 
                    'ID' => $rows[$i]['id']
                ];

                $this->template->setVars( $tplVars );
                $categories .= $this->template->parse(false);
            }

            $rows = $this->carModel->getProducers($this->language['id']);
            $count = count($rows);

            for($i = 0; $i < $count; $i++) {
                $tplVars = [
                    'TITLE' => $rows[$i]['title'], 
                    'NAME' => 'producer_id[]', 
                    'ID' => $rows[$i]['id']
                ];

                $this->template->setVars( $tplVars );
                $producers .= $this->template->parse(false);
            }

            $rows = $this->carModel->getYears();
            $count = count($rows);

            for($i = 0; $i < $count; $i++) {
                $tplVars = [
                    'TITLE' => $rows[$i]['year'], 
                    'NAME' => 'year[]', 
                    'ID' => $rows[$i]['year']
                ];

                $this->template->setVars( $tplVars );
                $years .= $this->template->parse(false);
            }

            $rows = $this->carModel->getColors($this->language['id']);
            $count = count($rows);

            for($i = 0; $i < $count; $i++) {
                $tplVars = [
                    'TITLE' => $rows[$i]['title'], 
                    'NAME' => 'color_id[]', 
                    'ID' => $rows[$i]['id']
                ];

                $this->template->setVars( $tplVars );
                $colors .= $this->template->parse(false);
            }
        }

        if($cars == '') {
            $cars = $this->locale['SUCCESS_9004'];
            $hidden = ' hidden';
        }

        $tplVars = [
            'STR_CATEGORY' => $this->locale['STR_CATEGORY'], 
            'STR_PRODUCER' => $this->locale['STR_PRODUCER'], 
            'STR_YEAR' => $this->locale['STR_YEAR'], 
            'STR_COLOR' => $this->locale['STR_COLOR'], 
            'HDR_CARS' => $this->locale['HDR_CARS'], 
            'TXT_CARS' => $this->locale['TXT_CARS'], 
            'STR_SORTBY' => $this->locale['STR_SORTBY'], 
            'STR_PRICE' => $this->locale['STR_PRICE'], 
            'CATEGORIES' => $categories, 
            'PRODUCERS' => $producers, 
            'YEARS' => $years, 
            'COLORS' => $colors, 
            'CARS' => $cars, 
            'hidden' => $hidden, 
            'currencies' => $this->htmlCurrency()
        ];

        $this->template->setVars( $tplVars );

        $this->content['html'] = $this->template->parse();
        $this->response['status'] = $this->statusSuccess;
    }

    /**
     * Method search - find cars by params
     * @param void
     */
    public function search() {
        $where = $this->whereLocation();

        if(isset($this->params['post']) && is_array($this->params['post'])) {
            foreach($this->params['post'] as $key => $value) {
                $where[$key] = $value;
            }
        }

        if(!empty($this->params['get']['currency']) && $this->validator->isPositiveInteger($this->params['get']['currency'])) {
            $this->currency = $this->getCurrency($this->params['get']['currency']);
        }

        $sortBy = $this->sortBy[0];
        $isDesc = 0;
        if(!empty($this->params['get']['sortby'])) {
            $this->params['get']['sortby'] = System::cleanString($this->params['get']['sortby']);

            if(in_array($this->params['get']['sortby'], $this->sortBy)) {
                $sortBy = $this->params['get']['sortby'];
            }
        }

        if(isset($this->params['get']['desc']) && in_array($this->params['get']['desc'], $this->arrBool)) {
            $isDesc = $this->params['get']['desc'];
        }

        $items = $this->carModel->get($this->language['id'], $where, $sortBy, $isDesc);

        $cars = '';
        $this->template->set( $this->tplPath . 'item.html');

        if($items) {
            $cars = $this->htmlCars($items);
        }

        $hidden = 0;

        if($cars == '') {
            $cars = $this->locale['SUCCESS_9004'];
            $hidden = 1;
        }

        $this->response['status'] = $this->statusSuccess;
        $this->response['cars'] = $cars;
        $this->response['hidden'] = $hidden;
    }

    /**
     * Method inbasket - add car in basket
     * @param void
     */
    public function inbasket() {
        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $startTime = strtotime($this->params['post']['from']);
        $endTime = strtotime($this->params['post']['till']);

        if(empty($this->params['post']['from']) || !$startTime 
            || empty($this->params['post']['till']) || !$endTime
        ) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $item = $this->carModel->getOne($this->params['object'], $this->language['id']);

        if(!$item || empty($item['id'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        if(!empty($this->params['get']['currency']) && $this->validator->isPositiveInteger($this->params['get']['currency'])) {
            $this->currency = $this->getCurrency($this->params['get']['currency']);
        }

        $startDate = date($this->dayFormat, $startTime);
        $endDate = date($this->dayFormat, $endTime);
        $item['in_euro'] = $this->currency['in_euro'];

        if($item['min_days'] > 1) {
            $tmpDate = date($this->dayFormat, strtotime($this->params['post']['from'] . '+' . ($item['min_days'] - 1) . ' days'));

            if($endDate < $tmpDate) {
                $this->response['error'][] = $this->locale['ERR_2018'];
                return false;
            }
        }

        if($item['max_days'] > 0) {
            $tmpDate = date($this->dayFormat, strtotime($this->params['post']['from'] . '+' . ($item['max_days'] - 1) . ' days'));

            if($endDate > $tmpDate) {
                $this->response['error'][] = $this->locale['ERR_2018'];
                return false;
            }
        }

        $disabledDates = $this->orderModel->getRentedDates($item['id']);
        if(!$this->orderModel->isFreePeriod($disabledDates, $startDate, $endDate)) {
            $this->response['error'][] = $this->locale['ERR_1013'];
            return false;
        }

        $model = new BasketModel();

        $disabledDates = $model->getSelectedDates($this->sessionToken, $item['id']);
        if(!$this->orderModel->isFreePeriod($disabledDates, $startDate, $endDate)) {
            $this->response['error'][] = $this->locale['ERR_1013'];
            return false;
        }

        $item['start_date'] = $this->params['post']['from'];
        $item['end_date'] = $this->params['post']['till'];

        $prices = $this->carModel->calculatePrice($item);

        $arr = [
            'session_id' => $this->sessionToken, 
            'car_id' => $item['id'], 
            'start_date' => date('Y-m-d', $startTime), 
            'end_date' => date('Y-m-d', $endTime), 
            'price' => $prices['total'], 
            'one_time_fee' => $prices['one_time_fee'], 
            'holidays' => $prices['holidays'], 
            'holidays_fee' => $prices['holidays_fee'], 
            'discount_days_fee' => $prices['discount_days_fee'], 
            'final_price' => $prices['final']
        ];

        $newId = $model->insert($arr);

        if ($newId) {
            $items = $model->get($this->sessionToken);
            $count = 0;
            if($items) {
                $count = count($items);
            }

            $this->response['status'] = $this->statusSuccess;
            $this->response['msg'] = $this->locale['SUCCESS_9000'];
            $this->response['count'] = $count;
        } else {
            $err[] = $this->locale['ERR_1008'];
        }
    }

    private function whereLocation() {
        $where = [
            'country_id' => [NULL, $this->location['country']['id']], 
            'region_id' => [NULL, $this->location['region']['id']]
        ];

        $regions = $this->locationModel->getParents($this->location['region']['parent_id']);

        if(count($regions) > 0) {
            $where['region_id'] = array_merge($where['region_id'], $regions);
        }

        return $where;
    }

    private function htmlCurrency() {
        $html = '';

        $count = count($this->currencies);
        for($i = 0; $i < $count; $i++) {
            $html .= '<option value="' . $this->currencies[$i]['id'] . '"' . ($this->currency['id'] == $this->currencies[$i]['id'] ? ' selected="selected"' : '') . '>' . strtoupper($this->currencies[$i]['iso']) . ', ' . $this->currencies[$i]['sign'] . '</option>';
        }

        return $html;
    }

    private function htmlCars($items = []) {
        $html = '';
        $this->template->set( $this->tplPath . 'item.html', false);

        $count = count($items);
        for($i = 0; $i < $count; $i++) {
            $disabledDates = $this->orderModel->getRentedDates($items[$i]['id']);
            $rentPeriod = $this->orderModel->getRentDates($disabledDates, $items[$i]['min_days']);

            $items[$i]['start_date'] = $rentPeriod['start_date'];
            $items[$i]['end_date'] = $rentPeriod['end_date'];

            $items[$i]['prices'] = $this->carModel->calculatePrice($items[$i]);

            $img = $this->nophoto;
            $photo = $items[$i]['id'] . '.jpg';
            if(file_exists($this->imgPath . $photo)) {
                $img = $this->imgUrl . $photo;
            }

            $tplVars = [
                'IMAGE' => $img, 
                'TITLE' => $items[$i]['title'], 
                'CATEGORY' => $items[$i]['category'], 
                'PRODUCER' => $items[$i]['producer'], 
                'MODEL' => $items[$i]['model'], 
                'COLOR' => $items[$i]['color'], 
                'YEAR' => $items[$i]['year'], 
                'ID' => $items[$i]['id'], 
                'MIN_DAYS' => $items[$i]['min_days'], 
                'MAX_DAYS' => $items[$i]['max_days'], 
                'DAY_PRICE' => $this->getFormatMoney($items[$i]['price'] / $this->currency['in_euro']), 
                'HOLIDAY_PERCENT' => $items[$i]['holiday_coef_percent'], 
                'ONE_TIME_FEE' => $this->getFormatMoney($items[$i]['one_time_fee'] / $this->currency['in_euro']), 
                'DISCOUNT_DAYS' => $items[$i]['discount_days'], 
                'DISCOUNT_ADD_DAYS' => $items[$i]['discount_add_days'], 
                'PRICE' => $this->getFormatMoney($items[$i]['prices']['final'] / $this->currency['in_euro']), 
                'CURRENCY_SIGN' => $this->currency['sign'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'STR_RENT' => $this->locale['STR_RENT'], 
                'DATE_FROM' => date($this->dayFormat, strtotime($items[$i]['start_date'])), 
                'DATE_TILL' => date($this->dayFormat, strtotime($items[$i]['end_date'])), 
                'RENTED_DATES' => json_encode($disabledDates)
            ];

            $this->template->setVars( $tplVars );
            $html .= $this->template->parse(false);
        }

        return $html;
    }
}

?>