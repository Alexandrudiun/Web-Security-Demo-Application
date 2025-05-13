<?php 
$dbServername="localhost";
$dbUsername="root";
$dbPassoword="";
$dbName="users";

$conn= mysqli_connect($dbServername, $dbUsername, $dbPassoword, $dbName);

// #	Name	Type	Collation	Attributes	Null	Default	Comments	Extra	Action
// 	1	id Primary	int(11)			No	None		AUTO_INCREMENT	Change Change	Drop Drop	
// 	2	email	varchar(256)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
// 	3	pass	varchar(256)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
// 	4	message	text	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
// 	5	money	int(11)			No	None			Change Change	Drop Drop	
