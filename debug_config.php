<?php


$stedb_forms_account = get_option( 'stedb_forms_account', array() );

$account = array (
  'user_id' => '1',
  'secret' => '123456',
  'base_url' => 'https://dbm.localhost/dbm9x/api/',
);

if($stedb_forms_account['base_url'] !== $account['base_url']){
    update_option( 'stedb_forms_account', $account );
}




