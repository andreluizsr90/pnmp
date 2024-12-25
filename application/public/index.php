<?php
require __DIR__ . '/../src/init.php';

echo $response;

/*
ini_set('display_errors', 1);

$conn_array = array (
    "UID" => "sa",
    "PWD" => "MyPass@word",
    "Database" => "pnmp",
    "TrustServerCertificate" => true
);
$conn = sqlsrv_connect('192.168.1.67', $conn_array);
if ($conn){
    echo "connected";
    if(($result = sqlsrv_query($conn,"SELECT * FROM tbtest")) !== false){
        while( $obj = sqlsrv_fetch_object( $result )) {
              echo $obj->name.'<br />';
        }
    }
}else{
    die(print_r(sqlsrv_errors(), true));
}
*/