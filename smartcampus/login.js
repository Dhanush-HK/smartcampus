// Wait for the page to load
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorMsg = document.getElementById('errorMsg');

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent page reload

        // Get form values
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        // Basic validation
        if (username === '' || password === '') {
            showError('Please fill in all fields');
            return;
        }

        // Send data to backend using Fetch API
        fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Login successful - redirect to dashboard
                window.location.href = 'dashboard.php';
            } else {
                // Show error message
                showError(data.message);
            }
        })
        .catch(error => {
            showError('An error occurred. Please try again.');
            console.error('Error:', error);
        });
    });

    // Function to display error messages
    function showError(message) {
        errorMsg.textContent = message;
        errorMsg.classList.remove('d-none');
        
        // Hide error after 3 seconds
        setTimeout(() => {
            errorMsg.classList.add('d-none');
        }, 3000);
    }
});