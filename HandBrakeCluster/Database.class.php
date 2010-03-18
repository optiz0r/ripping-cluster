<?php

class HandBrakeCluster_Database {

    private $config;
    private $dbh;

    private $hostname;
    private $username;
    private $password;
    private $dbname;

    private $prepared_statements = array();

    public function __construct(HandBrakeCluster_Config $config) {
        $this->config   = $config;

        $this->hostname = $this->config->getDatabase('hostname');
        $this->username = $this->config->getDatabase('username');
        $this->password = $this->config->getDatabase('password');
        $this->dbname   = $this->config->getDatabase('dbname');

        try {
            $this->dbh  = new PDO("mysql:host={$this->hostname};dbname={$this->dbname}", $this->username, $this->password);
        } catch (PDOException $e) {
            throw new HandBrakeCluster_Exception_DatabaseConnectionFailed($e->getMessage());
        }

    }

    public function __destruct() {
        $this->dbh = null;
    }

    public function selectAssoc($sql, $key_col, $value_col) {
        $results = array();

        foreach ($this->dbh->query($sql) as $row) {
            $results[$row[$key_col]] = $row[$value_col];
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
				throw new HandBrakeCluster_Exception_DatabaseQueryFailed();
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
			throw new HandBrakeCluster_Exception_ResultCountMismatch(count($rows));
		}

		return $rows[0];
	}

    public function insert($sql, $bind_params = null) {
        $stmt = $this->dbh->prepare($sql);

        if ($bind_params) {
            foreach ($bind_params as $param) {
                $stmt->bindValue(':'.$param['name'], $param['value'], $param['type']);
            }
        }

        return $stmt->execute();
    }

    public function errorInfo() {
        return $this->dbh->errorInfo();
    }

}

?>
