<?php
//------------------------------------------------
// DataBase
//------------------------------------------------

if ( !class_exists( 'DB' ) ) {
	class DB {
		
		private  $database;
		private  $user;
		private  $password;
		private  $host;
		private  $mysqli;

		public function __construct() {
                // Configuración compatible con XAMPP/Laragon y variables de entorno.
                // Defaults locales: DB games, user root, password vacío.
                $this->database = getenv('JUEGA123_DB_NAME') ?: 'games';
                $this->user     = getenv('JUEGA123_DB_USER') ?: 'root';
                $this->password = getenv('JUEGA123_DB_PASS') ?: '';
                $this->host     = getenv('JUEGA123_DB_HOST') ?: 'localhost';
                $this->mysqli   = null;
            }

			public function __destruct() {
			//mysqli_close($mysqli);
		}
		
		protected function connect() {
			$mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
			
			if ($mysqli->connect_error) {
				die('Connect Error: ');
				return die('Connect Error: ' . $mysqli->connect_error);
			}			
			return $mysqli;

		}
		
		public function query($query) {
            $db = $this->connect();
            $results = array();
            $result = $db->query($query);
            if ($result instanceof mysqli_result) {
                while ( $row = $result->fetch_object() ) {
                    $results[] = $row;
                }
            }
            return $results;
        }

		public function insert($table, $data, $format) {
			// Check for $table or $data not set
			if ( empty( $table ) || empty( $data ) || empty( $format ) ) {
				return false;
			}
			$results = 0;
			
			// Connect to the database
			$db = $this->connect();
			
			// Cast $data and $format to arrays
			$data = (array) $data;
			$format = (array) $format;
			
			// Build format string
			$format = implode('', $format); 
			$format = str_replace('%', '', $format);
			
			list( $fields, $placeholders, $values ) = $this->prep_query($data);
			
			// Prepend $format onto $values
			array_unshift($values, $format); 
			// Prepary our query for binding 
			if($stmt = $db->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})")){	
				// Dynamically bind values
				call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));	 		
				// Execute the query
				if($stmt->execute()){					
					// Check for successful insertion
					if ( $stmt->affected_rows ) {
						$results = $db->insert_id;						
						$stmt->close();
						return $results;
					}
				}else{
					echo 'ERROR en dbconfig '.__LINE__.' '.$db->error;
				}
				$stmt->close();
			}else{
				echo 'ERROR en dbconfig 99 '.__LINE__.' '.$db->error .' DataBase:'.$this->database;
			}
			return false;
		}
		
		public function update($table, $data, $format, $where, $where_format) {
			// Check for $table or $data not set
			if ( empty( $table ) || empty( $data ) ) {
				return false;
			}
			
			// Connect to the database
			$db = $this->connect();
			
			// Cast $data and $format to arrays
			$data = (array) $data;
			$format = (array) $format;
			
			// Build format array
			$format = implode('', $format); 
			$format = str_replace('%', '', $format);
			$where_format = implode('', $where_format); 
			$where_format = str_replace('%', '', $where_format);
			$format .= $where_format;
			
			list( $fields, $placeholders, $values ) = $this->prep_query($data, 'update');
			
			//Format where clause
			$where_clause = '';
			$where_values = '';
			$count = 0;
			
			foreach ( $where as $field => $value ) {
				if ( $count > 0 ) {
					$where_clause .= ' AND ';
				}
				
				$where_clause .= $field . '=?';
				$where_values[] = $value;
				
				$count++;
			}
			// Prepend $format onto $values
			array_unshift($values, $format);
			$values = array_merge($values, $where_values);
			// Prepary our query for binding
			if($stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_clause}")){
				// Dynamically bind values
				call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));
				
				// Execute the query
				if($stmt->execute()){ 
					// Check for successful insertion
					if ( $stmt->affected_rows ) {
						$stmt->close();
						//mysqli_close($db);
						return true;
					} 
				}else{
					if (defined('DEBUG')){
							
					}
				}
				$stmt->close();
			}
			//mysqli_close($db);
			return false;
		}
		
		public function select($query, $data, $format) {
			$results['count'] = 0;
			// Connect to the database
			$db = $this->connect();
			//$stmt = $db->stmt_init();
			//Prepare our query for binding
			if($stmt = $db->prepare($query)){			
				//Normalize format
				if(!empty($format) && $format!=''){
					$format = implode('', $format); 
					$format = str_replace('%', '', $format);

					// Prepend $format onto $values
					$data = (array)$data;
						array_unshift($data, $format);
					
					//Dynamically bind values
					call_user_func_array(array( $stmt, 'bind_param'), $this->ref_values($data));
				}

				//Execute the query
				if($stmt->execute()){
					//Fetch results
					$result = $stmt->get_result();
					
					//$stmt->store_result();
					$results['count'] = $result->num_rows;
					
					//Create results object
					while ($row = $result->fetch_object()) {
						$results[] = $row;
					}
					$stmt->close();
				} else {
					if (defined('DEBUG')){
						if (mysqli_error($db)){
							die(mysqli_error($db));
						}
					}
				}
			}else {
				if (defined('DEBUG')){
					if (mysqli_error($db)){
						die(mysqli_error($db));
					}
				}
			}
			//mysqli_close($db);
			return $results;
		}
		
		public function delete($table, $column, $id) {
			
			// Check for $table or $data not set
			if ( empty( $table ) || empty( $column ) || empty( $id ) ) {
				return false;
			}
			
			$db = $this->connect();
			$id = $this->check_input($id);

			// Prepary our query for binding
			if($stmt = $db->prepare("DELETE FROM {$table} WHERE {$column} = ?")){
				// Dynamically bind values
				$stmt->bind_param('i', $id);
				
				// Execute the query
				if($stmt->execute()){
					if ( $stmt->affected_rows ) {
						$stmt->close();
						//mysqli_close($db);
						return true;
					}
				}
			}

			//mysqli_close($db);
			return false;
		}
		
		private function prep_query($data, $type='insert') {
			// Instantiate $fields and $placeholders for looping
			$fields = '';
			$placeholders = '';
			$values = array();
			
			// Loop through $data and build $fields, $placeholders, and $values			
			foreach ( $data as $field => $value ) {
				$fields .= "{$field},";
				$values[] = $this->check_input($value);
				
				if ( $type == 'update') {
					$placeholders .= $field . '=?,';
				} else {
					$placeholders .= '?,';
				}
				
			}
			
			// Normalize $fields and $placeholders for inserting
			$fields = substr($fields, 0, -1);
			$placeholders = substr($placeholders, 0, -1);
			
			return array( $fields, $placeholders, $values );
		}
		
		private function ref_values($array) {
            $refs = array();
            foreach ($array as $key => $value) {
                $refs[$key] = &$array[$key];
            }
            return $refs;
        }

			private function check_input($value){
			// Stripslashes
			if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())  {
			  $value = stripslashes($value);
			 }
			// Quote if not a number
			if (!is_numeric($value)){
			   $value = preg_replace("/[^a-zA-Z0-9]+'/", "", $value);
			 }
			return $value;
		}
		
	}
}
?>