<?php 

namespace App;

use Model\LanguageModel;

/**
 * Class Profile
 * This controller contains methods for profile (cabinet)
 * lang (save lang), save (main data), password (change password)
 */
class ProfileController extends Controller {

    private $minIdLen = 10;
    private $maxIdLen = 15;

    private $minNameLen = 2;
    private $maxNameLen = 75;

    private $minPhoneLen = 7;
    private $maxPhoneLen = 35;

    private $tplPath = _ROOT_TPL_ . 'profile/';

    /**
     * Method main (default) - show console for cabinet
     * @param void
     */
    public function main() {
        $this->content['html'] = '';

        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        $currencies = '';
        $count = count($this->currencies);
        for($i = 0; $i < $count; $i++) {
            $currencies .= '<option value="' . $this->currencies[$i]['id'] . '"' . ($this->currency['id'] == $this->currencies[$i]['id'] ? ' selected="selected"' : '') . '>' . strtoupper($this->currencies[$i]['iso']) . ', ' . $this->currencies[$i]['sign'] . '</option>';
        }

        $this->template->set( $this->tplPath . 'console.html');

        $tplVars = [
            'LINK_PROFILE' => $this->links['profile'],
            'LINK_ORDERS' => $this->links['orders'], 
            'STR_MY_PROFILE' => $this->locale['STR_MY_PROFILE'], 
            'STR_MY_ORDERS' => $this->locale['STR_MY_ORDERS'], 
            'HDR_PROFILE' => $this->locale['HDR_PROFILE'], 
            'TXT_PROFILE' => $this->locale['TXT_PROFILE'], 
            'HDR_PROFILE_DATA' => $this->locale['HDR_PROFILE_DATA'], 
            'HDR_PROFILE_PASSWORD' => $this->locale['HDR_PROFILE_PASSWORD'], 
            'STR_EMAIL' => $this->locale['STR_EMAIL'], 
            'STR_FNAME' => $this->locale['STR_FNAME'], 
            'STR_LNAME' => $this->locale['STR_LNAME'], 
            'STR_PERSONAL_CODE' => $this->locale['STR_PERSONAL_CODE'], 
            'STR_PHONE' => $this->locale['STR_PHONE'], 
            'STR_SAVE' => $this->locale['STR_SAVE'], 
            'EMAIL' => $this->customer['email'], 
            'FNAME' => $this->customer['fname'], 
            'LNAME' => $this->customer['lname'], 
            'PERSONAL_CODE' => $this->customer['personal_code'], 
            'PHONE' => $this->customer['phone'], 
            'STR_PASSWORD' => $this->locale['STR_PASSWORD'], 
            'STR_NEW_PASSWORD' => $this->locale['STR_NEW_PASSWORD'], 
            'STR_RETYPE_NEW_PASSWORD' => $this->locale['STR_RETYPE_NEW_PASSWORD'], 
            'CURRENCIES' => $currencies
        ];

        $this->template->setVars( $tplVars );

        $this->content['html'] = $this->template->parse();
        $this->response['status'] = $this->statusSuccess;
    }

    /**
     * Method set language.
     * @param 
     * @return success or error
     */
    public function lang() {
        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        if(empty($this->params['object']) || !$this->validator->isPositiveInteger($this->params['object'])) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $model = new LanguageModel();
        $row = $model->getById($this->params['object']);

        if( empty( $row['id'] ) ) {
            $this->response['error'][] = $this->locale['ERR_1005'];
            return false;
        }

        $arr = [
            'language_id' => $row['id']
        ];
        $this->customerModel->update($arr, $row['id']);

        $this->response['status'] = $this->statusSuccess;
    }

