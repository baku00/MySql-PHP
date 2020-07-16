<?php
  require_once '../src/functions/mysql/config.inc.php';
  namespace MySql;
  use PDO;
  class MySql{
    private $pdo;
    private $info;
    private $connection;

    public function __construct($dataBase)
    {
      $this->connect($dataBase);
    }
    private function pdo_connexion($dbName,$host,$port,$username,$password)
    {
      error_reporting(E_ALL);
      $connStr = "mysql:host=$host;port=$port;dbname=$dbName";
      $pdoOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
      try {
        $pdo = new PDO($connStr, $username,$password, $pdoOptions);
      }
      catch(Exception $e)
      {
        if(!$mysql['display']['error'])die();
        
        $mysql['error']['message'] = str_replace('{FILE}',$e->getFile(),$mysql['error']['message']);
        $mysql['error']['message'] = str_replace('{LINE}',$e->getLine(),$mysql['error']['message']);
        $mysql['error']['message'] = str_replace('{MESSAGE}',$e->getMessage(),$mysql['error']['message']);
        $err=$mysql['error']['message'];
        die($err);
      }
      return $pdo;
    }
    public function update($table,array $values,$where,$value)
    {
      $fkv = $this->formatKeyValue($values,['=',','],true);
      $query = 'UPDATE ' . $table . ' SET '.$fkv['keys'].' WHERE '.$where . ' = :' . $where;
      $values[$where] = $value;
      $this->prepareBindExecute($query,$values);
    }
    public function remove($table,array $values)
    {
      $fkv = $this->formatKeyValue($values,['=',''],true);
      $query = 'DELETE FROM ' . $table . ' WHERE ' . $fkv['keys'];
      $pbe = $this->prepareBindExecute($query,$values);
    }
    public function select($table,array $values,$get='',$select = '')
    {
      $fkv = $this->formatKeyValue($values,['=',','],true);
      $query = 'SELECT '.(empty($select) ? '*' : $select).' FROM ' . $table . ' WHERE ' . $fkv['keys'];
      $pbe = $this->prepareBindExecute($query,$values);
      return empty($get) ? $pbe->fetchAll() : $pbe->fetch($get);
    }
    public function insert($table,array $values)
    {
      $fkv = $this->formatKeyValue($values);
      $query = 'INSERT INTO ' . $table . '('.$fkv['keys'].') VALUES ('.$fkv['values'].')';
      $pbe = $this->prepareBindExecute($query,$values);
    }
    private function prepareBindExecute($query,array $values)
    {
      $prepareBindExecute = $this->connection->prepare($query);
      foreach ($values as $key => $value) {
        $prepareBindExecute->bindValue(':'.$key,$value);
      }
      $prepareBindExecute->execute();
      return $prepareBindExecute;
    }
    private function formatKeyValue(array $values,array $split = [',',','], $fusionne = false)
    {
      $keys = '';
      $vals = '';
      foreach($values as $key=>$val){
        if(!$fusionne){
          $keys .= $key .$split[0];
          $vals .= ':'.$key .$split[1];
        }
        else {
          $keys .= $key .$split[0].':'.$key.$split[1];
        }
      }

      $keys = trim($keys,',');
      $vals = trim($vals,',');
      return ['keys'=>$keys,'values'=>$vals];
    }
    private function getConnection()
    {
      return $this->connection;
    }
    private function connect($dataBase)
    {
      $conn = $this->connection($dataBase);
      $this->connection = $this->pdo_connexion($conn['db'],$conn['host'],$conn['port'],$conn['username'],$conn['password']);
    }
    private function connection($name)
    {
      require_once $path['connections'];
      return $connections[$name];
    }
  }
