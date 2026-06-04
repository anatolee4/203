<?php
$motDePasse = 'Admin1234';
echo password_hash($motDePasse, PASSWORD_DEFAULT);
?>