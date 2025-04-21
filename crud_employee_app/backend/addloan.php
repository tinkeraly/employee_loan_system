<?php
include '../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$eid = $data['eid'];
$amount = $data['amount'];

$sql = "INSERT INTO Loan (EID, LoanAmount) VALUES ('$eid', '$amount')";

if ($conn->query($sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'error' => $conn->error]);
}
?>
