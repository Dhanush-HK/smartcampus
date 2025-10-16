// Wait for page to fully load
window.addEventListener('load', function() {
    console.log('Page loaded successfully');
    initializeDashboard();
});

// Initialize all dashboard functionality
function initializeDashboard() {
    // Load initial data
    loadStudents();
    loadStudentDropdowns();
    
    // Set today's date as default
    setDefaultDates();
    
    // Setup all form handlers
    setupAddStudentForm();
    setupAttendanceForm();
    setupFeeForm();
    setupReportsModal();
}

// Set default dates
function setDefaultDates() {
    const today = new Date().toISOString().split('T')[0];
    const attendanceDate = document.getElementById('attendanceDate');
    const feeDate = document.getElementById('feeDate');
    
    if (attendanceDate) attendanceDate.value = today;
    if (feeDate) feeDate.value = today;
}

// Setup Add Student Form
function setupAddStudentForm() {
    const form = document.getElementById('addStudentForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'add_student');
        formData.append('name', document.getElementById('studentName').value);
        formData.append('class', document.getElementById('studentClass').value);
        formData.append('roll_number', document.getElementById('rollNumber').value);
        formData.append('phone', document.getElementById('phone').value);
        
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student added successfully!');
                form.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('addStudentModal'));
                if (modal) modal.hide();
                loadStudents();
                loadStudentDropdowns();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
}

// Setup Attendance Form
function setupAttendanceForm() {
    const form = document.getElementById('attendanceForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'mark_attendance');
        formData.append('student_id', document.getElementById('attendanceStudent').value);
        formData.append('date', document.getElementById('attendanceDate').value);
        formData.append('status', document.getElementById('attendanceStatus').value);
        
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Attendance marked successfully!');
                form.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('attendanceModal'));
                if (modal) modal.hide();
                setDefaultDates();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
}

// Setup Fee Form
function setupFeeForm() {
    const form = document.getElementById('feeForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'record_fee');
        formData.append('student_id', document.getElementById('feeStudent').value);
        formData.append('amount', document.getElementById('feeAmount').value);
        formData.append('month', document.getElementById('feeMonth').value);
        formData.append('payment_date', document.getElementById('feeDate').value);
        
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Fee recorded successfully!');
                form.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('feeModal'));
                if (modal) modal.hide();
                setDefaultDates();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
}

// Setup Reports Modal
function setupReportsModal() {
    const reportsModal = document.getElementById('reportsModal');
    if (!reportsModal) {
        console.error('Reports modal not found');
        return;
    }
    
    reportsModal.addEventListener('shown.bs.modal', function() {
        console.log('Reports modal opened');
        loadAttendanceReport();
        loadFeeReport();
    });
    
    // Also setup tab switching
    const attendanceTab = document.getElementById('attendance-tab');
    const feeTab = document.getElementById('fee-tab');
    
    if (attendanceTab) {
        attendanceTab.addEventListener('click', function() {
            loadAttendanceReport();
        });
    }
    
    if (feeTab) {
        feeTab.addEventListener('click', function() {
            loadFeeReport();
        });
    }
}

// Load all students
function loadStudents() {
    fetch('api.php?action=get_students')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayStudents(data.students);
        }
    })
    .catch(error => console.error('Error loading students:', error));
}

