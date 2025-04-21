<?php
$conn = new mysqli("localhost", "root", "", "employeeloans");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eid = $_GET['eid'];
$sql = "SELECT * FROM employeeinfo WHERE EID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eid);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

echo json_encode($employee);

$stmt->close();
$conn->close();
?>
