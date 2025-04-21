<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eid = $_POST['EID'] ?? '';
    $name = $_POST['Name'] ?? '';
    $position = $_POST['Position'] ?? '';
    $salary = $_POST['Salary'] ?? 0;
    $age = $_POST['Age'] ?? 0;
    $address = $_POST['Address'] ?? '';
    $deptCode = $_POST['deptCode'] ?? '';

    if (empty($eid) || empty($name)) {
        echo "Missing required fields.";
        exit;
    }

    $sql = "UPDATE EmployeeInfo 
            SET Name = ?, Position = ?, Salary = ?, Age = ?, Address = ?, DeptCode = ?
            WHERE EID = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssdiisi", $name, $position, $salary, $age, $address, $deptCode, $eid);

    if ($stmt->execute()) {
        echo "success"; 
    } else {
        echo "Update failed: " . $stmt->error; 

    $stmt->close();
    $conn->close();
}
?>
