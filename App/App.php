<?php

namespace App;

use Service\Db;
use Helper\Template;
use Helper\System;
use Model\LanguageModel;
use Model\LocaleModel;
use Model\LocationModel;
use Model\PageModel;
use Model\CurrencyModel;
use Model\BasketModel;

class App {
    private $db;

    private $now;

    private $controllers = [
        'main' => [
            'class' => 'App\\MainController', 
            'page' => '', 
            'error404' => ''
        ],
        'customer' => [
            'class' => 'App\\CustomerController', 
            'main' => '',
            'country' => '',
            'login' => '',
            'registration' => '',
            'confirmation' => '',
            'forgot' => '',
            'recovery' => ''
        ], 
        'profile' => [
            'class' => 'App\\ProfileController', 
            'main' => '',
            'lang' => '',
            'save' => '',
            'password' => ''
        ], 
        'car' => [
            'class' => 'App\\CarController', 
            'main' => '',
            'search' => '',
            'inbasket' => ''
        ], 
        'basket' => [
            'class' => 'App\\BasketController', 
            'main' => '',
            'delete' => '',
            'save' => '',
            'checkout' => ''
        ], 
        'order' => [
            'class' => 'App\\OrderController', 
            'main' => '',
            'download' => '',
            'renew' => '',
            'terminate' => ''
        ]
    ];

    protected $defaults = [
        'controller' => 'main', 
        'action' => 'main', 
        'actionPage' => 'page', 
        'countryId' => '201', 
        'regionId' => '1002', 
        'languageIso' => '', 
        'currencyEuroId' => 1
    ];

    private $response = [];
    private $content = [];
    private $sessionToken = '';

    private $languages = [];
    private $locale = [];
    private $currencies = [];

    private $clientUri = '';
    private $firstUriItem = 0;

    public function __construct() {
        session_start();

        $this->run();
    }

    private function run() {
        $this->now = date('d.m.Y H:i:s');

        $this->db = Db::getInstance();
        $this->db->execSql('SET NAMES utf8;');

        $this->setLangs();

        $requestData = $this->getRequestData();

        if($requestData) {
            $requestData['defaults'] = $this->defaults;
            $requestData['clientUri'] = $this->clientUri;

            $this->setCurrencies($requestData['language']['id']);

            $requestData['currencies'] = $this->currencies;

            $class = $this->controllers[$requestData['controller']]['class'];
            $controller = new $class($requestData);
            $controller->setLocale($this->locale);

            $action = $requestData['action'];
            $controller->$action();

            $this->response = ['controller' => $requestData['controller'], 'action' => $requestData['action'], 'time' => $this->now];
            $this->response['data'] = $controller->getResponse();

            $this->content = $controller->getContent();
            $this->sessionToken = $controller->getSessionToken();
        }

        $this->showResponse();
    }

    private function setLangs() {
        $model = new LanguageModel();
        $result = $model->get();

        if($result) {
            $count = count($result);

            for($i = 0; $i < $count; $i++) {
                $row = $result[$i];
                $this->languages[$row['iso']] = ['id' => $row['id'], 'iso' => $row['iso'], 'title' => $row['title'], 'def' => $row['is_def']];

                if($row['is_def'] == 1) {
                    $this->defaults['languageIso'] = $row['iso'];
                }
            }
        }
    }

    private function setLocale(int $lang) {
        $model = new LocaleModel();
        $result = $model->get($lang);

        if($result) {
            $this->locale = $result;
        }
    }

    private function setCurrencies(int $lang) {
        $model = new CurrencyModel();
        $result = $model->get($lang);

        if($result) {
            $this->currencies = $result;
        }
    }

