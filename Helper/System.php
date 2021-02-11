<?php 

namespace Helper;

/**
 * This helper contains some common methods for system
 * 
 * @package Helper
 */
class System {

    public static function cleanString(String $str) {
        $str = strip_tags($str);
        $str = str_replace(' ', '-', $str);
        $str = preg_replace('/[^A-Za-z0-9\-_]/', '', $str);

        return $str;
    }
    
    /**
     * get correct ip 
     * @return ip or false
     */
    public static function getIP() {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		} else if(isset ($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
			return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
		} else if(isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
        }
        
        return false;
    }

    /**
     * send email
     */
    public static function sendMail( $to, $subj, $msg ) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($to, $subj, $msg, $headers);
    }

    /**
     * get confirmation code for registration
     * @param str
     * @return code
     */
    public static function getHash($str) {
        return hash ('sha3-256', $str);
    }

    /**
     * get confirmation code
     * @param customerID
     * @return code
     */
    public static function getConfirmationCode($customerID) {
        return substr(sha1(mt_rand()), 0, 13) . $customerID . substr(sha1(mt_rand()), 0, 13);
    }

    /**
     * get session token
     * @return token
     */
    public static function setSessionToken() {
        $now = time();
        $token = substr(sha1(mt_rand()), 0, 13) . $now . substr(sha1(mt_rand()), 0, 13);
        $options = [
            'expires' => $now + 60 * 60 * 24, 
            'path' => '/', 
            'domain' => _DOMAIN_, 
            'samesite' => 'None', 
            'secure' => true 

        ];

        setcookie( 'sessionToken', $token, $options );
        return $token;
    }

    public static function getChildren(array $arr, String $parentField = 'parent_id', int $parentId = 0, int $level = 0, array $children = []) {
        $count = count( $arr );

        for($i = 0; $i < $count; $i++) {
            if( $arr[$i][$parentField] == $parentId ) {
                $children[] = $arr[$i];
                $children = self::getChildren( $arr, $parentField, $arr[$i]['id'], $level + 1, $children );
            }
        }
        
        if( empty( $children ) ) {
            return [];
        }

        return $children;
    }

    public static function getHolidays(int $days, \DateTime $startDate) {
        $holidays = [];

        for($i = $days; $i > 0; $i--) {
            if( $i < $days ) {
                $startDate->add(new \DateInterval('P1D'));
            }

            $weekday = date('N', strtotime($startDate->format('Y-m-d')));

            if($weekday == 6 || $weekday == 7) {
                $holidays[] = $startDate->format('Y-m-d');
            }

            //TODO: also add holidays for current location (country)
        }

        return $holidays;
    }
}

?>