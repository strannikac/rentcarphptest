<?php 

namespace App;

use Helper\System;

/**
 * Class Customer
 * This controller contains methods for customer 
 * registration, login, forgot, recovery
 */
class CustomerController extends Controller {

    /**
     * Method main (default)
     * @param void
     */
    public function main() {
        $this->login();
    }

    /**
     * Method country.
     * @param id
     * @return regions in country or false
     */
    public function country() {
        $method = mb_strtoupper($_SERVER['REQUEST_METHOD']);

        if (empty($method) || !in_array($method, $this->allowedMethods)) {
            $this->response['error'][] = $this->locale['ERR_4001'];
            return false;
        }

        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $country = $this->locationModel->getCountryById($this->params['object'], $this->language['id']);
        if(!$country) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $regions = $this->locationModel->getRegionByCountryId($country['id'], $this->language['id']);

        if($regions) {
            $this->response['status'] = $this->statusSuccess;
            $this->response['regions'] = $regions;
            $this->response['country'] = $country;
        } else {
            $this->response['error'][] = $this->locale['SUCCESS_9004'];
        }
    }

    /**
     * Method login.
     * @param data for login
     * @return success or error
     */
    public function login() {
        if(isset($this->params['post']['login'])) {
            $this->params['post']['login'] = strip_tags(trim($this->params['post']['login']));
        }

        if(isset($this->params['post']['password'])) {
            $this->params['post']['password'] = strip_tags(trim($this->params['post']['password']));
        }

        if( empty( $this->params['post']['login'] ) || empty( $this->params['post']['password'] )
            || !$this->validator->isEmail( $this->params['post']['login'] )
        ) {
            $this->response['error'][] = $this->locale['ERR_3000'];
            return;
        }

        $now = time();
        $row = $this->customerModel->getByField('email', $this->params['post']['login']);

        if( empty($row['id']) ) {
            $this->response['error'][] = $this->locale['ERR_3000'];
        } else if( $row['password'] != $this->params['post']['password'] ) {
            $arr = ['incorrect_password_count' => ($row['incorrect_password_count'] + 1)];
            $this->customerModel->update($arr, $row['id']);

            $this->response['error'][] = $this->locale['ERR_3000'];
        } else if( $row['status_id'] != 1 ) {
            $this->response['error'][] = $this->locale['ERR_3002'];
        } else if( $row['incorrect_password_count'] > 2 ) {
            $this->response['error'][] = $this->locale['ERR_3001'];
        } else if( $row['confirmed_email'] != 1 ) {
            $this->response['error'][] = $this->locale['ERR_2017'];
        } else {
            $token = $this->customerModel->getToken($row['id']);

            $arr = [
                'incorrect_password_count' => 0, 
                'login_time' => $now
            ];
            $this->customerModel->update($arr, $row['id']);

            $this->response['status'] = $this->statusSuccess;
            $this->response['customerID'] = $row['id'];
            $this->response['token'] = $token;
        }
    }

    /**
     * Method forgot password.
     * @param login
     * @return success or error
     */
    public function forgot() {
        if(isset($this->params['post']['login'])) {
            $this->params['post']['login'] = strip_tags(trim($this->params['post']['login']));
        }

        if( empty( $this->params['post']['login'] ) 
            || !$this->validator->isEmail( $this->params['post']['login'] )
        ) {
            $this->response['error'][] = $this->locale['ERR_2005'];
            return;
        }

        $row = $this->customerModel->getByField('email', $this->params['post']['login']);

        if( empty($row['id']) ) {
            $this->response['error'][] = $this->locale['ERR_2005'];
        } else {
            $recovery = $this->customerModel->getRecoveryToken($row['id']);
            $token = '';

            if($recovery && !empty($recovery['token'])) {
                $token = $recovery['token'];
            } else {
                $token = $this->customerModel->setRecoveryToken($row['id']);
            }

            $msg = str_replace( '{LINK}', _CLIENT_URL_ . '?recovery=' . $token, $this->locale['TXT_FORGOT_EMAIL'] );

            System::sendMail( $row['email'], $this->locale['HDR_FORGOT_EMAIL'] . ' ' . _CLIENT_URL_, $msg );

            $this->response['status'] = $this->statusSuccess;
        }
    }

