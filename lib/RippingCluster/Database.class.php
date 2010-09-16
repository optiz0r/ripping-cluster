<?php

class RippingCluster_Database {

    private $config;
    private $dbh;

    private $hostname;
    private $username;
    private $password;
    private $dbname;

    private $prepared_statements = array();

    public function __construct(RippingCluster_Config $config) {
        $this->config   = $config;

        $this->hostname = $this->config->getDatabase('hostname');
        $this->username = $this->config->getDatabase('username');
        $this->password = $this->config->getDatabase('password');
        $this->dbname   = $this->config->getDatabase('dbname');

        try {
            $this->dbh  = new PDO("mysql:host={$this->hostname};dbname={$this->dbname}", $this->username, $this->password);
        } catch (PDOException $e) {
            throw new RippingCluster_Exception_DatabaseConnectionFailed($e->getMessage());
        }

    }

    public function __destruct() {
        $this->dbh = null;
    }

    public function selectAssoc($sql, $key_col, $value_cols) {
        $results = array();

        foreach ($this->dbh->query($sql) as $row) {
            if (is_array($value_cols)) {
                $values = array();
                foreach ($value_cols as $value_col) {
                    $values[$value_col] = $row[$value_col];
                }
                
                $results[$row[$key_col]] = $values;
            } else {
                $results[$row[$key_col]] = $row[$value_col];
            }
        }

        return $results;
    }

	public function selectList($sql, $bind_params = null) {
		if ($bind_params) {
	        $stmt = $this->dbh->prepare($sql);

            foreach ($bind_params as $param) {
                $stmt->bindValue(':'.$param['name'], $param['value'], $param['type']);
            }

			$result = $stmt->execute();
			if (!$result) {
                list($dummy, $code, $message) = $stmt->errorInfo();
                throw new RippingCluster_Exception_DatabaseQueryFailed($message, $code);
			}

			return $stmt->fetchAll();

		} else {
			$results = array();

			$result = $this->dbh->query($sql);
			foreach ($result as $row) {
				$results[] = $row;
			}

			return $results;
		}
	}

	public function selectOne($sql, $bind_params = null) {
		$rows = $this->selectList($sql, $bind_params);
		if (count($rows) != 1) {
			throw new RippingCluster_Exception_ResultCountMismatch(count($rows));
		}

		return $rows[0];
	}

    public function insert($sql, $bind_params = null) {
        $stmt = $this->dbh->prepare($sql);

        if ($bind_params) {
            foreach ($bind_params as $param) {
                if (isset($param['type'])) {
                    $stmt->bindValue(':'.$param['name'], $param['value'], $param['type']);
                } else {
                    $stmt->bindValue(':'.$param['name'], $param['value']);
                }
            }
        }

        $result = $stmt->execute();
        if (!$result) {
            list($code, $dummy, $message) = $stmt->errorInfo();
            throw new RippingCluster_Exception_DatabaseQueryFailed($message, $code);
        }
    }
    
    public function update($sql, $bind_params = null) {
        $stmt = $this->dbh->prepare($sql);

        if ($bind_params) {
            foreach ($bind_params as $param) {
                if (isset($param['type'])) {
                    $stmt->bindValue(':'.$param['name'], $param['value'], $param['type']);
                } else {
                    $stmt->bindValue(':'.$param['name'], $param['value']);
                }
            }
        }

        $result = $stmt->execute();
        if (!$result) {
            list($code, $dummy, $message) = $stmt->errorInfo();
            throw new RippingCluster_Exception_DatabaseQueryFailed($message, $code);
        }
    }

    public function errorInfo() {
        return $this->dbh->errorInfo();
    }
    
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

}

?>
