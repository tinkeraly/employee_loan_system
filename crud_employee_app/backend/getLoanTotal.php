<?php
include '../includes/db.php';

$sql = "SELECT E.Name, SUM(L.LoanAmount) AS TotalLoan 
        FROM EmployeeInfo E 
        JOIN Loan L ON E.EID = L.EID 
        GROUP BY E.EID";

$result = $conn->query($sql);
$totals = [];

while($row = $result->fetch_assoc()) {
    $totals[] = $row;
}
echo json_encode($totals);
?>
