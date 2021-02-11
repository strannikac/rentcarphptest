<?php 

namespace Model;

use Model\Model;

/**
 * Class LocationModel
 * This model contains methods for regions and countries
 */
class LocationModel extends Model {
    private $tableCountries = 'countries';
    private $tableCountriesLangs = 'countries_langs';
    private $tableLangs = 'regions_langs';

	public function __construct() {
		parent::__construct();

		$this->table = 'regions';
	}

    public function getCountries(int $lang) {
        $sql = 'SELECT c.id, c.iso, c.iso2, cl.title 
            FROM `' . $this->tableCountries . '` c 
            LEFT JOIN `' . $this->tableCountriesLangs . '` cl ON (cl.country_id = c.id AND cl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 
            ORDER BY c.pos';

        return $this->list($sql);
    }

    public function getRegionByCountryId(int $id, int $lang) {
        $sql = 'SELECT r.id, r.parent_id, rl.title, r.`url` 
            FROM `' . $this->table . '` r 
            LEFT JOIN `' . $this->tableLangs . '` rl ON (rl.region_id = r.id AND rl.language_id = ' . $lang . ') 
            WHERE r.status_id = 1 AND r.country_id = ' . $id . ' 
            ORDER BY r.parent_id, r.pos';

        $rows = $this->list($sql);
        if($rows) {
            $rows = $this->configureTree($rows);
            $rows = $this->setFullPath($rows);

            return $rows;
        }
        
        return false;
    }

    public function getCountryById(int $id, int $lang) {
        $sql = 'SELECT c.id, c.iso, c.iso2, cl.title 
            FROM `' . $this->tableCountries . '` c 
            LEFT JOIN `' . $this->tableCountriesLangs . '` cl ON (cl.country_id = c.id AND cl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 AND c.id = ' . $id;

        return $this->getRow($sql);
    }

    public function getRegionById(int $id, int $lang) {
        $sql = 'SELECT r.id, rl.title, r.`url` 
            FROM `' . $this->table . '` r 
            LEFT JOIN `' . $this->tableLangs . '` rl ON (rl.region_id = r.id AND rl.language_id = ' . $lang . ') 
            WHERE r.status_id = 1 AND r.id = ' . $id;

        return $this->getRow($sql);
    }

    public function getCountryByIso(String $iso, int $lang) {
        $sql = 'SELECT c.id, c.iso, c.iso2, cl.title 
            FROM `' . $this->tableCountries . '` c 
            LEFT JOIN `' . $this->tableCountriesLangs . '` cl ON (cl.country_id = c.id AND cl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 AND c.iso2 = \'' . strtolower($iso) . '\'';

        return $this->getRow($sql);
    }

    public function getRegionByName(String $name, int $lang) {
        $sql = 'SELECT r.id, r.parent_id, rl.title, r.`url` 
            FROM `' . $this->table . '` r 
            LEFT JOIN `' . $this->tableLangs . '` rl ON (rl.region_id = r.id AND rl.language_id = ' . $lang . ') 
            WHERE r.status_id = 1 AND r.title = \'' . $name . '\'';

        return $this->getRow($sql);
    }

    public function getByUri(String $uri, int $lang) {
        $sql = 'SELECT c.id, c.iso, c.iso2, cl.title, r.id AS regionId, r.parent_id AS regionParentId, rl.title AS regionTitle, r.`url` 
            FROM `' . $this->table . '` r 
            LEFT JOIN `' . $this->tableLangs . '` rl ON (rl.region_id = r.id AND rl.language_id = ' . $lang . ') 
            LEFT JOIN `' . $this->tableCountries . '` c ON (c.id = r.country_id) 
            LEFT JOIN `' . $this->tableCountriesLangs . '` cl ON (cl.country_id = c.id AND cl.language_id = ' . $lang . ') 
            WHERE c.status_id = 1 AND r.status_id = 1 AND r.`url` = \'' . $uri . '\'';

        return $this->getRow($sql);
    }

    public function getParents($parent, $level = 0, $parents = []) {
        if( $parent > 0 ) {
            $sql = 'SELECT id, parent_id FROM `' . $this->table . '`
                WHERE id = ' . $parent;
            $arr = [];

            $rows = $this->list($sql);
            
            if($rows) {
                $count = count($rows);
                for($i = 0; $i < $count; $i++) {
                    if( !empty($rows[$i]['id']) ) {
                        $parents[] = $rows[$i]['id'];
                        $arr[] = $rows[$i]['parent_id'];
                    }
                }
            }

            $count = count($arr);
            for($i = 0; $i < $count; $i++) {
                $parents = $this->getParents( $arr[$i], $level + 1, $parents );
            }
        }
        
        if( empty( $parents ) ) {
            return [];
        }

        return $parents;
    }

    public function getChildren($parent, $level = 0, $children = []) {
        if( $parent > 0 ) {
            $sql = 'SELECT id FROM `' . $this->table . '`
                WHERE parent_id = ' . $parent;
            $arr = [];

            $rows = $this->list($sql);
            
            if($rows) {
                $count = count($rows);
                for($i = 0; $i < $count; $i++) {
                    if( !empty($rows[$i]['id']) ) {
                        $children[] = $rows[$i]['id'];
                        $arr[] = $rows[$i]['id'];
                    }
                }
            }

            $count = count($arr);
            for($i = 0; $i < $count; $i++) {
                $children = $this->getChildren( $arr[$i], $level + 1, $children );
            }
        }
        
        if( empty( $children ) ) {
            return [];
        }

        return $children;
    }
}
?>