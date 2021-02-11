<?php 

namespace App;

use Service\Db;
use Service\GeoLocation;
use Model\CustomerModel;
use Model\LocationModel;
use Model\OrderModel;
use Model\PageModel;
use Helper\System;
use Helper\Template;
use Helper\Validator;

class Controller {
    protected $db;

    protected $allowedMethods = ['GET', 'POST'];

    protected $arrBool = [0,1];
    protected $validator;
    protected $template;

    protected $permissions;

    protected $customerModel;

    protected $customer = [];
    protected $customerId = '';
    protected $customerToken = '';
    protected $sessionToken = '';

    protected $locationModel;
    protected $pageModel;

    protected $request = [];
    protected $params = [];
    protected $response = [];

    protected $language = [];
    protected $locale = [];
    protected $location = [];
    protected $currencies = [];
    protected $currency = [];

    protected $page = [];
    protected $content = [];

    protected $defaults = [];
    protected $error;

    protected $links = [];
    protected $statusSuccess = 'success';
    protected $statusFail = 'error';

    protected $nophoto = _URL_IMAGES_ . 'nophoto.png';

    protected $dayFormat = 'd.m.Y';
    protected $moneyDecimals = 2;
    protected $moneySep = '.';
    protected $moneyThousandsSep = '';

    public function __construct(array $request) {
        $this->db = Db::getInstance();
        $this->request = $request;
        $this->params = $this->request['params'];
        $this->language = $this->request['language'];
        $this->defaults = $this->request['defaults'];
        $this->currencies = $this->request['currencies'];

        $this->validator = new Validator();
        $this->template = new Template();
        $this->customerModel = new CustomerModel();
        $this->locationModel = new LocationModel();
        $this->pageModel = new PageModel();

        if($this->language['id'] == 1) {
            $this->dayFormat = 'm/d/Y';
        }

        $this->sessionToken = empty($_COOKIE['sessionToken']) ? System::setSessionToken() : $_COOKIE['sessionToken'];

        if(!empty($_COOKIE['customerId']) && $this->validator->isInteger($_COOKIE['customerId'])) {
            $this->customerId = $_COOKIE['customerId'];
        }

        if(!empty($_COOKIE['customerToken'])) {
            $this->customerToken = $_COOKIE['customerToken'];
        }

        if($this->customerToken != '' && $this->customerId != '') {
	        if( $this->customerModel->checkToken($this->customerId, $this->customerToken) ) {
                $customer = $this->customerModel->getById($this->customerId);
                if($customer) {
                    $this->customer = $customer;
                }
            } else {
                $this->response['logout'] = 1;
            }
        }

        $this->setCurrency();

        $this->setPage();

        if(!empty($this->request['region'])) {
            $this->setRegionByUri($this->request['region']);
        }

        if(empty($this->request['region']) || empty($this->location)) {
            $ip = System::getIP();

            if($ip) {
                $location = new GeoLocation();
                $currentLocation = $location->getByIP($ip);

                $this->setRegionByLocation($currentLocation);
            }
        }

        $this->checkRegion();
        $this->checkOrders();

        $this->links = [
            'profile' => _SERVER_URL_ . $this->language['iso'] . '/' . $this->location['region']['url'] . '/profile/', 
            'orders' => _SERVER_URL_ . $this->language['iso'] . '/' . $this->location['region']['url'] . '/order/'
        ];
    }

    private function setCurrency() {
        if(empty($this->customer['currency_id'])) {
            $this->currency = $this->getCurrency();
        } else {
            $this->currency = $this->getCurrency($this->customer['currency_id']);
        }
    }

    protected function getCurrency(int $id = 0) {
        $count = count($this->currencies);

        for($i = 0; $i < $count; $i++) {
            if($id > 0) {
                if($this->currencies[$i]['id'] == $id) {
                    return $this->currencies[$i];
                }
            } else {
                if($this->currencies[$i]['is_def'] == 1) {
                    return $this->currencies[$i];
                }
            }
        }

        return false;
    }

    private function setPage() {
        $uri = $this->request['controller'];
        $uri2 = $uri;

        if($this->request['controller'] == $this->defaults['controller'] && $this->request['action'] == $this->defaults['actionPage']) {
            $uri = $this->params['object'];
        } else if(!empty($this->request['action']) && !is_numeric($this->request['action']) && $this->request['action'] != $this->defaults['action']) {
            $uri = $this->request['action'];
        }

        $row = $this->pageModel->getByUri($uri, $this->language['id']);
        
        if( $row && !empty( $row['id'] ) ) {
            $this->page = $row;
        } elseif($uri2 !== '') {
            $row2 = $this->pageModel->getByUri($uri2, $this->language['id']);

            if( $row2 && !empty( $row2['id'] ) ) {
                $this->page = $row2;
            }
        }

        if(!isset($this->page['id'])) {
            $this->page['is404'] = 1;
        }
    }

    private function setRegionByLocation(array $location) {
        $row = $this->locationModel->getCountryByIso($location['countryIso'], $this->language['id']);

        if( $row && !empty( $row['id'] ) ) {
            $this->location['country'] = $row;

            $rowRegion = $this->locationModel->getRegionByName($location['city'], $this->language['id']);

            if( $rowRegion && !empty( $rowRegion['id'] ) ) {
                $this->location['region'] = $rowRegion;
            }
        }
    }

    private function setRegionByUri(String $uri) {
        $row = $this->locationModel->getByUri($uri, $this->language['id']);

        if( $row && !empty( $row['id'] ) ) {
            $this->location['country'] = [
                'id' => $row['id'], 
                'iso' => $row['iso'], 
                'iso2' => $row['iso2'], 
                'title' => $row['title']
            ];

            $this->location['region'] = [
                'id' => $row['regionId'], 
                'parent_id' => $row['regionParentId'], 
                'title' => $row['regionTitle'], 
                'url' => $row['url']
            ];
        }
    }

    private function checkRegion() {
        if(!isset($this->location['country'])) {
            $row = $this->locationModel->getCountryById($this->defaults['countryId'], $this->language['id']);
            if($row) {
                $this->location['country'] = $row;
            }
        }

        if(!isset($this->location['region'])) {
            $row = $this->locationModel->getRegionById($this->defaults['regionId'], $this->language['id']);
            if($row) {
                $this->location['region'] = $row;
            }
        }
    }

    private function checkOrders() {
        $model = new OrderModel();
        $model->check();
    }

    public function setLocale(array $locale) {
        $this->locale = $locale;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getContent() {
        if(!isset($this->customer['id']) && isset($this->content['html']) && !empty($this->params['get']['code'])) {
            $this->content['html'] .= '<script>$(function(){ showRegistrationConfirmPopup(\'' . $this->params['get']['code'] . '\'); });</script>';
        }
        if(!isset($this->customer['id']) && isset($this->content['html']) && !empty($this->params['get']['recovery'])) {
            $this->content['html'] .= '<script>$(function(){ showRecoveryPopup(\'' . $this->params['get']['recovery'] . '\'); });</script>';
        }
        if(isset($this->response['logout']) && isset($this->content['html'])) {
            $this->content['html'] .= '<script>$(function(){ logout(); });</script>';
        }

        $arr = $this->content;
        $arr['language'] = $this->language;
        $arr['currency'] = $this->currency;
        $arr['page'] = $this->page;
        $arr['location'] = $this->location;
        $arr['customer'] = $this->customer;

        return $arr;
    }

    public function getSessionToken() {
        return $this->sessionToken;
    }

    protected function getFormatMoney($money) {
        return number_format($money, $this->moneyDecimals, $this->moneySep, $this->moneyThousandsSep);
    }
}

?>