// Display students in table
function displayStudents(students) {
    const tableDiv = document.getElementById('studentsTable');
    if (!tableDiv) return;
    
    if (students.length === 0) {
        tableDiv.innerHTML = '<p class="text-center text-muted">No students found. Add your first student!</p>';
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr><th>Roll No</th><th>Name</th><th>Class</th><th>Phone</th><th>Added On</th></tr></thead>';
    html += '<tbody>';
    
    students.forEach(student => {
        html += `<tr>
            <td>${student.roll_number}</td>
            <td>${student.name}</td>
            <td>${student.class}</td>
            <td>${student.phone || 'N/A'}</td>
            <td>${new Date(student.created_at).toLocaleDateString()}</td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    tableDiv.innerHTML = html;
}

// Load students into dropdowns
function loadStudentDropdowns() {
    fetch('api.php?action=get_students')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const attendanceSelect = document.getElementById('attendanceStudent');
            const feeSelect = document.getElementById('feeStudent');
            
            let options = '<option value="">Select Student</option>';
            data.students.forEach(student => {
                options += `<option value="${student.id}">${student.name} (${student.roll_number})</option>`;
            });
            
            if (attendanceSelect) attendanceSelect.innerHTML = options;
            if (feeSelect) feeSelect.innerHTML = options;
        }
    })
    .catch(error => console.error('Error loading dropdowns:', error));
}

// Load Attendance Report
function loadAttendanceReport() {
    const reportDiv = document.getElementById('attendanceReport');
    if (!reportDiv) {
        console.error('Attendance report div not found');
        return;
    }
    
    reportDiv.innerHTML = '<div class="text-center my-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading attendance report...</p></div>';
    
    fetch('api.php?action=get_attendance_report')
    .then(response => response.json())
    .then(data => {
        console.log('Attendance report data:', data);
        if (data.success) {
            displayAttendanceReport(data.report);
        } else {
            reportDiv.innerHTML = '<div class="alert alert-danger">Error loading report</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        reportDiv.innerHTML = '<div class="alert alert-danger">Failed to load report. Check console for details.</div>';
    });
}

// Display Attendance Report
function displayAttendanceReport(report) {
    const reportDiv = document.getElementById('attendanceReport');
    if (!reportDiv) return;
    
    if (!report || report.length === 0) {
        reportDiv.innerHTML = '<div class="alert alert-info text-center">No attendance records found. Mark attendance first!</div>';
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
    html += '<thead class="table-light"><tr><th>Student Name</th><th>Roll No</th><th>Present Days</th><th>Absent Days</th><th>Total Days</th><th>Attendance %</th></tr></thead>';
    html += '<tbody>';
    
    report.forEach(record => {
        const present = parseInt(record.present) || 0;
        const absent = parseInt(record.absent) || 0;
        const total = present + absent;
        const percentage = total > 0 ? ((present / total) * 100).toFixed(2) : 0;
        const colorClass = percentage >= 75 ? 'text-success' : percentage >= 50 ? 'text-warning' : 'text-danger';
        
        html += `<tr>
            <td>${record.name}</td>
            <td>${record.roll_number}</td>
            <td class="text-success"><strong>${present}</strong></td>
            <td class="text-danger"><strong>${absent}</strong></td>
            <td><strong>${total}</strong></td>
            <td class="${colorClass}"><strong>${percentage}%</strong></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    reportDiv.innerHTML = html;
}

// Load Fee Report
function loadFeeReport() {
    const reportDiv = document.getElementById('feeReport');
    if (!reportDiv) {
        console.error('Fee report div not found');
        return;
    }
    
    reportDiv.innerHTML = '<div class="text-center my-4"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading fee report...</p></div>';
    
    fetch('api.php?action=get_fee_report')
    .then(response => response.json())
    .then(data => {
        console.log('Fee report data:', data);
        if (data.success) {
            displayFeeReport(data.report);
        } else {
            reportDiv.innerHTML = '<div class="alert alert-danger">Error loading report</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        reportDiv.innerHTML = '<div class="alert alert-danger">Failed to load report. Check console for details.</div>';
    });
}

// Display Fee Report
function displayFeeReport(report) {
    const reportDiv = document.getElementById('feeReport');
    if (!reportDiv) return;
    
    if (!report || report.length === 0) {
        reportDiv.innerHTML = '<div class="alert alert-info text-center">No fee records found. Record fees first!</div>';
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
    html += '<thead class="table-light"><tr><th>Student Name</th><th>Roll No</th><th>Total Paid (₹)</th><th>Last Payment</th><th>Status</th></tr></thead>';
    html += '<tbody>';
    
    report.forEach(record => {
        const totalPaid = parseFloat(record.total_paid) || 0;
        const status = totalPaid >= 5000 ? 'Up to Date' : 'Pending';
        const statusClass = totalPaid >= 5000 ? 'badge bg-success' : 'badge bg-warning text-dark';
        
        html += `<tr>
            <td>${record.name}</td>
            <td>${record.roll_number}</td>
            <td><strong>₹${totalPaid.toFixed(2)}</strong></td>
            <td>${record.last_payment ? new Date(record.last_payment).toLocaleDateString() : 'N/A'}</td>
            <td><span class="${statusClass}">${status}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    reportDiv.innerHTML = html;
}