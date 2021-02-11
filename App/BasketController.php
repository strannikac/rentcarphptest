<?php 

namespace App;

use Helper\System;
use Model\CarModel;
use Model\BasketModel;
use Model\OrderModel;

/**
 * Class BasketController
 * This controller contains methods for basket 
 */
class BasketController extends Controller {

    private $tplPath = _ROOT_TPL_ . 'basket/';

    private $carModel;
    private $basketModel;
    private $orderModel;
    private $checkoutLink = '';

    public function __construct(array $request) {
        parent::__construct($request);

        $this->carModel = new CarModel();
        $this->basketModel = new BasketModel();
        $this->orderModel = new OrderModel();

        $this->checkoutLink = _SERVER_URL_ . $this->language['iso'] . '/' . $this->location['region']['url'] . '/basket/checkout';
    }

    /**
     * Method main (default) - show table of cars in basket
     * @param void
     */
    public function main() {
        $this->content['html'] = '';
        $this->content['js'][] = 'js/basket.js';

        $this->template->set( $this->tplPath . 'main.html');

        $items = $this->basketModel->get($this->sessionToken);

        $cars = '';
        $total = 0;

        if($items) {
            $cars = $this->htmlCars($items);
            $total = $cars['total'];
            $cars = $cars['cars'];
        }

        $tplVars = [
            'STR_BASKET' => $this->locale['STR_BASKET'], 
            'TXT_BASKET' => $this->locale['TXT_BASKET'], 
            'STR_CAR' => $this->locale['STR_CAR'], 
            'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
            'STR_PRICE' => $this->locale['STR_PRICE'], 
            'STR_DETAILS' => $this->locale['STR_DETAILS'], 
            'STR_DELETE' => $this->locale['STR_DELETE'], 
            'STR_TOTAL' => $this->locale['STR_TOTAL'], 
            'STR_CHECKOUT' => $this->locale['STR_CHECKOUT'], 
            'LINK_CHECKOUT' => empty($this->customer['id']) ? '#' : $this->checkoutLink, 
            'CLASS_LOGIN' => empty($this->customer['id']) ? ' js-link-login' : ' js-link-checkout', 
            'TOTAL_PRICE' => $total, 
            'CURRENCY_SIGN' => $this->currency['sign'], 
            'ITEMS' => $cars, 
            'HIDDEN' => $cars == '' ? ' hidden' : ''
        ];

        $this->template->setVars( $tplVars );

        $this->content['html'] = $this->template->parse();
        $this->response['status'] = $this->statusSuccess;
    }