    private function getRequestData() {
        $arr = ['controller' => '', 'action' => '', 'page' => [], 'language' => [], 'region' => '', 'params' => []];

        $len = mb_strpos($_SERVER['REQUEST_URI'], '?');
        $uri = $_SERVER['REQUEST_URI'];
        $params = '';

        if( $len > 0 ) {
            $uri = mb_substr($_SERVER['REQUEST_URI'], 0, $len);
            $params = mb_substr($_SERVER['REQUEST_URI'], $len);
        }

        $arr_tmp = explode( '/', $uri );
        $count = count( $arr_tmp );
        $arr_uri = [];

        for( $i = 0; $i < $count; $i++ ) {
            $arr_tmp[$i] = System::cleanString($arr_tmp[$i]);

            if( !empty( $arr_tmp[$i] ) ) {
                $arr_uri[] = $arr_tmp[$i];
            }
        }

        $index = $this->firstUriItem;

        if( empty( $arr_uri[$index] ) || !isset($this->languages[$arr_uri[$index]]) ) {
            $arr['language'] = $this->languages[$this->defaults['languageIso']];
        } else {
            $arr['language'] = $this->languages[$arr_uri[$index]];
        }

        $this->setLocale($arr['language']['id']);

        $index++;

        if( !empty( $arr_uri[$index] ) ) {
            $arr['region'] = $arr_uri[$index];
        }

        $index++;

        if( empty( $arr_uri[$index] ) ) {
            $arr['controller'] = $this->defaults['controller'];
        } else {
            $arr['controller'] = $arr_uri[$index];
            $this->clientUri .= $arr_uri[$index] . '/';
        }

        $index++;
	
        if( empty( $arr_uri[$index] ) ) {
            $arr['action'] = $this->defaults['action'];
        } else {
            if( is_numeric( $arr_uri[$index] ) ) {
                $arr['action'] = $this->defaults['action'];
                $arr['params']['object'] = $arr_uri[$index];
            } else {
                $arr['action'] = $arr_uri[$index];
            }
            $this->clientUri .= $arr_uri[$index] . '/';
        }

        $index++;
	
        if( !empty( $arr_uri[$index] ) ) {
            $arr['params']['object'] = $arr_uri[$index];
            $this->clientUri .= $arr_uri[$index] . '/';
        }

        if( isset( $_POST ) && is_array( $_POST ) ) {
            foreach( $_POST as $k => $v ) {
                $arr['params']['post'][$k] = $v;
            }
        }

        if( isset( $_GET ) && is_array( $_GET ) ) {
            foreach( $_GET as $k => $v ) {
                $arr['params']['get'][$k] = $v;
            }
        }

        $this->clientUri .= $params;

        if( !isset( $this->controllers[$arr['controller']] ) ) {
            if($arr['action'] == $this->defaults['action']) {
                $arr['action'] = $arr['controller'];
            }

            $arr['controller'] = $this->defaults['controller'];
        }

        if( $arr['controller'] == $this->defaults['controller'] && (!isset( $this->controllers[$arr['controller']][$arr['action']] ) || $arr['action'] == $this->defaults['action']) ) {
            $arr['params']['object'] = $arr['action'];
            $arr['action'] = $this->defaults['actionPage'];
        }

        if( !isset( $this->controllers[$arr['controller']]['class'] ) 
            || !isset( $this->controllers[$arr['controller']][$arr['action']] )
            || $this->controllers[$arr['controller']][$arr['action']] == 'class' 
        ) {
            $this->response['error'][] = $this->locale['ERR_1002'];
            return false;
        }

        return $arr;
    }

    /**
     * show response for app (json string or html)
     */
    private function showResponse() {
        if( isset( $this->content['html'] ) ) {
            echo $this->configureContent();
            exit;
        }

        $jsonResponse = json_encode( $this->response );

        echo $jsonResponse;
        exit;
    }

    private function configureContent() {
        $tplVars = [];
        $indexVars = [];

        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'index.html' );

        $indexVars['js'] = '';
        $indexVars['css'] = '';
        $indexVars['popups'] = '';

        if(isset($this->content['popups'])) {
            $indexVars['css'] .= $this->content['popups'];
        }

