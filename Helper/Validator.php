<?php

namespace Helper;

class Validator
{
    /**
     * Removes extra spaces and html tags from the string.
     * 
     * @param mixed $value
     * 
     * @return string
     */
    public function clean($value)
    {
        if (is_string($value)) {
            $value = preg_replace('/\s+/', ' ', $value);
            $value = trim($value);
            $value = strip_tags($value);
        }
        
        return $value;
    }

    /**
     * Checks whether the value is String.
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    public function isString($value)
    {
        return is_string($value);
    }

    /**
     * Checks whether the value is Positive Integer.
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    public function isPositiveInteger($value)
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }
        return (bool) preg_match_all('/^[\d]{1,10}+$/', $value);
    }
    
    /**
     * Checks whether the value is Integer.
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    public function isInteger($value)
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }
        return (bool) preg_match_all('/^[-]{0,1}[\d]+$/', $value);
    }

    /**
     *  Checks whether the value is Float.
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    public function isFloat($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return false;
        }
        return (bool) preg_match_all('/^[-]{0,1}[\d]+\.[\d]+$|^[-]{0,1}[\d]+$/', $value);
    }

    /**
     *  Checks whether the value is Float.
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    public function isPrice($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return false;
        }
        return (bool) preg_match_all('/^([0-9]{1,13})$|^([0-9]{1,13}\.[0-9]{1,2})$/', $value);
        //[0-9]+(\.[0-9]{2})?
    }

    /**
     * Checks whether the value is not empty.
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    public function required($value)
    {
        return !(is_null($value) || ($value === ''));
    }

    /**
     * Checks whether the values are equal each other.
     * 
     * @param mixed $value
     * @param mixed $confirmWith
     * 
     * @return bool
     */
    public function confirm($value, $confirmWith = '')
    {
        return ($value === $confirmWith);
    }
    
    /**
     * Checks whether the values are equal each other.
     * 
     * @param mixed $value
     * @param mixed $confirmWith
     * @param bool $caseSensitive
     * 
     * @return bool
     */
    public function notSame($value, $confirmWith, $caseSensitive = false)
    {
        if (!is_string($value) || !is_string($confirmWith)) {
            return false;
        }
        if ($caseSensitive === true) {
            return !($value === $confirmWith);
        }      
        return !(strtolower($value) === strtolower($confirmWith));
    }
    
    /**
     * Checks whether the length of the string value is in the needed diapason.
     * 
     * @param string $value
     * @param null $min
     * @param null $max
     * 
     * @return bool
     */
    public function lengthLimits($value, $min = null, $max = null)
    {
        if (!is_string($value) || (!is_null($min) && (mb_strlen($value) < (int)$min)) || (!is_null($max) && (mb_strlen($value) > (int)$max))) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks whether the length of the email paths is in the needed diapason.
     * 
     * @param string $value
     * @param int $localLength
     * @param int $networkLength
     * 
     * @return bool
     */
    public function emailPathsLengthChecking($value, $localLength, $networkLength)
    {
        if (!is_int($localLength) || !is_int($networkLength) || !is_string($value)) {
            return false;
        }
        $emailPath = explode('@', $value);
        if ((mb_strlen($emailPath[0]) > (int) $localLength) || (mb_strlen($emailPath[1]) > (int) $networkLength)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Convert string to number
     * 
     * @param float|int|string $value
     * 
     * @return bool
     */
    public function convertToNumber($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $value = false;
        } elseif (preg_match_all('/^[-]{0,1}[\d]+\.[\d]+$/', $value)) {
            $value = (float) $value;
        } elseif (preg_match_all('/^[-]{0,1}[\d]+$/', $value)) {
            $value = (int) $value;
        } else {
            $value = false;
        }
        return $value;
    }
    
    /**
     * Checks whether the numeric value is in the needed diapason.
     * 
     * @param float|int $value
     * @param float|int $min
     * @param float|int $max
     * 
     * @return bool
     */
    public function minMaxLimits($value, $min, $max)
    {
        if (!is_int($min) || !is_int($max) || !isset($value)) {
            return false;
        }
        $value = $this->convertToNumber($value);
        if ($value === false || $value < $min || $value > $max) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks whether the numeric value is greater than min value
     * 
     * @param float|int $value
     * @param float|int $min
     * 
     * @return bool
     */
    public function minLimit($value, $min)
    {
        if (!is_int($min) || !isset($value)) {
            return false;
        }
        $value = $this->convertToNumber($value);
        if ($value === false || $value < $min) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks whether the numeric value is less than max value
     * 
     * @param float|int $value
     * @param float|int $max
     * 
     * @return bool
     */
    public function maxLimit($value, $max)
    {
        if (!is_int($max) || !isset($value)) {
            return false;
        }
        $value = $this->convertToNumber($value);

        if ($value === false || $value > $max) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks whether the value is an URL.
     * 
     * @param string $value
     * 
     * @return bool
     */
    public function isURL($url)
    {
        $pattern = "/^(https?:\/\/)?(www\.)?([-a-z0-9_\.]{2,}\.)([a-z]{2,6})((\/[-a-z0-9_]{1,})?\/?([a-z0-9_-]{2,}\.[a-z]{2,6})?(\?[a-z0-9_]{2,}=[-0-9]{1,})?((\&[a-z0-9_]{2,}=[-0-9]{1,}){1,})?)/i";
        if (!is_string($url)) {
            $result = false;
        } else {
            $result = (boolean) preg_match_all($pattern, $url, $matches); 
        }
        
        return $result; 
    }

    /**
     * Checks whether the value is an Email.
     * 
     * @param string $value
     * 
     * @return bool
     */
    public function isEmail($value)
    {
        return (boolean) filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Checks whether the value is an IP.
     * 
     * @param string $value
     * 
     * @return bool
     */
    public function isIP($value)
    {
        return (boolean) filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * Checks whether the age is greater than age limit.
     * 
     * @param string $date
     * @param string $format
     * @param int $ageLimit
     * 
     * @return bool
     */
    public function CheckAgeLimit($date, $format, $ageLimit)
    {
        if ($this->checkDateTime($date, $format)) {
             $ageLimitTime = strtotime("- ". $ageLimit . " year");
             return (strtotime($date) <= $ageLimitTime);
        }
        return false;
    }
    /**
     * Checks whether the date matches the specified format.
     * 
     * @param string|int $date
     * @param string $format
     * 
     * @return bool
     */
    public function checkDateTime($date, $format)
    {
        return date($format, is_string($date) ? strtotime($date) : $date) == $date;
    }

    /**
     * Checks whether the value matches the specified pattern.
     * 
     * @param string $value
     * @param $pattern
     * 
     * @return bool
     */
    public function regex($value, $pattern)
    {
        return (boolean) preg_match_all('~' . $pattern . '~is', $value, $matches);
    }
}