    /**
     * Method for cnanging customer data.
     * @param data for saving
     * @return success or error
     */
    public function save() {
        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        $arr = [];

        if(isset($this->params['post']['lname'])) {
            $this->params['post']['lname'] = strip_tags(trim($this->params['post']['lname']));

            if($this->customer['lname'] != $this->params['post']['lname'] && $this->validator->lengthLimits( $this->params['post']['fname'], $this->minNameLen, $this->maxNameLen )) {
                $arr['lname'] = $this->params['post']['lname'];
            }
        }

        if(isset($this->params['post']['fname'])) {
            $this->params['post']['fname'] = strip_tags(trim($this->params['post']['fname']));

            if($this->customer['fname'] != $this->params['post']['fname'] && $this->validator->lengthLimits( $this->params['post']['fname'], $this->minNameLen, $this->maxNameLen )) {
                $arr['fname'] = $this->params['post']['fname'];
            }
        }

        if(isset($this->params['post']['country'])) {
            $this->params['post']['country'] = strip_tags(trim($this->params['post']['country']));

            if($this->customer['country_id'] != $this->params['post']['country'] && $this->validator->isPositiveInteger( $this->params['post']['country'] ) ) {
                $arr['country_id'] = $this->params['post']['country'];
            }
        }

        if(isset($this->params['post']['region'])) {
            $this->params['post']['region'] = strip_tags(trim($this->params['post']['region']));

            if($this->customer['region_id'] != $this->params['post']['region'] && $this->validator->isPositiveInteger( $this->params['post']['region'] ) ) {
                $arr['region_id'] = $this->params['post']['region'];
            }
        }

        if(isset($this->params['post']['personal_code'])) {
            $this->params['post']['personal_code'] = strip_tags(trim($this->params['post']['personal_code']));

            if($this->customer['personal_code'] != $this->params['post']['personal_code'] && $this->validator->lengthLimits( $this->params['post']['personal_code'], $this->minIdLen, $this->maxIdLen )) {
                $arr['personal_code'] = $this->params['post']['personal_code'];
            }
        }

        if(isset($this->params['post']['phone'])) {
            $this->params['post']['phone'] = strip_tags(trim($this->params['post']['phone']));

            if($this->customer['phone'] != $this->params['post']['phone'] && $this->validator->lengthLimits( $this->params['post']['phone'], $this->minPhoneLen, $this->maxPhoneLen ) && $this->customerModel->isUniqueValue('phone', $this->params['post']['phone']) ) {
                $arr['phone'] = $this->params['post']['phone'];
            }
        }

        if(isset($this->params['post']['currency'])) {
            $this->params['post']['currency'] = strip_tags(trim($this->params['post']['currency']));

            if($this->customer['currency_id'] != $this->params['post']['currency'] && $this->validator->isPositiveInteger( $this->params['post']['currency'] ) ) {
                $arr['currency_id'] = $this->params['post']['currency'];
            }
        }

        if( count($arr) > 0 ) {
            if ($this->customerModel->update($arr, $this->customer['id'])) {
                $this->response['status'] = $this->statusSuccess;
                $this->response['msg'] = $this->locale['SUCCESS_9001'];
            } else {
                $this->response['error'][] = $this->locale['ERR_1009'];
            }
        } else {
            $this->response['data']['status'] = $this->statusSuccess;
        }
    }

    /**
     * Method for changing password
     * @param data for saving
     * @return success or error
     */
    public function password() {
        if(empty($this->customer['id'])) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            $this->response['logout'] = 1;
            return false;
        }

        if(isset($this->params['post']['current'])) {
            $this->params['post']['current'] = strip_tags($this->params['post']['current']);
        }

        if(isset($this->params['post']['password'])) {
            $this->params['post']['password'] = strip_tags($this->params['post']['password']);
        }

        if( empty( $this->params['post']['current'] )
            || empty( $this->params['post']['password'] ) 
        ) {
            $this->response['error'][] = $this->locale['ERR_4002'];
            return false;
        } 
        
        if($this->params['post']['current'] != $this->customer['password']) {
            $this->response['error'][] = $this->locale['ERR_2011'];
            return false;
        } 
        
        if($this->params['post']['password'] != $this->params['post']['repassword']) {
            $this->response['error'][] = $this->locale['ERR_2011'];
            return false;
        }

        $arr = ['password' => $this->params['post']['password']];

        if ($this->customerModel->update($arr, $this->customer['id'])) {
            $this->response['status'] = $this->statusSuccess;
            $this->response['msg'] = $this->locale['SUCCESS_9001'];
        } else {
            $this->response['error'][] = $this->locale['ERR_1009'];
        }
    }
}

?>