        if(empty($this->content['customer']['id'])) {
            $this->content['js'][] = 'js/login.js';
            $this->content['js'][] = 'js/registration.js';

            $indexVars['popups'] .= $this->htmlRegistrationPopup();
            $indexVars['popups'] .= $this->htmlLoginPopup();
        } else {
            $this->content['js'][] = 'js/profile.js';
        }

        $indexVars['lang_switcher'] = $this->htmlLangSwitcher();
        $indexVars['location_switcher'] = $this->htmlLocationSwitcher();
        $indexVars['main_menu'] = $this->htmlMainMenu();
        $indexVars['header_profile'] = $this->htmlHeaderProfile();

        $indexVars['css'] = '';
        $tpl->set( _ROOT_TPL_ . 'css.html', false );
        if(isset($this->content['css'])) {
            $count = count($this->content['css']);
            for($i = 0; $i < $count; $i++) {
                $tplVars = ['_path_' => _URL_WEB_ . $this->content['css'][$i]];
                $tpl->setVars($tplVars);
                $indexVars['css'] .= $tpl->parse(false);
            }
        }

        $tplVars['js'] = '';
        $tpl->set( _ROOT_TPL_ . 'js.html', false );
        if(isset($this->content['js'])) {
            $count = count($this->content['js']);
            for($i = 0; $i < $count; $i++) {
                $tplVars = ['_path_' => _URL_WEB_ . $this->content['js'][$i]];
                $tpl->setVars($tplVars);
                $indexVars['js'] .= $tpl->parse(false);
            }
        }

        if(isset($this->content['page']['is404']) || ($this->content['html'] == '' && !empty($this->response['data']['error']))) {
            $this->showErrorPage();
        }

        $indexVars['_home_url_'] = _CLIENT_URL_ . $this->content['language']['iso'] . '/';
        $indexVars['_domain_'] = _DOMAIN_;
        $indexVars['_path_api_'] = _SERVER_URL_ . $this->content['language']['iso'] . '/' . $this->content['location']['region']['url'] . '/';
        $indexVars['_path_html_'] = _URL_WEB_;
        $indexVars['_path_img_'] = _URL_IMAGES_;
        $indexVars['_locale_'] = json_encode($this->locale);
        $indexVars['language_id'] = $this->content['language']['id'];
        $indexVars['language_iso'] = $this->content['language']['iso'];

        $indexVars['content'] = $this->content['html'];
        $indexVars['meta_title'] = $this->content['page']['meta_title'];
        $indexVars['meta_description'] = $this->content['page']['meta_description'];
        $indexVars['meta_keywords'] = $this->content['page']['meta_keywords'];

        $tpl->setVars($indexVars);
        $html = $tpl->parse();
        
