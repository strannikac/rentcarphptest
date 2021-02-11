<?php 

namespace Model;

use Model\Model;

/**
 * Class CustomerModel
 * This model contains methods for customers
 */
class CustomerModel extends Model {

	private $tableTokens = 'customers_tokens';
	private $tableRoles = 'customers_roles';
	private $tableRolesLangs = 'customers_roles_langs';

	public function __construct() {
		parent::__construct();

		$this->table = 'customers';
	}

	public function find(String $searchStr) {
		$searchStr = strip_tags($searchStr);
		$searchStr = addslashes($searchStr);

		$sql = 'SELECT * FROM `' . $this->table . '`
            WHERE status_id != 4 AND (email LIKE \'%' . $searchStr . '%\' 
            	OR phone LIKE \'%' . $searchStr . '%\' 
            	OR lname LIKE \'%' . $searchStr . '%\' 
            	OR fname LIKE \'%' . $searchStr . '%\')';

        $this->db->querySql($sql);
        $res = $this->db->getAll();

        if( count( $res ) < 1 ) {
            return false;
        }

        return $res;
	}

    /**
     * check token and customer id
     * @params id, token
     * @return bool
     */
    public function checkToken($id, $token) {
        $now = time();

        if( empty($id) || empty( $token ) ) {
            return false;
        }

        $sql = 'SELECT * FROM `' . $this->tableTokens . '`
            WHERE customer_id = \'' . $id . '\' AND type_id = 1 AND token = \'' . $token . '\'';
        //echo $sql . ' / ';

        $this->db->querySql($sql);
        $row = $this->db->getRow();

        if( empty( $row['customer_id'] ) || ($now - $row['date']) > TOKEN_LIFETIME ) {
            return false;
        }

        $this->updateToken($id);

        return true;
    }

    /**
     * update token for customer
     * @params id, token
     * @return void
     */
    private function updateToken($id, String $token = '') {
        $now = time();

        $sql = 'UPDATE `' . $this->tableTokens . '`
            SET `date` = ' . $now;

        if( $token != '' ) {
        	$sql .= ', token = \'' . $token . '\'';
        }

        $sql .= ' WHERE customer_id = \'' . $id . '\' AND type_id = 1';
        $this->db->execSql($sql);
    }

    /**
     * get token for customer
     * @param id
     * @return token
     */
    public function getToken($id) {
        $now = time();

        $sql = 'SELECT * FROM `' . $this->tableTokens . '`
            WHERE customer_id = \'' . $id . '\' AND type_id = 1';

        $this->db->querySql($sql);
        $row = $this->db->getRow();

        $token = substr(md5(mt_rand()), 0, 8)."-".substr(md5(mt_rand()), 0, 8)."-".substr(md5(mt_rand()), 0, 8)."-".substr(md5(mt_rand()), 0, 8);

        if( empty( $row['customer_id'] ) ) {
            $sql = 'INSERT INTO `' . $this->tableTokens . '`
                (customer_id, type_id, token, `date`)
                VALUES (
                    \'' . $id . '\',
                    1, 
                    \'' . $token . '\',
                    \'' . $now . '\'
                )';
            $this->db->execSql($sql);
        } else {
            $this->updateToken($id, $token);
        }

        return $token;
    }

    /**
     * get recovery token for customer
     * @param id
     * @return token
     */
    public function setRecoveryToken($id) {
        $now = time();

        $token = substr(md5(mt_rand()), 0, 8)."-".substr(md5(mt_rand()), 0, 8)."-".substr(md5(mt_rand()), 0, 8)."-".substr(md5(mt_rand()), 0, 8);

        $sql = 'INSERT INTO `' . $this->tableTokens . '`
            (customer_id, type_id, token, `date`)
            VALUES (
                \'' . $id . '\',
                2, 
                \'' . $token . '\',
                \'' . $now . '\'
            )';
        $this->db->execSql($sql);

        return $token;
    }

    /**
     * get recovery token for customer
     * @param id
     * @return token
     */
    public function getRecoveryToken(int $id) {
        $sql = 'SELECT * FROM `' . $this->tableTokens . '`
            WHERE customer_id = ' . $id . ' AND type_id = 2';

        return $this->getRow($sql);
    }

    public function removeRecoveryToken(int $id) {
        $sql = 'DELETE FROM `' . $this->tableTokens . '`
            WHERE customer_id = ' . $id . ' AND type_id = 2';

        return $this->db->execSql($sql);
    }

    /**
     * save confirmation code for customer
     * @params id, code
     */
    public function saveConfirmationToken(int $id, $token) {
        $now = time();

        $sql = 'INSERT INTO `' . $this->tableTokens . '`
            (customer_id, type_id, token, `date`)
            VALUES (
                \'' . $id . '\',
                3, 
                \'' . $token . '\',
                \'' . $now . '\'
            )';
        $this->db->execSql($sql);
    }

    /**
     * get confirmation token for customer
     * @param id
     * @return token
     */
    public function getByConfirmationCode(String $code) {
        $sql = 'SELECT * FROM `' . $this->tableTokens . '`
            WHERE type_id = 3 AND token = \'' . $code . '\'';

        return $this->getRow($sql);
    }

    /**
     * get confirmation token for customer
     * @param id
     * @return token
     */
    public function getByRecoveryToken(String $token) {
        $sql = 'SELECT * FROM `' . $this->tableTokens . '`
            WHERE type_id = 2 AND token = \'' . $token . '\'';

        return $this->getRow($sql);
    }
}
?>