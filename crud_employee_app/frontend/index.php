<?php
$conn = new mysqli("localhost", "root", "", "employeeloans");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT e.EID, e.Name, e.Position, e.Salary, e.Age, e.Address, e.DeptCode, d.DeptDescription, 
               IFNULL(SUM(l.LoanAmount), 0) AS TotalLoan
        FROM employeeinfo e
        LEFT JOIN departmentinfo d ON e.DeptCode = d.DeptCode
        LEFT JOIN loan l ON e.EID = l.EID
        GROUP BY e.EID";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Information</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f6f6f6;
        margin: 20px;
    }
    h2 { margin-bottom: 10px; }
    .search-container {
        margin-bottom: 15px;
    }
    .search-container input {
        padding: 8px;
        width: 250px;
    }
    .btn {
        padding: 8px 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        margin-left: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background-color:rgb(76, 175, 80);
        color: white;
    }
    .actions i {
        margin: 0 5px;
        cursor: pointer;
        font-size: 20px;
        z-index:1000;
        position: relative;
    }
    .modal {
            display: none;
            position: fixed;
            z-index: 1002;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
    }
    .modal-content {
            background-color: #ffffff; 
            padding: 45px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            color: #000; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
   }

    .modal-content input[type="text"], .modal-content input[type="number"], .modal-content input[type="date"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
    }
    .modal-content button {
        padding: 10px;
        margin-top: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }
   
    .eye-icon {
    cursor: pointer;
    font-size: 24px;
    color: #007bff;
    transition: color 0.3s;
    }
    .eye-icon:hover {
    color: #0056b3;
    }
    #viewModalContent {
    max-height: 500px;
    overflow-y: auto;
    }
    .modal-content button {
    padding: 10px;
    margin-top: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    }

  </style>
</head>
<body>
<?php
session_start();
if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?>">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
        ?>
    </div>
<?php endif ?>

<h2>Employee Information</h2>

<div class="search-container">
  <input type="text" id="searchInput" placeholder="Search by EID, Name, or DeptCode">
  <button class="btn" onclick="searchEmployee()">Search</button>
  <button class="btn" onclick="openModal('addModal')">Add Employee</button>
</div>

<div id="backButtonContainer" style="display: none; margin-bottom: 15px;">
  <button class="btn" onclick="goBackToDashboard()">Back to Dashboard</button>
</div>

<table>
  <thead>
    <tr>
      <th>Employee ID</th>
      <th>Name</th>
      <th>Position</th>
      <th>Salary</th>
      <th>Age</th>
      <th>Address</th>
      <th>DeptCode</th>
      <th>Total Loan</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody id="employeeTable">
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['EID'] ?></td>
      <td><?= $row['Name'] ?></td>
      <td><?= $row['Position'] ?></td>
      <td><?= $row['Salary'] ?></td>
      <td><?= $row['Age'] ?></td>
      <td><?= $row['Address'] ?></td>
      <td><?= $row['DeptCode'] ?></td>
      <td>₱<?= number_format($row['TotalLoan'], 2) ?></td>
      <td class="actions">
        <i class="eye" onclick="openViewModal(<?= $row['EID'] ?>)" style="cursor: pointer;">👁</i>
        <i class="plus" onclick="openAddLoanModal(<?= $row['EID'] ?>)">➕</i>
        <i class="trash" onclick="openDeleteModal(<?= $row['EID'] ?>)">🗑</i>
        <i class="pen" data-id="<?= $row['EID'] ?>" onclick="editEmployee(<?= $row['EID'] ?>)">✏️</i> 

      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<!-- Add Employee Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3>Add Employee</h3>
    <form method="POST" action="../backend/addEmployee.php">
    <label for="eid">Employee ID:</label><input type="text" name="eid" id="eid" required>
      <label for="name">Name:</label><input type="text" name="name" id="name" required>
      <label for="position">Position:</label><input type="text" name="position" id="position" required>
      <label for="salary">Salary:</label><input type="number" name="salary" id="salary" required>
      <label for="age">Age:</label><input type="number" name="age" id="age" required>
      <label for="address">Address:</label><input type="text" name="address" id="address" required>
      <label for="dept">Dept Code:</label><input type="text" name="deptcode" id="dept" required>
      <label for="initialLoan">Initial Loan Amount (optional):</label><input type="number" name="loan_amount"id="initialLoan">
      <button type="submit">Submit</button>
      <button type="button" onclick="closeModal('addModal')">Close</button>
    </form>
  </div>
</div>

<!-- Edit Employee Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <h3>Edit Employee</h3>
    <form id="editEmployeeForm" method="POST" action="../backend/editEmployee.php">
      <input type="hidden" id="editEid" name="EID">    
      <label for="editName">Name:</label>
      <input type="text" id="editName" name="Name" required>
      <label for="editPosition">Position:</label>
      <input type="text" id="editPosition" name="Position" required>
      <label for="editSalary">Salary:</label>
      <input type="number" step="0.01" id="editSalary" name="Salary" required>
      <label for="editAge">Age:</label>
      <input type="number" id="editAge" name="Age" required>
      <label for="editAddress">Address:</label>
      <input type="text" id="editAddress" name="Address" required>
      <label for="editDeptCode">Department Code:</label>
      <input type="text" id="editDeptCode" name="DeptCode" required>
      <button type="submit">Save Changes</button>
      <button type="button" onclick="closeModal('editModal')">Close</button>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <h3>Confirm Delete</h3>
    <p>Are you sure you want to delete this employee?</p>
    <input type="hidden" id="deleteEID">
    <button id="deleteButton" onclick="confirmDelete()">Delete</button>
    <button onclick="closeModal('deleteModal')">Cancel</button>
  </div>