        return $html;
    }

    private function htmlLangSwitcher() {
        $html = '';
        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'lang-switcher.html' );
        $tpl->set( _ROOT_TPL_ . 'lang-switcher-item.html', false );

        foreach( $this->languages as $iso => $lang ) {
            $link = _CLIENT_URL_ . $iso . '/' . $this->content['location']['region']['url'] . '/' . $this->clientUri;

            $tplVars = [
                'sel' => $iso == $this->content['language']['iso'] ? ' class="sel"' : '',
                'link' => $link,
                'name' => $lang['title'],
                'iso' => $iso,
                'id' => $lang['id']
            ];

            $tpl->setVars( $tplVars );
            $html .= $tpl->parse(false);
        }

        $tplVars = [
            'name' => $this->content['language']['title'],
            'iso' => $this->content['language']['iso'],
            'langs' => $html
        ];

        $tpl->setVars( $tplVars );
        return $tpl->parse();
    }

    private function htmlLocationSwitcher() {
        $html = '';

        $model = new LocationModel();
        $countries = $model->getCountries($this->content['language']['id']);

        if(!$countries) {
            return $html;
        }

        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'location-switcher.html' );
        $indexVars = [];

        $tplCountry = new Template();
        $tplCountry->set( _ROOT_TPL_ . 'location-country-item.html' );

        $count = count($countries);
        for($i = 0; $i < $count; $i++) {
            $tplVars = [
                'sel' => $countries[$i]['id'] == $this->content['location']['country']['id'] ? ' class="sel"' : '',
                'name' => $countries[$i]['title'],
                'iso' => $countries[$i]['iso2'],
                'id' => $countries[$i]['id']
            ];

            $tplCountry->setVars( $tplVars );
            $html .= $tplCountry->parse();
        }

        $indexVars['countries'] = $html;
        $indexVars['country'] = $this->content['location']['country']['title'];

        $regions = $model->getRegionByCountryId($this->content['location']['country']['id'], $this->content['language']['id']);

        if(!$regions) {
            $indexVars['regions'] = '';
            $indexVars['region'] = '';

            $tpl->setVars( $indexVars );
            $html = $tpl->parse();

            return $html;
        }

        $tplRegion = new Template();
        $tplRegion->set( _ROOT_TPL_ . 'location-region-item.html' );
        $html = '';

        foreach( $regions as $id => $item ) {
            $link = _CLIENT_URL_ . $this->content['language']['iso']  . '/' . $item['url'] . '/' . $this->clientUri;

            if($id == $this->content['location']['region']['id']) {
                $this->content['location']['region']['path'] = $item['path'];
            }

            $tplVars = [
                'sel' => $id == $this->content['location']['region']['id'] ? ' class="sel"' : '',
                'link' => $link,
                'name' => $item['path'],
                'uri' => $item['url'],
                'id' => $id
            ];

            $tplRegion->setVars( $tplVars );
            $html .= $tplRegion->parse();
        }

        $indexVars['regions'] = $html;
        $indexVars['region'] = $this->content['location']['region']['path'];
        $indexVars['uri'] = $this->content['location']['region']['url'];
        $indexVars['id'] = $this->content['location']['country']['id'];

        $tpl->setVars( $indexVars );
        return $tpl->parse();
    }

    private function htmlMainMenu() {
        $html = '';

        $model = new PageModel();
        $pages = $model->getMainMenu($this->content['language']['id']);

        if(!$pages) {
            return $html;
        }

        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'main-nav-item.html');

        $count = count($pages);

        for($i = 0; $i < $count; $i++) {
            $link = _CLIENT_URL_ . $this->content['language']['iso'] . '/' . $this->content['location']['region']['url'] . '/' . $pages[$i]['url'] . '/';

            $tplVars = [
                'sel' => $pages[$i]['id'] == $this->content['page']['id'] ? ' class="sel"' : '',
                'link' => $link,
                'name' => $pages[$i]['title'],
                'id' => $pages[$i]['id']
            ];

            $tpl->setVars( $tplVars );
            $html .= $tpl->parse();
        }

        return $html;
    }

    private function htmlHeaderProfile() {
        $html = '';

        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'header-profile.html');

        $tplVars = [];

        if(empty($this->content['customer']['id'])) {
            $tpl->set( _ROOT_TPL_ . 'header-profile-guest.html', false );

            $tplVars = [
                'str_signin' => $this->locale['STR_SIGNIN'],
                'str_signup' => $this->locale['STR_SIGNUP']
            ];
        } else {
            $tpl->set( _ROOT_TPL_ . 'header-profile-customer.html', false );

            $tplVars = [
                'str_profile' => $this->locale['STR_PROFILE'],
                'str_signout' => $this->locale['STR_SIGNOUT'], 
                'link_profile' => _CLIENT_URL_ . $this->content['language']['iso'] . '/' . $this->content['location']['region']['url'] . '/profile/',
                'link_signout' => '#',
            ];
        }

        $tpl->setVars( $tplVars );
        $html = $tpl->parse(false);

        $model = new BasketModel();
        $items = $model->get($this->sessionToken);
        $count = 0;
        if($items) {
            $count = count($items);
        }

        $tplVars = [
            'profile' => $html,
            'link_basket' => _CLIENT_URL_ . $this->content['language']['iso'] . '/' . $this->content['location']['region']['url'] . '/basket/', 
            'HIDDEN' => $count > 0 ? '' : ' hidden', 
            'amount' => $count > 0 ? $count : '', 
        ];
        $tpl->setVars( $tplVars );
        $html = $tpl->parse();

        return $html;
    }

    private function htmlRegistrationPopup() {
        $html = '';

        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'registration.html');

        $tplVars = [
            'hdr_signup' => $this->locale['HDR_SIGNUP'],
            'txt_signup' => $this->locale['TXT_SIGNUP'], 
            'str_email' => $this->locale['STR_EMAIL'], 
            'str_signup' => $this->locale['STR_SIGNUP'], 
            'STR_PASSWORD' => $this->locale['STR_PASSWORD'], 
            'STR_RETYPE_PASSWORD' => $this->locale['STR_RETYPE_PASSWORD'], 
            'TXT_SIGNUP_TERMS' => $this->locale['TXT_SIGNUP_TERMS'], 
            'HDR_SIGNUP_CONFIRM' => $this->locale['HDR_SIGNUP_CONFIRM'], 
            'TXT_SIGNUP_CONFIRM' => $this->locale['TXT_SIGNUP_CONFIRM'], 
            'STR_TYPE_CODE' => $this->locale['STR_TYPE_CODE'], 
            'STR_CONTINUE' => $this->locale['STR_CONTINUE'], 
            'HDR_SIGNUP_COMPLETE' => $this->locale['HDR_SIGNUP_COMPLETE'], 
            'TXT_SIGNUP_COMPLETE' => $this->locale['TXT_SIGNUP_COMPLETE']
        ];

        $tpl->setVars( $tplVars );
        $html = $tpl->parse();

        return $html;
    }

    private function htmlLoginPopup() {
        $html = '';

        $tpl = new Template();
        $tpl->set( _ROOT_TPL_ . 'login.html');

        $tplVars = [
            'HDR_SIGNIN' => $this->locale['HDR_SIGNIN'],
            'STR_EMAIL' => $this->locale['STR_EMAIL'], 
            'STR_SIGNIN' => $this->locale['STR_SIGNIN'], 
            'STR_PASSWORD' => $this->locale['STR_PASSWORD'], 
            'STR_FORGOT_PASSWORD' => $this->locale['STR_FORGOT_PASSWORD'], 
            'HDR_FORGOT_PASSWORD' => $this->locale['HDR_FORGOT_PASSWORD'], 
            'TXT_FORGOT_PASSWORD' => $this->locale['TXT_FORGOT_PASSWORD'], 
            'STR_SEND' => $this->locale['STR_SEND'], 
            'STR_SAVE' => $this->locale['STR_SAVE'], 
            'HDR_RECOVERY_PASSWORD' => $this->locale['HDR_RECOVERY_PASSWORD'], 
            'TXT_RECOVERY_PASSWORD' => $this->locale['TXT_RECOVERY_PASSWORD'], 
            'STR_RETYPE_PASSWORD' => $this->locale['STR_RETYPE_PASSWORD'], 
            'TXT_FORGOT_RESPONSE' => $this->locale['TXT_FORGOT_RESPONSE'], 
            'TXT_RECOVERY_RESPONSE' => $this->locale['TXT_RECOVERY_RESPONSE']
        ];

        $tpl->setVars( $tplVars );
        $html = $tpl->parse();

        return $html;
    }

    private function showErrorPage() {
        header('Location: ' . _CLIENT_URL_ . $this->content['language']['iso'] . '/' . $this->content['location']['region']['url'] . '/error404/');
    }
}

?>