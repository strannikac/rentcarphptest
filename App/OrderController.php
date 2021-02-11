<?php 

namespace App;

use Model\CarModel;
use Model\OrderModel;

/**
 * Class OrderController
 * This controller contains methods for orders 
 */
class OrderController extends Controller {

    private $tplPath = _ROOT_TPL_ . 'order/';

    private $carModel;
    private $orderModel;

    private $downloadLink = '';
    private $renewLink = '';
    private $terminateLink = '';

    private $statusTerminated = 3;
    private $statusClosed = 5;

    public function __construct(array $request) {
        parent::__construct($request);

        $this->carModel = new CarModel();
        $this->orderModel = new OrderModel();

        $this->downloadLink = $this->links['orders'] . 'download/';
        $this->renewLink = $this->links['orders'] . 'renew/';
        $this->terminateLink = $this->links['orders'] . 'terminate/';

        $this->content['js'][] = 'js/order.js';
    }

    /**
     * Method main (default) - show table of orders (with cars)
     * @param void
     */
    public function main() {
        $this->content['html'] = '';

        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        $items = $this->orderModel->get($this->customer['id'], $this->language['id']);

        $orders = '';
        if($items) {
            $orders = $this->htmlOrders($items);
        }

        $this->template->set( $this->tplPath . 'main.html');

        $tplVars = [
            'STR_INVOICE' => $this->locale['STR_INVOICE'], 
            'STR_ORDER_DATE' => $this->locale['STR_ORDER_DATE'], 
            'STR_PRICE' => $this->locale['STR_PRICE'], 
            'STR_DETAILS' => $this->locale['STR_DETAILS'], 
            'STR_STATUS' => $this->locale['STR_STATUS'], 
            'STR_ACTIONS' => $this->locale['STR_ACTIONS'], 
            'STR_MY_ORDERS' => $this->locale['STR_MY_ORDERS'], 
            'TXT_MY_ORDERS' => $this->locale['TXT_MY_ORDERS'], 
            'STR_MY_PROFILE' => $this->locale['STR_MY_PROFILE'], 
            'LINK_PROFILE' => $this->links['profile'],
            'LINK_ORDERS' => $this->links['orders'],
            'ITEMS' => $orders, 
            'HIDDEN' => $orders == '' ? ' hidden' : ''
        ];

        $this->template->setVars( $tplVars );

        $this->content['html'] = $this->template->parse();
        $this->response['status'] = $this->statusSuccess;
    }

