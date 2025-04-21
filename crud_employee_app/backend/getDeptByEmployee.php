<?php
include '../includes/db.php';

$eid = $_GET['eid'];
$sql = "SELECT D.DeptDescription 
        FROM DepartmentInfo D 
        JOIN EmployeeInfo E ON D.DeptCode = E.DeptCode 
        WHERE E.EID = $eid";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode($row);
?>
