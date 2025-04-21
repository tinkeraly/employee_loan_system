<?php
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['eid'])) {
    $eid = $_POST['eid'];

    $deleteLoans = "DELETE FROM Loan WHERE EID = ?";
    $stmt1 = $conn->prepare($deleteLoans);
    $stmt1->bind_param("i", $eid);
    $stmt1->execute();

    $deleteEmployee = "DELETE FROM EmployeeInfo WHERE EID = ?";
    $stmt2 = $conn->prepare($deleteEmployee);
    $stmt2->bind_param("i", $eid);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        echo "success";
    } else {
        echo "failed";
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
}
?>