    /**
     * Method recovery password.
     * @param id and recovery_token
     * @return success or error
     */
    public function recovery() {
        $now = time();

        if(isset($this->params['post']['password'])) {
            $this->params['post']['password'] = strip_tags($this->params['post']['password']);
        }

        if(isset($this->params['post']['token'])) {
            $this->params['post']['token'] = strip_tags($this->params['post']['token']);
        }

        if( empty( $this->params['post']['password'] ) ) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return;
        }

        $row = $this->customerModel->getByRecoveryToken($this->params['post']['token']);

        if( empty($row['customer_id']) || ($now - $row['date']) > RECOVERY_TOKEN_LIFETIME ) {
            $this->response['error'][] = $this->locale['ERR_1003'];
        } else {
            $this->customerModel->update(['password' => $this->params['post']['password']], $row['customer_id']);
            $this->customerModel->removeRecoveryToken($row['customer_id']);
            $this->response['status'] = $this->statusSuccess;
        }
    }

    /**
     * Method registration.
     * @param data for registration
     * @return success or error
     */
    public function registration() {
        $err = [];

        $method = mb_strtoupper($_SERVER['REQUEST_METHOD']);

        if (empty($method) || !in_array($method, $this->allowedMethods)) {
            $this->response['error'][] = $this->locale['ERR_4001'];
            return false;
        }

        if(isset($this->params['post']['email'])) {
            $this->params['post']['email'] = strip_tags(trim($this->params['post']['email']));
        }

        if(isset($this->params['post']['password'])) {
            $this->params['post']['password'] = strip_tags($this->params['post']['password']);
        }

        if( empty( $this->params['post']['email'] ) || !$this->validator->isEmail( $this->params['post']['email'] ) 
            || empty( $this->params['post']['password'] ) 
            || (!empty( $this->params['post']['country'] ) && !$this->validator->isPositiveInteger( $this->params['post']['country'] )) 
            || (!empty( $this->params['post']['region'] ) && !$this->validator->isPositiveInteger( $this->params['post']['region'] )) 
        ) {
            $this->response['error'][] = $this->locale['ERR_4002'];
        } else {
            if(!$this->customerModel->isUniqueValue('email', $this->params['post']['email'])) {
                $err[] = $this->locale['ERR_2015'];
            } else {
                $now = time();
                $arr = [
                    'language_id' => $this->language['id'], 
                    'email' => $this->params['post']['email'], 
                    'password' => $this->params['post']['password'], 
                    'creation_time' => $now, 
                    'update_time' => $now
                ];

                if(isset( $this->params['post']['country'] ) ) {
                    $arr['country_id'] = $this->params['post']['country'];
                }

                if(isset( $this->params['post']['region'] ) ) {
                    $arr['region_id'] = $this->params['post']['region'];
                }

                $newId = $this->customerModel->insert($arr);

                if ($newId) {
                    $code = System::getConfirmationCode($newId);
                    $this->customerModel->saveConfirmationToken($newId, $code);

                    $url = _CLIENT_URL_ . '?code=' . $code;
                    $msg = str_replace( ['{SITE}', '{URL}', '{CODE}'], [_CLIENT_URL_, $url, $code], $this->locale['TXT_SIGNUP_EMAIL'] );

                    System::sendMail($this->params['post']['email'], $this->locale['HDR_SIGNUP_EMAIL'] . ' ' . _CLIENT_URL_, $msg);

                    $this->response['status'] = $this->statusSuccess;
                } else {
                    $err[] = $this->locale['ERR_1008'];
                }
            }
        }

        $this->response['error'] = $err;
    }

    /**
     * Method confirmation of registration.
     * @param code
     * @return success or error
     */
    public function confirmation() {
        if( empty( $this->params['post']['code'] ) ) {
            $this->response['error']['code'] = $this->locale['ERR_2013'];
            return;
        }

        $this->params['post']['code'] = strip_tags( $this->params['post']['code'] );
        $now = time();

        $row = $this->customerModel->getByConfirmationCode($this->params['post']['code']);

        if( empty($row['customer_id']) || ($now - $row['date']) > CONFIRMATION_TOKEN_LIFETIME ) {
            $this->response['error']['code'] = $this->locale['ERR_2014'];
        } else {
            $arr = ['confirmed_email' => 1];

            if ($this->customerModel->update($arr, $row['customer_id'])) {
                $this->response['status'] = $this->statusSuccess;
            } else {
                $this->response['error'][] = $this->locale['ERR_1009'];
            }
        }
    }
}

?>