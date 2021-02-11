<?php 

namespace Model;

use Service\Db;
use Helper\System;

/**
 * Class Model
 * This common model
 * contains common methods for all models
 */
abstract class Model {
	protected $db;
	protected $table = '';

	public function __construct() {
		$this->db = Db::getInstance();
	}

	//create table
    public function createTable(String $table = '', array $fields) {
    	if( $table == '' ) {
			$table = $this->table;
		}

		if( count($fields) > 0 ) {
			$sql = 'CREATE TABLE IF NOT EXISTS ' . $table . ' (' . implode(', ', $fields) . ')';
			$this->db->execSql($sql);
		}
    }

	//get list (all rows)
	public function list(String $sql) {
		$this->db->querySql($sql);
        $res = $this->db->getAll();

        if( count( $res ) < 1 ) {
            return false;
        }

        return $res;
	}

	//get by id
	public function getById($id, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$sql = 'SELECT * FROM `' . $table . '`
            WHERE id = \'' . $id . '\'';

        $this->db->querySql($sql);
        $row = $this->db->getRow();

        if( empty( $row['id'] ) ) {
            return false;
        }

        return $row;
	}

	//get by any field
	public function getByField(String $field, String $value, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$sql = 'SELECT * FROM `' . $table . '`
            WHERE `' . $field . '` = \'' . $value . '\'';

        $this->db->querySql($sql);
        return $this->db->getRow();
	}

	//get by id
	public function getRow(String $sql) {
		$this->db->querySql($sql);
        $row = $this->db->getRow();

        if( empty( $row ) ) {
            return false;
        }

        return $row;
	}

	//save some data only
	public function update(array $arr, $id, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$setFields = '';

		foreach($arr as $fld => $val) {
			if($setFields != '') {
				$setFields .= ', ';
			}
			$setFields .= '`' . $fld . '` = \'' . $val . '\'';
		}

		$sql = 'UPDATE `' . $table . '` 
			SET ' . $setFields . ' 
            WHERE id = \'' . $id . '\'';

        if( $this->db->execSql($sql) ) {
			return true;
		}

		return false;
	}

	public function updateBy(array $arr, String $where, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$setFields = '';

		foreach($arr as $fld => $val) {
			if($setFields != '') {
				$setFields .= ', ';
			}
			$setFields .= '`' . $fld . '` = \'' . $val . '\'';
		}

		$sql = 'UPDATE `' . $table . '` 
			SET ' . $setFields . ' 
            WHERE ' . $where;

        if( $this->db->execSql($sql) ) {
			return true;
		}

		return false;
	}

	//add row in table 
	public function insert(array $arr, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$fields = '';
		$values = '';

		foreach($arr as $fld => $val) {
			if($fields != '') {
				$fields .= ', ';
				$values .= ', ';
			}
			$fields .= '`' . $fld . '`';
			$values .= '\'' . $val . '\'';
		}

		$sql = 'INSERT INTO `' . $table . '` 
			(' . $fields . ') 
            VALUES (' . $values . ')';

        if( $this->db->execSql($sql) ) {
			return $this->db->getLastId();
		}

		return false;
	}

	//check unique field
	public function isUniqueValue(String $field, $value, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$sql = 'SELECT COUNT(*) AS count FROM `' . $table . '` 
			WHERE `' . $field . '` = \'' . $value . '\'';

		$this->db->querySql($sql);
        $row = $this->db->getRow();

        if( $row['count'] > 0 ) {
			return false;
		}

		return true;
	}

	//delete row from table 
	public function delete($id, String $table = '') {
		if( $table == '' ) {
			$table = $this->table;
		}

		$sql = 'DELETE FROM `' . $table . '` 
			WHERE id = \'' . $id . '\'';

        if( $this->db->execSql($sql) ) {
			return true;
		}

		return false;
	}

    protected function configureTree(array $items) {
        $items = System::getChildren($items);
		return $items;
	}
	
	protected function setFullPath(array $items, array $fields = ['id', 'parent_id', 'path', 'title']) {
		$arr = [];
		$count = count( $items );
		$path = '';

        for($i = 0; $i < $count; $i++) {
			$arr[$items[$i][$fields[0]]] = $items[$i];
            if( $items[$i][$fields[1]] > 0 ) {
				$path = $arr[$items[$i][$fields[1]]][$fields[2]] . ', ' . $arr[$items[$i][$fields[0]]][$fields[3]];
				$arr[$items[$i][$fields[0]]][$fields[2]] = $path;
				
            } else {
				$path = $arr[$items[$i][$fields[0]]][$fields[3]];
				$arr[$items[$i][$fields[0]]][$fields[2]] = $path;
            }
        }

        return $arr;
    }
}
?>