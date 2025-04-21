<?php
header('Content-Type: application/json');

include(__DIR__ . '/includes/db.php');

$eid = $_GET['eid'];

$employeeQuery = "
    SELECT e.*, d.DeptDescription 
    FROM EmployeeInfo e 
    LEFT JOIN DepartmentInfo d ON e.DeptCode = d.DeptCode 
    WHERE e.EID = ?
";
$stmt = $conn->prepare($employeeQuery);
$stmt->bind_param("i", $eid);
$stmt->execute();
$employeeResult = $stmt->get_result();

if ($employeeResult->num_rows > 0) {
    $employee = $employeeResult->fetch_assoc();

    $loanQuery = "SELECT LoanAmount, Date FROM Loan WHERE EID = ?";
    $stmt = $conn->prepare($loanQuery);
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $loanResult = $stmt->get_result();

    $loans = [];
    while ($loan = $loanResult->fetch_assoc()) {
        $loans[] = $loan;
    }

    echo json_encode([
        'status' => 'success',
        'employee' => $employee,
        'loans' => $loans
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Employee not found'
    ]);
}
?>
