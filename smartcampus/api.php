<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_students':
                $sql = "SELECT * FROM students ORDER BY created_at DESC";
                $result = $conn->query($sql);
                
                $students = [];
                while ($row = $result->fetch_assoc()) {
                    $students[] = $row;
                }
                
                echo json_encode(['success' => true, 'students' => $students]);
                break;
            case 'get_attendance_report':
    $sql = "SELECT s.id, s.name, s.roll_number, s.class,
            COUNT(CASE WHEN a.status='Present' THEN 1 END) as present,
            COUNT(CASE WHEN a.status='Absent' THEN 1 END) as absent
            FROM students s
            LEFT JOIN attendance a ON s.id = a.student_id
            GROUP BY s.id, s.name, s.roll_number, s.class
            ORDER BY s.name";
    
    $result = $conn->query($sql);
    $report = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report[] = [
                'name' => $row['name'],
                'roll_number' => $row['roll_number'],
                'class' => $row['class'],
                'present' => (int)$row['present'],
                'absent' => (int)$row['absent']
            ];
        }
    }
    
    echo json_encode(['success' => true, 'report' => $report]);
    break;

case 'get_fee_report':
    $sql = "SELECT s.id, s.name, s.roll_number, s.class,
            COALESCE(SUM(f.amount), 0) as total_paid,
            MAX(f.payment_date) as last_payment
            FROM students s
            LEFT JOIN fees f ON s.id = f.student_id
            GROUP BY s.id, s.name, s.roll_number, s.class
            ORDER BY s.name";
    
    $result = $conn->query($sql);
    $report = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $report[] = [
                'name' => $row['name'],
                'roll_number' => $row['roll_number'],
                'class' => $row['class'],
                'total_paid' => (float)$row['total_paid'],
                'last_payment' => $row['last_payment']
            ];
        }
    }
    
    echo json_encode(['success' => true, 'report' => $report]);
    break;
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'add_student':
            $name = $_POST['name'];
            $class = $_POST['class'];
            $roll_number = $_POST['roll_number'];
            $phone = $_POST['phone'];
            
            // Check if roll number already exists
            $check = $conn->prepare("SELECT id FROM students WHERE roll_number = ?");
            $check->bind_param("s", $roll_number);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Roll number already exists']);
                exit();
            }
            
            $stmt = $conn->prepare("INSERT INTO students (name, class, roll_number, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $class, $roll_number, $phone);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Student added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding student']);
            }
            break;
            
        case 'mark_attendance':
            $student_id = $_POST['student_id'];
            $date = $_POST['date'];
            $status = $_POST['status'];
            
            // Check if attendance already marked for this date
            $check = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND date = ?");
            $check->bind_param("is", $student_id, $date);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Attendance already marked for this date']);
                exit();
            }
            
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $student_id, $date, $status);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Attendance marked']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error marking attendance']);
            }
            break;
            
        case 'record_fee':
            $student_id = $_POST['student_id'];
            $amount = $_POST['amount'];
            $month = $_POST['month'];
            $payment_date = $_POST['payment_date'];
            
            $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, month, payment_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idss", $student_id, $amount, $month, $payment_date);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Fee recorded']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error recording fee']);
            }
            break;
    }
}

$conn->close();
?>