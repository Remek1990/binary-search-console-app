<?php

include 'binarySearchClass.php';

$token = $argv[2];
try
{
  $res = BinarySearch::find( $argv[1] , $token );
}
catch(Exception $e)
{
   echo 'Exception: ' . htmlspecialchars( $e->getMessage() ) . PHP_EOL;
}

if( empty( $res ) )
{
  echo 'undef' . PHP_EOL;
}
else
{
  echo $token . ': ' . $res . PHP_EOL;
}