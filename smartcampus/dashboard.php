<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">🎓 Smart Campus</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Welcome Message -->
        <div class="row mb-4">
            <div class="col-12">
                <h2>Welcome, <?php echo $_SESSION['admin_username']; ?>!</h2>
                <p class="text-muted">Manage your campus efficiently</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h3 class="text-primary">👨‍🎓</h3>
                        <h6>Add Student</h6>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h3 class="text-success">✓</h3>
                        <h6>Mark Attendance</h6>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#attendanceModal">Mark</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h3 class="text-warning">💰</h3>
                        <h6>Record Fee</h6>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#feeModal">Record</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h3 class="text-info">📊</h3>
                        <h6>View Reports</h6>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#reportsModal">View</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">All Students</h5>
            </div>
            <div class="card-body">
                <div id="studentsTable">
                    <p class="text-center">Loading students...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="studentName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Class</label>
                            <select class="form-select" id="studentClass" required>
                                <option value="">Select Class</option>
                                <option value="9th">9th</option>
                                <option value="10th">10th</option>
                                <option value="11th">11th</option>
                                <option value="12th">12th</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" class="form-control" id="rollNumber" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" pattern="[0-9]{10}" placeholder="10 digit number">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="attendanceForm">
                        <div class="mb-3">
                            <label class="form-label">Select Student</label>
                            <select class="form-select" id="attendanceStudent" required>
                                <option value="">Loading students...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="attendanceDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="attendanceStatus" required>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Mark Attendance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Modal -->
<div class="modal fade" id="feeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Fee Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="feeForm">
                    <div class="mb-3">
                        <label class="form-label">Select Student</label>
                        <select class="form-select" id="feeStudent" required>
                            <option value="">Loading students...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" class="form-control" id="feeAmount" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Month</label>
                        <input type="month" class="form-control" id="feeMonth" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="feeDate" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Record Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>     
    <!-- Reports Modal -->
<div class="modal fade" id="reportsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📊 Campus Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="attendance-tab" data-bs-toggle="tab" 
                                data-bs-target="#attendance" type="button">Attendance Report</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fee-tab" data-bs-toggle="tab" 
                                data-bs-target="#fee" type="button">Fee Report</button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="reportTabContent">
                    <!-- Attendance Report Tab -->
                    <div class="tab-pane fade show active" id="attendance" role="tabpanel">
                        <div id="attendanceReport">
                            <p class="text-center">Loading attendance report...</p>
                        </div>
                    </div>
                    <!-- Fee Report Tab -->
                    <div class="tab-pane fade" id="fee" role="tabpanel">
                        <div id="feeReport">
                            <p class="text-center">Loading fee report...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dashboard.js"></script>
</body>
</html>