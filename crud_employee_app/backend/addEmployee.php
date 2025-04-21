<?php
session_start(); 

$conn = new mysqli("localhost", "root", "", "employeeloans");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eid = isset($_POST['eid']) ? trim($_POST['eid']) : '';
$name = trim($_POST['name']);
$position = trim($_POST['position']);
$salary = floatval($_POST['salary']);
$age = intval($_POST['age']);
$address = trim($_POST['address']);
$deptcode = trim($_POST['deptcode']);
$loan_amount = isset($_POST['loan_amount']) ? floatval($_POST['loan_amount']) : 0;

if (empty($eid) || empty($name) || empty($position) || empty($salary) || empty($age) || empty($address) || empty($deptcode)) {
    $_SESSION['message'] = "All fields including Employee ID are required (except loan).";
    $_SESSION['msg_type'] = "danger";
    header("Location: ../frontend/index.php");
    exit();
}

$check = $conn->prepare("SELECT EID FROM employeeinfo WHERE EID = ?");
if (!$check) {
    $_SESSION['message'] = "Prepare failed: " . $conn->error;
    $_SESSION['msg_type'] = "danger";
    header("Location: ../frontend/index.php");
    exit();
}
$check->bind_param("s", $eid); 
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $_SESSION['message'] = "Employee ID already exists. Please use a different EID.";
    $_SESSION['msg_type'] = "danger";
    $check->close();
    header("Location: ../frontend/index.php");
    exit();
}
$check->close();

$sql = "INSERT INTO employeeinfo (EID, Name, Position, Salary, Age, Address, DeptCode)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['message'] = "Prepare failed: " . $conn->error;
    $_SESSION['msg_type'] = "danger";
    header("Location: ../frontend/index.php");
    exit();
}
$stmt->bind_param("isssiss", $eid, $name, $position, $salary, $age, $address, $deptcode);

if ($stmt->execute()) {
    if (!empty($loan_amount) && $loan_amount > 0) {
        $loan_stmt = $conn->prepare("INSERT INTO loan (EID, LoanAmount, LoanDate) VALUES (?, ?, NOW())");
        if (!$loan_stmt) {
            $_SESSION['message'] = "Loan insert prepare failed: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
            header("Location: ../frontend/index.php");
            exit();
        }
        $loan_stmt->bind_param("id", $eid, $loan_amount);
        $loan_stmt->execute();
        $loan_stmt->close();
    }

    $_SESSION['message'] = "Employee successfully added.";
    $_SESSION['msg_type'] = "success";
} else {
    $_SESSION['message'] = "Error adding employee: " . $stmt->error;
    $_SESSION['msg_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: ../frontend/index.php");
exit();
?>
