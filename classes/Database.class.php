<?php
//------------------------------------------------------------------
/// \file   Database.class.php
/// \brief  Database abstraction model
/// \author A. M. Soell
/// \date   4/21/2008
/// \version  3.0
///
/// Attempt to keep the database interactions somewhat abstracted,
/// in the event of adding database support beyond MySQL
///
// $Id:$
//-------------------------------------------------------------------

//
///	\brief	Database class
///
/// Basic database and recordset calls
//
  class Database extends Common {
    private $connection; // Resource
    private $rec; // Recordset
    private $prefix; // String
    
//
/// \brief  Constructor method
///
/// Creates the database object and opens a connection based on configuration settings;
///
/// \return								true on successful connection
//       
    public function __construct() {
  		$config = new Config();
  		$this->prefix = $config->db_prefix;
  		
      if ($this->connection = mysql_connect($config->db_server, $config->db_user, $config->db_password)) {
        mysql_select_db($config->db_database, $this->connection);
        $this->query("SET NAMES 'utf8'");
      } else {
        return false;
      }
    }
    
    public function __destruct() {
      unset($this->rec);
      @mysql_close($this->connection);
    }
    
//
/// \brief  Query the database
///
/// Submits query to the database and stores the result
///
/// \param[in]  sql        String object of SQL statement
/// \return								Primary key value for affected / inserted records
//    
    public function query($sql, $debug=false) {
      $sql = str_replace('#__', $this->prefix, $sql);
      if ($debug) echo $sql;
      $rs = new Recordset(mysql_query($sql, $this->connection));
      if (strtoupper(substr($sql, 0, 6))=="SELECT") {
      	return $rs;
      } else {
        return mysql_insert_id($this->connection);
      }
    }
  }

  class Recordset {
    private $rs;

    public function __construct($rs = null) {
      if (!is_null($rs)) {
        $this->rs = $rs;
      }
    }

    public function fetch_array() {
      return @mysql_fetch_array($this->rs);
    }

    public function num_rows() {
      return mysql_num_rows($this->rs);
    }
  }
?>
