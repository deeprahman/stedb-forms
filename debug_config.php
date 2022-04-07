<?php

$stedb_forms_account = get_option( 'stedb_forms_account', array() );
if(isset($_SERVER['HTTP_HOST'])){
  $host = substr($_SERVER['HTTP_HOST'], 0, 5);
  if(in_array($host, array('local', '127.0', '192.1','dev.d','dbm.d'))){
     define('DEBUG', TRUE);
     define ('REMOTE', TRUE);
  }else{
     define('DEBUG', FALSE);
     define ('REMOTE', FALSE);
  }
}
$account = array (
  'user_id' => '1',
  'secret' => '123456',
  'base_url' => REMOTE?'https://opt4.stedb.com/dbm9x/api/':'http://dbm.deeprahman.lo/dbm9x/api/',
);

if($stedb_forms_account['base_url'] != $account['base_url']){
    update_option( 'stedb_forms_account', $account );
}