    /**
     * Method save - update basket (with changes)
     * @param void
     */
    public function save() {
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

        $item = $this->basketModel->getById($this->params['object']);
        
        if(!$item || empty($item['id'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $car = $this->carModel->getOne($item['car_id'], $this->language['id']);

        $startDate = date($this->dayFormat, $startTime);
        $endDate = date($this->dayFormat, $endTime);

        if($car['min_days'] > 1) {
            $tmpDate = date($this->dayFormat, strtotime($this->params['post']['from'] . '+' . ($car['min_days'] - 1) . ' days'));

            if($endDate < $tmpDate) {
                $this->response['error'][] = $this->locale['ERR_2018'];
                return false;
            }
        }

        if($car['max_days'] > 0) {
            $tmpDate = date($this->dayFormat, strtotime($this->params['post']['from'] . '+' . ($car['max_days'] - 1) . ' days'));

            if($endDate > $tmpDate) {
                $this->response['error'][] = $this->locale['ERR_2018'];
                return false;
            }
        }

        $disabledDates = $this->orderModel->getRentedDates($car['id']);
        if(!$this->orderModel->isFreePeriod($disabledDates, $startDate, $endDate)) {
            $this->response['error'][] = $this->locale['ERR_1013'];
            return false;
        }

        $disabledDates = $this->basketModel->getSelectedDates($this->sessionToken, $car['id'], $item['id']);
        if(!$this->orderModel->isFreePeriod($disabledDates, $startDate, $endDate)) {
            $this->response['error'][] = $this->locale['ERR_1013'];
            return false;
        }

        $car['start_date'] = $this->params['post']['from'];
        $car['end_date'] = $this->params['post']['till'];

        $prices = $this->carModel->calculatePrice($car);

        $arr = [
            'start_date' => date('Y-m-d', strtotime($car['start_date'])), 
            'end_date' => date('Y-m-d', strtotime($car['end_date'])), 
            'price' => $prices['total'], 
            'one_time_fee' => $prices['one_time_fee'], 
            'holidays' => $prices['holidays'], 
            'holidays_fee' => $prices['holidays_fee'], 
            'discount_days_fee' => $prices['discount_days_fee'], 
            'final_price' => $prices['final'] 
        ];

        if(!$this->basketModel->update($arr, $this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1009'];
            return false;
        }

        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        if($this->currency['id'] != $euro['id']) {
            $prices['final'] /= $this->currency['in_euro'];
            $prices['total'] /= $this->currency['in_euro'];
            $prices['one_time_fee'] /= $this->currency['in_euro'];
            $prices['holidays_fee'] /= $this->currency['in_euro'];
            $prices['discount_days_fee'] /= $this->currency['in_euro'];
            $car['price'] /= $this->currency['in_euro'];
        }

        $details = '';
        $details .= '<div>' . $this->locale['STR_BASE_PRICE'] . ': <br/>' .  $this->getFormatMoney($prices['total']) . $this->currency['sign'] . '</div>';
        $details .= '<div>' . $this->locale['STR_ONE_TIME_FEE'] . ': <br/>' .  $this->getFormatMoney($prices['one_time_fee']) . $this->currency['sign'] . '</div>';
        $details .= $prices['holidays_fee'] > 0 ? '<div>' . $this->locale['STR_WEEKENDS_HOLIDAYS'] . ': <br/>' .  $prices['holidays'] . '</div>' : '';
        $details .= $prices['holidays_fee'] > 0 ? '<div>' . $this->locale['STR_HOLIDAYS_FEE'] . ': <br/>' .  $this->getFormatMoney($prices['holidays_fee']) . $this->currency['sign'] . '</div>' : '';
        $details .= $prices['discount_days_fee'] > 0 ? '<div>' . $this->locale['STR_DISCOUNT_DAYS_FEE'] . ': <br/>' .  $this->getFormatMoney($prices['discount_days_fee']) . $this->currency['sign'] . '</div>' : '';
        
        $this->response['status'] = $this->statusSuccess;
        $this->response['id'] = $this->params['object'];
        $this->response['price'] = $prices['final'];
        $this->response['details'] = $details;
    }

    /**
     * Method delete - remove from basket
     * @param void
     */
    public function delete() {
        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $this->basketModel->delete($this->params['object']);

        $this->response['status'] = $this->statusSuccess;
        $this->response['id'] = $this->params['object'];
    }

    /**
     * Method checkout - create order (if customer is logined)
     * @param void
     */
    public function checkout() {
        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        $items = $this->basketModel->get($this->sessionToken);

        if(!$items || count($items) < 1) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $count = count($items);
        $cars = [];
        $carsIds = [];

        $now = time();

        $orderPrices = [
            'price' => 0, 
            'one_time_fee' => 0, 
            'holidays_fee' => 0, 
            'discount_days_fee' => 0, 
            'final_price' => 0
        ];

        $orderStartDate = '';
        $orderEndDate = '';

        for($i = 0; $i < $count; $i++) {
            $car = $this->carModel->getOne($items[$i]['car_id'], $this->language['id']);

            if(!$car || empty($car['id'])) {
                $this->response['error'][] = $this->locale['ERR_1005'];
                return false;
            }

            $disabledDates = $this->orderModel->getRentedDates($car['id']);
            if(!$this->orderModel->isFreePeriod($disabledDates, $items[$i]['start_date'], $items[$i]['end_date'])) {
                $this->response['error'][] = $this->locale['ERR_1013'];
                return false;
            }

            $disabledDates = $this->basketModel->getSelectedDates($this->sessionToken, $car['id'], $items[$i]['id']);
            if(!$this->orderModel->isFreePeriod($disabledDates, $items[$i]['start_date'], $items[$i]['end_date'])) {
                $this->response['error'][] = $this->locale['ERR_1013'];
                return false;
            }

            if($i == 0 || $orderStartDate > $items[$i]['start_date']) {
                $orderStartDate = $items[$i]['start_date'];
            }

            if($i == 0 || $orderEndDate < $items[$i]['end_date']) {
                $orderEndDate = $items[$i]['end_date'];
            }

            $carsIds[] = $items[$i]['car_id'];

            $cars[] = [
                'car_id' => $items[$i]['car_id'], 
                'start_date' => $items[$i]['start_date'], 
                'end_date' => $items[$i]['end_date'], 
                'price' => $items[$i]['price'], 
                'one_time_fee' => $items[$i]['one_time_fee'], 
                'holidays' => $items[$i]['holidays'], 
                'holidays_fee' => $items[$i]['holidays_fee'], 
                'discount_days_fee' => $items[$i]['discount_days_fee'], 
                'final_price' => $items[$i]['final_price']
            ];

            $orderPrices['price'] += $items[$i]['price'];
            $orderPrices['one_time_fee'] += $items[$i]['one_time_fee'];
            $orderPrices['holidays_fee'] += $items[$i]['holidays_fee'];
            $orderPrices['discount_days_fee'] += $items[$i]['discount_days_fee'];
            $orderPrices['final_price'] += $items[$i]['final_price'];
        }

        $orderNumber = $this->customer['orders'] + 1;
        $invoice = date('Ymd') . '-' . $this->customer['id'] . '-' . $orderNumber;

        $arr = [
            'customer_id' => $this->customer['id'], 
            'invoice' => $invoice, 
            'start_date' => $orderStartDate, 
            'end_date' => $orderEndDate, 
            'price' => $orderPrices['price'], 
            'one_time_fee' => $orderPrices['one_time_fee'], 
            'holidays_fee' => $orderPrices['holidays_fee'], 
            'discount_days_fee' => $orderPrices['discount_days_fee'], 
            'final_price' => $orderPrices['final_price'], 
            'creation_time' => $now, 
            'update_time' => $now
        ];

        $orderId = $this->orderModel->insert($arr);

        if ($orderId) {
            $this->orderModel->addCars($cars, $orderId);
            $this->basketModel->clear($this->sessionToken);

            $arr = ['orders' => $orderNumber, 'update_time' => $now];
            $this->customerModel->update($arr, $this->customer['id']);

            $this->response['status'] = $this->statusSuccess;
            $this->response['msg'] = $this->locale['SUCCESS_9000'];
            $this->response['link'] = $this->links['orders'];
        } else {
            $this->response['error'][] = $this->locale['ERR_1008'];
        }
    }

    private function htmlCars($items = []) {
        $html = '';
        $total = 0;
        $this->template->set( $this->tplPath . 'item.html', false);
        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        $count = count($items);
        for($i = 0; $i < $count; $i++) {
            $car = $this->carModel->getOne($items[$i]['car_id'], $this->language['id']);

            if($this->currency['id'] != $euro['id']) {
                $items[$i]['final_price'] /= $this->currency['in_euro'];
                $items[$i]['price'] /= $this->currency['in_euro'];
                $items[$i]['one_time_fee'] /= $this->currency['in_euro'];
                $items[$i]['holidays_fee'] /= $this->currency['in_euro'];
                $items[$i]['discount_days_fee'] /= $this->currency['in_euro'];
                $car['price'] /= $this->currency['in_euro'];
            }

            $total += $items[$i]['final_price'];

            $holidays = [];
            $arr = explode(',', $items[$i]['holidays']);
            $len = count($arr);

            for($j = 0; $j < $len; $j++) {
                $date = trim($arr[$j]);

                if(!empty($date)) {
                    $holidays[] = $date;
                }
            }

            $disabledDates = $this->orderModel->getRentedDates($items[$i]['car_id']);

            $tplVars = [
                'TITLE' => $car['title'], 
                'CATEGORY' => $car['category'], 
                'ID' => $items[$i]['id'], 
                'CAR_ID' => $items[$i]['car_id'], 
                'MIN_DAYS' => $car['min_days'], 
                'MAX_DAYS' => $car['max_days'], 
                'DAY_PRICE' => $this->getFormatMoney($car['price']), 
                'HOLIDAY_PERCENT' => $car['holiday_coef_percent'], 
                'ONE_TIME_FEE' => $items[$i]['one_time_fee'], 
                'DISCOUNT_DAYS' => $car['discount_days'], 
                'DISCOUNT_ADD_DAYS' => $car['discount_add_days'], 
                'PRICE' => $this->getFormatMoney($items[$i]['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign'], 
                'STR_CAR' => $this->locale['STR_CAR'], 
                'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'STR_SAVE' => $this->locale['STR_SAVE'], 
                'DATE_FROM' => date($this->dayFormat, strtotime($items[$i]['start_date'])), 
                'DATE_TILL' => date($this->dayFormat, strtotime($items[$i]['end_date'])), 
                'STR_DETAILS' => $this->locale['STR_DETAILS'], 
                'STR_DELETE' => $this->locale['STR_DELETE'], 
                'BASE_PRICE_DETAILS' => '<div><span class="label">' . $this->locale['STR_BASE_PRICE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['price']) . $this->currency['sign'] . '</span></div>', 
                'ONE_TIME_FEE_DETAILS' => $items[$i]['one_time_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_ONE_TIME_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['one_time_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'HOLIDAYS_DETAILS' => $items[$i]['holidays_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_WEEKENDS_HOLIDAYS'] . ': </span><span class="value">' .  implode(', ', $holidays) . '</span></div>' : '', 
                'HOLIDAYS_FEE_DETAILS' => $items[$i]['holidays_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_HOLIDAYS_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['holidays_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'DISCOUNT_DAYS_FEE_DETAILS' => $items[$i]['discount_days_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_DISCOUNT_DAYS_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['discount_days_fee']) . $this->currency['sign'] . '</span></div>' : '',
                'RENTED_DATES' => json_encode($disabledDates)
            ];

            $this->template->setVars( $tplVars );
            $html .= $this->template->parse(false);
        }

        return ['cars' => $html, 'total' => $this->getFormatMoney($total)];
    }
}

?>