    /**
     * Method download - download order in pdf
     * @param void
     */
    public function download() {
        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $item = $this->orderModel->getOne($this->params['object'], $this->language['id']);
        
        if(!$item || empty($item['id'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $cars = $this->orderModel->getCars($item['id']);
        if(!$cars || count($cars) < 1) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }
        
        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        $html = '';

        $this->template->set( $this->tplPath . 'pdf.html');
        $this->template->set( $this->tplPath . 'pdf-item.html', false);

        $count = count($cars);
        for($j = 0; $j < $count; $j++) {
            $car = $this->carModel->getOne($cars[$j]['car_id'], $this->language['id']);

            if($this->currency['id'] != $euro['id']) {
                $cars[$j]['final_price'] = $this->getFormatMoney($cars[$j]['final_price'] / $this->currency['in_euro']);
                $cars[$j]['price'] = $this->getFormatMoney($cars[$j]['price'] / $this->currency['in_euro']);
                $cars[$j]['one_time_fee'] = $this->getFormatMoney($cars[$j]['one_time_fee'] / $this->currency['in_euro']);
                $cars[$j]['renewal_fee'] = $this->getFormatMoney($cars[$j]['renewal_fee'] / $this->currency['in_euro']);
                $cars[$j]['holidays_fee'] = $this->getFormatMoney($cars[$j]['holidays_fee'] / $this->currency['in_euro']);
                $cars[$j]['discount_days_fee'] = $this->getFormatMoney($cars[$j]['discount_days_fee'] / $this->currency['in_euro']);
                $cars[$j]['termination_penalty_fee'] = $this->getFormatMoney($cars[$j]['termination_penalty_fee'] / $this->currency['in_euro']);
            }

            $details = '';
            if($cars[$j]['one_time_fee'] > 0) {
                $details .= $this->locale['STR_ONE_TIME_FEE'] . ': <strong>' . $cars[$j]['one_time_fee'] . $this->currency['sign'] . '</strong><br/>';
            }
            if($cars[$j]['renewal_fee'] > 0) {
                $details .= $this->locale['STR_RENEWAL_FEE'] . ': <strong>' . $cars[$j]['renewal_fee'] . $this->currency['sign'] . '</strong><br/>';
            }
            if($cars[$j]['holidays_fee'] > 0) {
                $details .= $this->locale['STR_HOLIDAYS_FEE'] . ': <strong>' . $cars[$j]['holidays_fee'] . $this->currency['sign'] . '</strong><br/>';
            }
            if($cars[$j]['discount_days_fee'] > 0) {
                $details .= $this->locale['STR_DISCOUNT_DAYS_FEE'] . ': <strong>' . $cars[$j]['discount_days_fee'] . $this->currency['sign'] . '</strong><br/>';
            }
            if($cars[$j]['termination_penalty_fee'] > 0) {
                $details .= $this->locale['STR_TERMINATION_FEE'] . ': <strong>' . $cars[$j]['termination_penalty_fee'] . $this->currency['sign'] . '</strong><br/>';
            }

            if(!empty($cars[$j]['termination_date'])) {
                $cars[$j]['end_date'] = $cars[$j]['termination_date'];
            }

            $tplVars = [
                'TITLE' => $car['title'], 
                'CATEGORY' => $car['category'], 
                'DATE_FROM' => date($this->dayFormat, strtotime($cars[$j]['start_date'])), 
                'DATE_TILL' => date($this->dayFormat, strtotime($cars[$j]['end_date'])), 
                'BASE_PRICE' => $this->getFormatMoney($cars[$j]['price']), 
                'DETAILS' => $details, 
                'PRICE' => $this->getFormatMoney($cars[$j]['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign']
            ];

            $this->template->setVars( $tplVars );
            $html .= $this->template->parse(false);
        }

        if($this->currency['id'] != $euro['id']) {
            $item['final_price'] = $this->getFormatMoney($item['final_price'] / $this->currency['in_euro']);
            $item['price'] = $this->getFormatMoney($item['price'] / $this->currency['in_euro']);
            $item['one_time_fee'] = $this->getFormatMoney($item['one_time_fee'] / $this->currency['in_euro']);
            $item['renewal_fee'] = $this->getFormatMoney($item['renewal_fee'] / $this->currency['in_euro']);
            $item['holidays_fee'] = $this->getFormatMoney($item['holidays_fee'] / $this->currency['in_euro']);
            $item['discount_days_fee'] = $this->getFormatMoney($item['discount_days_fee'] / $this->currency['in_euro']);
            $item['termination_penalty_fee'] = $this->getFormatMoney($item['termination_penalty_fee'] / $this->currency['in_euro']);
        }

        $details = '';
        if($item['one_time_fee'] > 0) {
            $details .= $this->locale['STR_ONE_TIME_FEE'] . ': <strong>' . $item['one_time_fee'] . $this->currency['sign'] . '</strong><br/>';
        }
        if($item['renewal_fee'] > 0) {
            $details .= $this->locale['STR_RENEWAL_FEE'] . ': <strong>' . $item['renewal_fee'] . $this->currency['sign'] . '</strong><br/>';
        }
        if($item['holidays_fee'] > 0) {
            $details .= $this->locale['STR_HOLIDAYS_FEE'] . ': <strong>' . $item['holidays_fee'] . $this->currency['sign'] . '</strong><br/>';
        }
        if($item['discount_days_fee'] > 0) {
            $details .= $this->locale['STR_DISCOUNT_DAYS_FEE'] . ': <strong>' . $item['discount_days_fee'] . $this->currency['sign'] . '</strong><br/>';
        }
        if($item['termination_penalty_fee'] > 0) {
            $details .= $this->locale['STR_TERMINATION_FEE'] . ': <strong>' . $item['termination_penalty_fee'] . $this->currency['sign'] . '</strong><br/>';
        }

        if(!empty($item['termination_date'])) {
            $item['end_date'] = $item['termination_date'];
        }

        $tplVars = [
            'STR_ORDER_DATE' => $this->locale['STR_ORDER_DATE'], 
            'STR_CAR' => $this->locale['STR_CAR'], 
            'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
            'STR_BASE_PRICE' => $this->locale['STR_BASE_PRICE'], 
            'STR_DETAILS' => $this->locale['STR_DETAILS'], 
            'STR_PRICE' => $this->locale['STR_PRICE'], 
            'STR_TOTAL' => $this->locale['STR_TOTAL'], 
            'INVOICE' => $item['invoice'], 
            'DATE_FROM' => date($this->dayFormat, strtotime($item['start_date'])), 
            'DATE_TILL' => date($this->dayFormat, strtotime($item['end_date'])), 
            'ITEMS' => $html, 
            'BASE_PRICE' => $this->getFormatMoney($item['price']), 
            'DETAILS' => $details, 
            'TOTAL_PRICE' => $this->getFormatMoney($item['final_price']), 
            'CURRENCY_SIGN' => $this->currency['sign']
        ];

        $this->template->setVars( $tplVars );
        $html = $this->template->parse();

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output($item['invoice'] . '.pdf','D');
    }

    /**
     * Method renew - create new order from existing
     * @param void
     */
    public function renew() {
        if(!isset($this->params['post']['confirm']) || $this->params['post']['confirm'] != 1) {
            $this->content['html'] = '';
        }

        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $item = $this->orderModel->getOne($this->params['object'], $this->language['id']);
        
        if(!$item || empty($item['id']) || $item['status_id'] == $this->statusTerminated || $item['status_id'] == $this->statusClosed) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $cars = $this->orderModel->getCars($item['id']);
        if(!$cars || count($cars) < 1) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        if(isset($this->params['post']['confirm']) && $this->params['post']['confirm'] == 1) {
            //renew confirmation
            $count = count($cars);

            $carsRenew = [];
            $now = time();

            $orderPrices = [
                'price' => 0, 
                'renewal_fee' => 0, 
                'holidays_fee' => 0, 
                'final_price' => 0
            ];

            $orderStartDate = '';
            $orderEndDate = '';

            for($i = 0; $i < $count; $i++) {
                $startTime = strtotime($this->params['post']['from_' . $cars[$i]['car_id']]);
                $endTime = strtotime($this->params['post']['till_' . $cars[$i]['car_id']]);

                if(empty($this->params['post']['from_' . $cars[$i]['car_id']]) || !$startTime 
                    || empty($this->params['post']['till_' . $cars[$i]['car_id']]) || !$endTime
                ) {
                    $this->response['error'][] = $this->locale['ERR_1005'];
                    return false;
                }

                $car = $this->carModel->getOne($cars[$i]['car_id'], $this->language['id']);
                $car['start_date'] = $this->params['post']['from_' . $cars[$i]['car_id']];
                $car['end_date'] = $this->params['post']['till_' . $cars[$i]['car_id']];

                $disabledDates = $this->orderModel->getRentedDates($car['id']);
                if(!$this->orderModel->isFreePeriod($disabledDates, $car['start_date'], $car['end_date'])) {
                    $this->response['error'][] = $this->locale['ERR_1013'];
                    return false;
                }

                if($i == 0 || $orderStartDate > $car['start_date']) {
                    $orderStartDate = $car['start_date'];
                }
    
                if($i == 0 || $orderEndDate < $car['end_date']) {
                    $orderEndDate = $car['end_date'];
                }

                $prices = $this->carModel->calculateRenewPrice($car);

                $cars[$i]['final_price'] = $prices['final'];
                $cars[$i]['price'] = $prices['total'];
                $cars[$i]['renewal_fee'] = $prices['renewal_fee'];
                $cars[$i]['holidays_fee'] = $prices['holidays_fee'];
                $cars[$i]['holidays'] = $prices['holidays'];
    
                $carsRenew[] = [
                    'car_id' => $cars[$i]['car_id'], 
                    'start_date' => date('Y-m-d', strtotime($car['start_date'])), 
                    'end_date' => date('Y-m-d', strtotime($car['end_date'])), 
                    'price' => $cars[$i]['price'], 
                    'renewal_fee' => $cars[$i]['renewal_fee'], 
                    'holidays' => $cars[$i]['holidays'], 
                    'holidays_fee' => $cars[$i]['holidays_fee'], 
                    'final_price' => $cars[$i]['final_price']
                ];
    
                $orderPrices['price'] += $cars[$i]['price'];
                $orderPrices['renewal_fee'] += $cars[$i]['renewal_fee'];
                $orderPrices['holidays_fee'] += $cars[$i]['holidays_fee'];
                $orderPrices['final_price'] += $cars[$i]['final_price'];
            }

            $orderNumber = $this->customer['orders'] + 1;
            $invoice = date('Ymd') . '-' . $this->customer['id'] . '-' . $orderNumber;

            $arr = [
                'customer_id' => $this->customer['id'], 
                'parent_id' => $item['id'], 
                'invoice' => $invoice, 
                'start_date' => date('Y-m-d', strtotime($orderStartDate)), 
                'end_date' => date('Y-m-d', strtotime($orderEndDate)), 
                'price' => $orderPrices['price'], 
                'renewal_fee' => $orderPrices['renewal_fee'], 
                'holidays_fee' => $orderPrices['holidays_fee'], 
                'final_price' => $orderPrices['final_price'], 
                'creation_time' => $now, 
                'update_time' => $now
            ];

            $orderId = $this->orderModel->insert($arr);

            if ($orderId) {
                $this->orderModel->addCars($carsRenew, $orderId);

                $arr = ['orders' => $orderNumber, 'update_time' => $now];
                $this->customerModel->update($arr, $this->customer['id']);

                $this->response['status'] = $this->statusSuccess;
                $this->response['msg'] = $this->locale['SUCCESS_9000'];
                $this->response['link'] = $this->links['orders'];
            } else {
                $this->response['error'][] = $this->locale['ERR_1008'];
            }
        } else {
            //show
            $htmlCars = $this->htmlCarsRenew($cars, $item);

            if($this->currency['id'] != $euro['id']) {
                $item['final_price'] /= $this->currency['in_euro'];
                $item['price'] /= $this->currency['in_euro'];
            }

            $this->template->set( $this->tplPath . 'renew.html');

            $tplVars = [
                'STR_RENEW_ORDER' => $this->locale['STR_RENEW_ORDER'], 
                'TXT_RENEW_ORDER' => $this->locale['TXT_RENEW_ORDER'], 
                'STR_BACK_ORDERS' => $this->locale['STR_BACK_ORDERS'], 
                'STR_CAR' => $this->locale['STR_CAR'], 
                'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'STR_TOTAL' => $this->locale['STR_TOTAL'], 
                'STR_RENEW' => $this->locale['STR_RENEW'], 
                'INVOICE' => $item['invoice'],
                'LINK_ORDERS' => $this->links['orders'],
                'ITEMS' => $htmlCars, 
                'HIDDEN' => $htmlCars == '' ? ' hidden' : '', 
                'ID' => $item['id'], 
                'TOTAL_PRICE' => $this->getFormatMoney($item['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign']
            ];

            $this->template->setVars( $tplVars );

            $this->content['html'] = $this->template->parse();
            $this->response['status'] = $this->statusSuccess;
        }
    }

    /**
     * Method terminate - stop existing order
     * @param void
     */
    public function terminate() {
        if(!isset($this->params['post']['confirm']) || $this->params['post']['confirm'] != 1) {
            $this->content['html'] = '';
        }

        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $item = $this->orderModel->getOne($this->params['object'], $this->language['id']);
        
        if(!$item || empty($item['id']) || $item['status_id'] == $this->statusTerminated || $item['status_id'] == $this->statusClosed) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $cars = $this->orderModel->getCars($item['id']);
        if(!$cars || count($cars) < 1) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $euro = $this->getCurrency($this->defaults['currencyEuroId']);
        $now = time();

        if(isset($this->params['post']['confirm']) && $this->params['post']['confirm'] == 1) {
            //terminate
            $item['termination_date'] = date('Y-m-d');
            $orderPrices = [
                'old_final_price' => $item['final_price'], 
                'price' => 0, 
                'one_time_fee' => 0, 
                'renewal_fee' => 0, 
                'holidays_fee' => 0, 
                'termination_penalty_fee' => 0, 
                'discount_days_fee' => 0, 
                'final_price' => 0
            ];

            $count = count($cars);
            for($i = 0; $i < $count; $i++) {
                $cars[$i] = $this->getTerminationPrice($cars[$i], $item);

                $arr = [
                    'termination_date' => $item['termination_date'], 
                    'price' => $cars[$i]['price'],  
                    'holidays' => $cars[$i]['holidays'], 
                    'holidays_fee' => $cars[$i]['holidays_fee'], 
                    'discount_days_fee' => $cars[$i]['discount_days_fee'], 
                    'termination_penalty_fee' => $cars[$i]['termination_penalty_fee'], 
                    'final_price' => $cars[$i]['final_price']
                ];

                $this->orderModel->updateCar($arr, $cars[$i]['id']);

                $orderPrices['price'] += $cars[$i]['price'];
                $orderPrices['one_time_fee'] += $cars[$i]['one_time_fee'];
                $orderPrices['renewal_fee'] += $cars[$i]['renewal_fee'];
                $orderPrices['holidays_fee'] += $cars[$i]['holidays_fee'];
                $orderPrices['discount_days_fee'] += $cars[$i]['discount_days_fee'];
                $orderPrices['termination_penalty_fee'] += $cars[$i]['termination_penalty_fee'];
                $orderPrices['final_price'] += $cars[$i]['final_price'];
            }

            $arr = [
                'termination_date' => $item['termination_date'], 
                'price' => $orderPrices['price'], 
                'one_time_fee' => $orderPrices['one_time_fee'], 
                'renewal_fee' => $orderPrices['renewal_fee'], 
                'holidays_fee' => $orderPrices['holidays_fee'], 
                'discount_days_fee' => $orderPrices['discount_days_fee'], 
                'termination_penalty_fee' => $orderPrices['termination_penalty_fee'], 
                'final_price' => $orderPrices['final_price'], 
                'status_id' => $this->statusTerminated, 
                'update_time' => $now
            ];

            $result = $this->orderModel->update($arr, $item['id']);

            if ($result) {
                $this->response['status'] = $this->statusSuccess;
                $this->response['msg'] = $this->locale['SUCCESS_9001'];
                $this->response['link'] = $this->links['orders'];
            } else {
                $this->response['error'][] = $this->locale['ERR_1009'];
            }
        } else {
            //show
            $htmlCars = $this->htmlCarsTerminate($cars, $item);
            $orderPrices = $htmlCars['prices'];
            $htmlCars = $htmlCars['html'];

            $item['old_final_price'] = $orderPrices['old_final_price'];
            if($this->currency['id'] != $euro['id']) {
                $item['old_final_price'] /= $this->currency['in_euro'];
            }

            $this->template->set( $this->tplPath . 'terminate.html');

            $tplVars = [
                'STR_TERMINATE_ORDER' => $this->locale['STR_TERMINATE_ORDER'], 
                'TXT_TERMINATE_ORDER' => $this->locale['TXT_TERMINATE_ORDER'], 
                'STR_BACK_ORDERS' => $this->locale['STR_BACK_ORDERS'], 
                'STR_CAR' => $this->locale['STR_CAR'], 
                'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'STR_TERMINATION_FEE' => $this->locale['STR_TERMINATION_FEE'], 
                'STR_TERMINATION_PRICE' => $this->locale['STR_TERMINATION_PRICE'], 
                'STR_TOTAL' => $this->locale['STR_TOTAL'], 
                'STR_TERMINATE' => $this->locale['STR_TERMINATE'], 
                'INVOICE' => $item['invoice'],
                'LINK_ORDERS' => $this->links['orders'],
                'ITEMS' => $htmlCars, 
                'HIDDEN' => $htmlCars == '' ? ' hidden' : '', 
                'ID' => $item['id'], 
                'TOTAL_PRICE' => $this->getFormatMoney($item['old_final_price']), 
                'TOTAL_TERMINATION_FEE' => $this->getFormatMoney($orderPrices['termination_penalty_fee']), 
                'TOTAL_TERMINATION_PRICE' => $this->getFormatMoney($orderPrices['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign']
            ];

            $this->template->setVars( $tplVars );

            $this->content['html'] = $this->template->parse();
            $this->response['status'] = $this->statusSuccess;
        }
    }

    private function htmlOrders($items = []) {
        $html = '';

        $this->template->set( $this->tplPath . 'item.html');
        $this->template->set( $this->tplPath . 'item-car.html', false);
        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        $count = count($items);
        for($i = 0; $i < $count; $i++) {
            $cars = $this->orderModel->getCars($items[$i]['id']);
            $htmlCars = '';
            $len = count($cars);

            for($j = 0; $j < $len; $j++) {
                $car = $this->carModel->getOne($cars[$j]['car_id'], $this->language['id']);

                if($this->currency['id'] != $euro['id']) {
                    $cars[$j]['final_price'] /= $this->currency['in_euro'];
                    $cars[$j]['price'] /= $this->currency['in_euro'];
                    $cars[$j]['one_time_fee'] /= $this->currency['in_euro'];
                    $cars[$j]['holidays_fee'] /= $this->currency['in_euro'];
                    $cars[$j]['discount_days_fee'] /= $this->currency['in_euro'];
                    $cars[$j]['renewal_fee'] /= $this->currency['in_euro'];
                    $cars[$j]['termination_penalty_fee'] /= $this->currency['in_euro'];
                }

                $tplVars = [
                    'TITLE' => $car['title'], 
                    'CATEGORY' => $car['category'], 
                    'ID' => $cars[$j]['car_id'], 
                    'ORDER_ID' => $cars[$j]['order_id'], 
                    'PRICE' => $this->getFormatMoney($cars[$j]['final_price']), 
                    'CURRENCY_SIGN' => $this->currency['sign'], 
                    'DATE_FROM' => date($this->dayFormat, strtotime($cars[$j]['start_date'])), 
                    'DATE_TILL' => empty($cars[$j]['termination_date']) ? date($this->dayFormat, strtotime($cars[$j]['end_date'])) : date($this->dayFormat, strtotime($cars[$j]['termination_date'])), 
                    'STR_DETAILS' => $this->locale['STR_DETAILS'], 
                    'STR_CAR' => $this->locale['STR_CAR'], 
                    'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                    'STR_PRICE' => $this->locale['STR_PRICE'], 
                    'BASE_PRICE_DETAILS' => '<div><span class="label">' . $this->locale['STR_BASE_PRICE'] . ': </span><span class="value">' .  $this->getFormatMoney($cars[$j]['price']) . $this->currency['sign'] . '</span></div>', 
                    'ONE_TIME_FEE_DETAILS' => $cars[$j]['one_time_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_ONE_TIME_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($cars[$j]['one_time_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                    'RENEWAL_FEE_DETAILS' => $cars[$j]['renewal_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_RENEWAL_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($cars[$j]['renewal_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                    'HOLIDAYS_DETAILS' => $cars[$j]['holidays_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_WEEKENDS_HOLIDAYS'] . ': </span><span class="value">' .  $cars[$j]['holidays'] . '</span></div>' : '', 
                    'HOLIDAYS_FEE_DETAILS' => $cars[$j]['holidays_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_HOLIDAYS_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($cars[$j]['holidays_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                    'DISCOUNT_DAYS_FEE_DETAILS' => $cars[$j]['discount_days_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_DISCOUNT_DAYS_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($cars[$j]['discount_days_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                    'TERMINATION_PENALTY_FEE_DETAILS' => $cars[$j]['termination_penalty_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_TERMINATION_PENALTY_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($cars[$j]['termination_penalty_fee']) . $this->currency['sign'] . '</span></div>' : ''
                ];

                $this->template->setVars( $tplVars );
                $htmlCars .= $this->template->parse(false);
            }

            if($this->currency['id'] != $euro['id']) {
                $items[$i]['final_price'] /= $this->currency['in_euro'];
                $items[$i]['price'] /= $this->currency['in_euro'];
                $items[$i]['one_time_fee'] /= $this->currency['in_euro'];
                $items[$i]['holidays_fee'] /= $this->currency['in_euro'];
                $items[$i]['discount_days_fee'] /= $this->currency['in_euro'];
                $items[$i]['renewal_fee'] /= $this->currency['in_euro'];
                $items[$i]['termination_penalty_fee'] /= $this->currency['in_euro'];
            }

            $linkRenew = '';
            $linkTerminate = '';

            if($items[$i]['status_id'] != $this->statusTerminated && $items[$i]['status_id'] != $this->statusClosed) {
                $linkRenew = '<a href="' . $this->renewLink . $items[$i]['id'] . '/' . '" class="js-link-renew">' . $this->locale['STR_RENEW'] . '</a><br/>';
                $linkTerminate = '<a href="' . $this->terminateLink . $items[$i]['id'] . '/' . '" class="js-link-terminate">' . $this->locale['STR_TERMINATE'] . '</a><br/>';
            }

            $tplVars = [
                'STR_INVOICE' => $this->locale['STR_INVOICE'], 
                'STR_ORDER_DATE' => $this->locale['STR_ORDER_DATE'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'STR_DETAILS' => $this->locale['STR_DETAILS'], 
                'STR_STATUS' => $this->locale['STR_STATUS'], 
                'STR_CAR' => $this->locale['STR_CAR'], 
                'STR_CARS' => $this->locale['STR_CARS'], 
                'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                'STR_DOWNLOAD_PDF' => $this->locale['STR_DOWNLOAD_PDF'], 
                'ID' => $items[$i]['id'], 
                'INVOICE' => $items[$i]['invoice'], 
                'PRICE' => $this->getFormatMoney($items[$i]['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign'], 
                'DATE_FROM' => date($this->dayFormat, strtotime($items[$i]['start_date'])), 
                'DATE_TILL' => empty($items[$i]['termination_date']) ? date($this->dayFormat, strtotime($items[$i]['end_date'])) : date($this->dayFormat, strtotime($items[$i]['termination_date'])), 
                'STATUS' => $items[$i]['status'], 
                'LINK_DOWNLOAD' => $this->downloadLink . $items[$i]['id'] . '/', 
                'LINK_RENEW' => $linkRenew, 
                'LINK_TERMINATE' => $linkTerminate, 
                'BASE_PRICE_DETAILS' => '<div><span class="label">' . $this->locale['STR_BASE_PRICE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['price'], 2) . $this->currency['sign'] . '</span></div>', 
                'ONE_TIME_FEE_DETAILS' => $items[$i]['one_time_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_ONE_TIME_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['one_time_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'RENEWAL_FEE_DETAILS' => $items[$i]['renewal_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_RENEWAL_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['renewal_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'HOLIDAYS_FEE_DETAILS' => $items[$i]['holidays_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_HOLIDAYS_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['holidays_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'DISCOUNT_DAYS_FEE_DETAILS' => $items[$i]['discount_days_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_DISCOUNT_DAYS_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['discount_days_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'TERMINATION_PENALTY_FEE_DETAILS' => $items[$i]['termination_penalty_fee'] > 0 ? '<div><span class="label">' . $this->locale['STR_TERMINATION_PENALTY_FEE'] . ': </span><span class="value">' .  $this->getFormatMoney($items[$i]['termination_penalty_fee']) . $this->currency['sign'] . '</span></div>' : '', 
                'CARS' => $htmlCars
            ];

            $this->template->setVars( $tplVars );
            $html .= $this->template->parse();
        }

        return $html;
    }

    private function htmlCarsRenew(array $cars = [], array $order) {
        $html = '';

        $this->template->set( $this->tplPath . 'renew-item.html');
        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        $orderStartDate = strtotime($order['end_date'] . '+ 1 days');

        $count = count($cars);
        for($j = 0; $j < $count; $j++) {
            $car = $this->carModel->getOne($cars[$j]['car_id'], $this->language['id']);

            if($this->currency['id'] != $euro['id']) {
                $cars[$j]['final_price'] /= $this->currency['in_euro'];
                $cars[$j]['price'] /= $this->currency['in_euro'];
                $cars[$j]['holidays_fee'] /= $this->currency['in_euro'];
                $cars[$j]['renewal_fee'] /= $this->currency['in_euro'];
                $car['price'] /= $this->currency['in_euro'];
            }

            $startDate = new \DateTime($cars[$j]['end_date']);
            $endDate = new \DateTime($cars[$j]['end_date']);

            $interval = $startDate->diff($endDate);
            $days = $interval->format('%a') + 2;

            $disabledDates = $this->orderModel->getRentedDates($car['id']);
            $rentPeriod = $this->orderModel->getRentDates($disabledDates, $days, date($this->dayFormat, $orderStartDate));

            $tplVars = [
                'STR_CAR' => $this->locale['STR_CAR'], 
                'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'TITLE' => $car['title'], 
                'CATEGORY' => $car['category'], 
                'ID' => $cars[$j]['car_id'], 
                'MIN_DAYS' => $car['min_days'], 
                'MAX_DAYS' => $car['max_days'], 
                'DAY_PRICE' => $this->getFormatMoney($car['price']), 
                'HOLIDAY_PERCENT' => $car['holiday_coef_percent'], 
                'RENEWAL_FEE' => $car['renewal_fee'], 
                'PRICE' => $this->getFormatMoney($cars[$j]['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign'], 
                'DATE_FROM' => date($this->dayFormat, strtotime($rentPeriod['start_date'])), 
                'DATE_TILL' => date($this->dayFormat, strtotime($rentPeriod['end_date'])),
                'RENTED_DATES' => json_encode($disabledDates)
            ];

            $this->template->setVars( $tplVars );
            $html .= $this->template->parse();
        }

        return $html;
    }

    private function htmlCarsTerminate(array $cars = [], array $order) {
        $html = '';

        $this->template->set( $this->tplPath . 'terminate-item.html');

        $order['termination_date'] = date('Y-m-d');
        $orderPrices = [
            'old_final_price' => $order['final_price'], 
            'price' => 0, 
            'one_time_fee' => 0, 
            'renewal_fee' => 0, 
            'holidays_fee' => 0, 
            'termination_penalty_fee' => 0, 
            'discount_days_fee' => 0, 
            'final_price' => 0
        ];

        $euro = $this->getCurrency($this->defaults['currencyEuroId']);

        $count = count($cars);
        for($i = 0; $i < $count; $i++) {
            $cars[$i] = $this->getTerminationPrice($cars[$i], $order);

            if($this->currency['id'] != $euro['id']) {
                $cars[$i]['old_final_price'] /= $this->currency['in_euro'];
                $cars[$i]['final_price'] /= $this->currency['in_euro'];
                $cars[$i]['price'] /= $this->currency['in_euro'];
                $cars[$i]['one_time_fee'] /= $this->currency['in_euro'];
                $cars[$i]['renewal_fee'] /= $this->currency['in_euro'];
                $cars[$i]['holidays_fee'] /= $this->currency['in_euro'];
                $cars[$i]['discount_days_fee'] /= $this->currency['in_euro'];
                $cars[$i]['termination_penalty_fee'] /= $this->currency['in_euro'];
            }

            $tplVars = [
                'STR_CAR' => $this->locale['STR_CAR'], 
                'STR_RENT_DATE' => $this->locale['STR_RENT_DATE'], 
                'STR_PRICE' => $this->locale['STR_PRICE'], 
                'STR_TERMINATION_FEE' => $this->locale['STR_TERMINATION_FEE'], 
                'STR_TERMINATION_PRICE' => $this->locale['STR_TERMINATION_PRICE'], 
                'TITLE' => $cars[$i]['title'], 
                'CATEGORY' => $cars[$i]['category'], 
                'ID' => $cars[$i]['car_id'], 
                'DATE_FROM' => $cars[$i]['start_date'], 
                'DATE_TILL' => $cars[$i]['end_date'], 
                'PRICE' => $this->getFormatMoney($cars[$i]['old_final_price']), 
                'TERMINATION_FEE' => $this->getFormatMoney($cars[$i]['termination_penalty_fee']), 
                'TERMINATION_PRICE' => $this->getFormatMoney($cars[$i]['final_price']), 
                'CURRENCY_SIGN' => $this->currency['sign'], 
            ];

            $orderPrices['price'] += $cars[$i]['price'];
            $orderPrices['one_time_fee'] += $cars[$i]['one_time_fee'];
            $orderPrices['renewal_fee'] += $cars[$i]['renewal_fee'];
            $orderPrices['holidays_fee'] += $cars[$i]['holidays_fee'];
            $orderPrices['discount_days_fee'] += $cars[$i]['discount_days_fee'];
            $orderPrices['termination_penalty_fee'] += $cars[$i]['termination_penalty_fee'];
            $orderPrices['final_price'] += $cars[$i]['final_price'];

            $this->template->setVars( $tplVars );
            $html .= $this->template->parse();
        }

        return ['html' => $html, 'prices' => $orderPrices];
    }

    private function getTerminationPrice(array $item, array $order) {
        $car = $this->carModel->getOne($item['car_id'], $this->language['id']);
        $car['start_date'] = $item['start_date'];
        $car['end_date'] = $order['termination_date'];

        $item['title'] = $car['title'];
        $item['category'] = $car['category'];

        if($order['parent_id'] > 0) {
            $car['is_renew'] = 1;
        }
        
        if($order['termination_date'] < $item['start_date']) {
            $car['end_date'] = $item['start_date'];
        }

        $item['old_final_price'] = $item['final_price'];

        if($order['termination_date'] < $item['end_date']) {
            $prices = $this->carModel->calculateTerminationPrice($car);

            if($car['termination_penalty_percent'] > 0) {
                $item['termination_penalty_fee'] = ($item['old_final_price'] - $prices['final']) * $car['termination_penalty_percent'] / 100;
            }

            $item['price'] = $prices['total'];
            $item['final_price'] = $prices['final'];
            $item['holidays_fee'] = $prices['holidays_fee'];
            $item['holidays'] = $prices['holidays'];
            $item['discount_days_fee'] = $prices['discount_days_fee'];
        }

        return $item;
    }
}
?>