</div>

<!-- View Employee Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content" id="viewModalContent">
    <h3>Employee Loan Information</h3>

    <div>
      <strong>Employee ID:</strong> <span id="viewEID"></span><br>
      <strong>Name:</strong> <span id="viewName"></span><br>
      <strong>Dept Code:</strong> <span id="viewDeptCode"></span><br>
      <strong>Dept Description:</strong> <span id="viewDeptDesc"></span><br>
    </div>
    <hr>
    <div id="loanBreakdownSection">
      <h4>Loan Breakdown</h4>
      <table id="loanBreakdownTable">
    <thead>
      <tr>
        <th>Loan Amount</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody id="loanBreakdown">
  
    </tbody>
  </table>
    <br>
    <button onclick="closeModal('viewModal')">Close</button>
  </div>
</div>

<!-- Add Loan Modal -->
<div id="addLoanModal" class="modal">
  <div class="modal-content">
    <h3>Add Loan</h3>
    <form method="POST" action="../backend/addLoan.php">
      <input type="hidden" id="loanEid" name="EID">
      <label for="loanAmount">Loan Amount:</label><input type="number" id="loanAmount" name="loanAmount">
      <label for="loanDate">Loan Date:</label><input type="date" id="loanDate" name="loanDate">
      <button type="submit">Add Loan</button>
      <button type="button" onclick="closeModal('addLoanModal')">Close</button>
    </form>
  </div>
</div>

<script>
function searchEmployee() {
  let query = document.getElementById("searchInput").value.toLowerCase();
  let rows = document.querySelectorAll("#employeeTable tr");

  document.getElementById("backButtonContainer").style.display = query ? 'block' : 'none';

  rows.forEach(row => {
    let cells = row.getElementsByTagName("td");
    let matches = false;

    for (let i = 0; i < 3; i++) {
      if (cells[i].innerText.toLowerCase().includes(query)) {
        matches = true;
        break;
      }
    }

    if (matches) row.style.display = '';
    else row.style.display = 'none';
  });
}

function openViewModal(eid) {
  console.log("Opening modal for EID:", eid);
  fetch(`../backend/getLoansByEmployee.php?eid=${eid}`)
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
      
        document.getElementById("viewModal").style.display = "flex";

        const emp = data.employee;
        document.getElementById("viewEID").textContent = emp.EID;
        document.getElementById("viewName").textContent = emp.Name;
        document.getElementById("viewDeptCode").textContent = emp.DeptCode;
        document.getElementById("viewDeptDesc").textContent = emp.DeptDescription;

        const loanTbody = document.getElementById("loanBreakdown");
        loanTbody.innerHTML = ""; 

        if (data.loans.length > 0) {
          data.loans.forEach(loan => {
            const row = document.createElement("tr");
            row.innerHTML = `
              <td>₱${loan.LoanAmount}</td>
              <td>${loan.Date}</td>
            `;
            loanTbody.appendChild(row);
          });
        } else {
          loanTbody.innerHTML = `
            <tr>
              <td colspan="2">No existing loans.</td>
            </tr>
          `;
        }
      } else {
        alert("Employee not found.");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      alert("Failed to load employee info.");
    });
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = "none";
}

function openDeleteModal(eid) {
  document.getElementById('deleteEID').value = eid;
  document.getElementById('deleteModal').style.display = 'block';
}

function openAddLoanModal(EID) {
  openModal("addLoanModal");
  document.getElementById("loanEid").value = EID;
}
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}
function editEmployee(eid) {
    const rows = document.querySelectorAll("#employeeTable tr");
    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        if (cells[0].innerText == eid) {
            document.getElementById("editEid").value = cells[0].innerText;
            document.getElementById("editName").value = cells[1].innerText;
            document.getElementById("editPosition").value = cells[2].innerText;
            document.getElementById("editSalary").value = cells[3].innerText;
            document.getElementById("editAge").value = cells[4].innerText;
            document.getElementById("editAddress").value = cells[5].innerText;
            document.getElementById("editDeptCode").value = cells[6].innerText;

            openModal('editModal');
        }
    });
}
function openModal(Id) {
    document.getElementById(Id).style.display = 'flex';
}
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function goBackToDashboard() {
  window.location.href = 'index.php';
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  $('#editEmployeeForm').on('submit', function(e) {
    e.preventDefault(); 

    $.ajax({
      url: '../backend/editEmployee.php', 
      type: 'POST',
      data: $(this).serialize(), 
      success: function(response) {
        if (response.trim() === "success") {
          alert("Employee updated successfully!");
          closeModal('editModal');
          location.reload(); 
        } else {
          alert("Update failed: " + response);
        }
      },
      error: function() {
        alert("AJAX error occurred.");
      }
    });
  });
});
</script>

</body>
</html>
