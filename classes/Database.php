<?php
	class Database {
        private $pdo;
        private $defaultOptions = array(
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

		function __construct($config, $options = NULL) {
			if ($this->connect($config, $options)) {
				$this->pdo->query('SET NAMES utf8');
			}
			else {
				$this->pdo = NULL;
			}
		}

		// Try to connect using the provided configuration
		public function connect($config, $options) {
			if (!is_array($config)) {
				$this->errorLog('Invalid config paramer given '.gettype($config).', Array expected.');
				return FALSE;
			}

			// Check if we actually have a valid config
			if(!$this->checkConfig($config)) {
				$this->errorLog('Configuration variable is not set or does not match the requirements.');
				return FALSE;
			}

			// Creating DSN object
			$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['schema']};charset={$config['charset']}";
			
			// Setting options if not present
			if($options === NULL) {
				$options = $this->defaultOptions;
			}

			// Creating PDO object
			try{
				$this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
				return TRUE;
			}
			catch (PDOException $e) {
				$this->errorLog('PDO Exception: '.$e->getCode().'. '.$e->getMessage());
				return FALSE;
			}
		}

		public function ok() {
			return ($this->pdo !== NULL);
		}

		// Execute normal query (NOT SECURE)
		public function query($sql) {
			if(!$this->ok())
                return FALSE;
            
            return $this->pdo->query(trim($sql));
		}

		// Execute prepared statement
		public function execute_prepared($sql, $params) {
            if(!$this->ok())
                return FALSE;

            $stmt = $this->pdo->prepare(trim($sql));
            if ($stmt === FALSE) {
                $this->errorLog('Failed to prepare statement: '.trim($sql).' Params: '.implode(', ', $this->params));
            }
            else { 
                $this->trimParams($params);
                $stmt->execute($params);
            }
            return $stmt;
		}

		// Checks if the config array have everything required for the connection
		private function checkConfig($config) {
			return (
				(is_array($config)) &&
				(count($config) >= 6) &&
				(isset($config['host'])) &&
				(isset($config['port'])) &&
				(isset($config['schema'])) &&
				(isset($config['charset'])) &&
				(isset($config['username'])) &&
				(isset($config['password']))
			);
		}
		
		// Trim params before executing
		private function trimParams(&$params) {
			foreach ($params as &$val) {
				if(is_string($val)) {
					$val = trim($val);
				}
			}
		}

		// Logs information into file
		private function errorLog($message, $filename='logs/database.txt') {
			//date_default_timezone_set("Europe/Sofia");
			$debug = debug_backtrace();
			$time = date('H:i:s');
			$func = 'Call Stack: ';
			for ($i=count($debug)-1; $i >= 1; $i--) { 
				$func .= $debug[$i]['function'].'()'.PHP_EOL;
			}
			
			$file = 'File: '.$debug[count($debug)-1]['file'].':'.$debug[count($debug)-1]['line'];
			$error = "[$time] ".$file.PHP_EOL.$func.PHP_EOL.'Message: '.$message.PHP_EOL;
			//echo $error;
			file_put_contents($filename, $error, FILE_APPEND);
		}
    }
?>