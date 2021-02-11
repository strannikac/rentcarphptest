<?php 

namespace Service;

class GeoLocation {

    private $apiUrl = 'http://www.geoplugin.net/php.gp';
    
    public function getByIP($ip) {
        $location = unserialize(file_get_contents($this->apiUrl . '?ip=' . $ip));
        
        return [
            'country' => $location['geoplugin_countryName'], 
            'countryIso' => $location['geoplugin_countryCode'], 
            'region' => $location['geoplugin_region'], 
            'city' => $location['geoplugin_city']
        ];
    }
}

?>