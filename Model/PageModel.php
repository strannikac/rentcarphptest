<?php 

namespace Model;

use Model\Model;

/**
 * Class PageModel
 * This model contains methods for site pages
 */
class PageModel extends Model {
    private $tableLangs = 'site_pages_langs';

	public function __construct() {
		parent::__construct();

		$this->table = 'site_pages';
	}

    public function getMainMenu(int $lang) {
        $sql = 'SELECT p.id, p.url, pl.title, pl.content, pl.meta_title, pl.meta_description, pl.meta_keywords, pl.menu_title 
            FROM `' . $this->table . '` p 
            LEFT JOIN `' . $this->tableLangs . '` pl ON (pl.id = p.id AND pl.language_id = ' . $lang . ') 
            WHERE p.parent_id = 0 AND p.in_menu = 1 AND p.status_id = 1 
            ORDER BY p.pos';

        return $this->list($sql);
    }

    public function getByUri(String $uri, int $lang) {
        $sql = 'SELECT p.id, pl.title, pl.content, pl.meta_title, pl.meta_description, pl.meta_keywords, pl.menu_title 
            FROM `' . $this->table . '` p 
            LEFT JOIN `' . $this->tableLangs . '` pl ON (pl.id = p.id AND pl.language_id = ' . $lang . ') 
            WHERE p.status_id = 1 AND p.url = \'' . strtolower($uri) . '\'';

        return $this->getRow($sql);
    }
}
?>