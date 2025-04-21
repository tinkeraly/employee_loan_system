<?php
$conn = new mysqli("localhost", "root", "", "employeeloans");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eid = $_GET['EID'];

$sql = "SELECT * FROM EmployeeInfo WHERE EID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $employee = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($employee);
} else {
    http_response_code(404); 
    echo json_encode(["error" => "Employee not found"]);
}

$stmt->close();
$conn->close();
?>
