<?php
$conn = new mysqli("localhost", "root", "", "employeeloans");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eid = $_POST['eid'];
$loan_amount = $_POST['loan_amount'];

$sql = "INSERT INTO loan (EID, LoanAmount) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $eid, $loan_amount);

if ($stmt->execute()) {
    header("Location: ../frontend/index.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
