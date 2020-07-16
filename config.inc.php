<?php
  // File connections path
  // exemple:
  // 'firstConnection'=>[
  //   'db'        =>  'myDataBase',
  //   'host'      =>  'myHost',
  //   'port'      =>  'myPort',
  //   'username'  =>  'myUsername',
  //   'password'  =>  'myPassword',
  // ],
  $path['connections'] = '../src/functions/mysql/connections.inc.php';

  // Show mysql errors
  $mysql['display']['error']=false;

  // {FILE} = $e->getFile();
  // {LINE} = $e->getLine();
  // {MESSAGE} = $e->getMessage();
  $mysql['error']['message']='Err PDO dans {FILE} [{LINE}] {MESSAGE}';
