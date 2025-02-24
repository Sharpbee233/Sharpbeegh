<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Registration - Student Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            color: #1a73e8;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-section h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #1a73e8;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .btn-register:hover {
            background: #1557b0;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            display: none;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>School Registration</h1>
            <p>Register your school in the Student Attendance System</p>
        </div>

        <div class="alert alert-danger" id="error-message"></div>
        <div class="alert alert-success" id="success-message"></div>

        <form id="registrationForm">
            <div class="form-grid">
                <div class="form-section">
                    <h2>School Information</h2>
                    <div class="form-group">
                        <label for="school_name">School Name</label>
                        <input type="text" class="form-control" id="school_name" required>
                    </div>
                    <div class="form-group">
                        <label for="school_address">Address</label>
                        <textarea class="form-control" id="school_address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="school_contact">Contact Number</label>
                        <input type="tel" class="form-control" id="school_contact" required>
                    </div>
                    <div class="form-group">
                        <label for="school_email">School Email</label>
                        <input type="email" class="form-control" id="school_email" required>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Admin Information</h2>
                    <div class="form-group">
                        <label for="admin_firstname">First Name</label>
                        <input type="text" class="form-control" id="admin_firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_lastname">Last Name</label>
                        <input type="text" class="form-control" id="admin_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Email</label>
                        <input type="email" class="form-control" id="admin_email" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_username">Username</label>
                        <input type="text" class="form-control" id="admin_username" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Password</label>
                        <input type="password" class="form-control" id="admin_password" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="admin_confirm_password" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">Register School</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registrationForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset alerts
                $('.alert').hide();

                // Validate passwords match
                if($('#admin_password').val() !== $('#admin_confirm_password').val()) {
                    $('#error-message').text('Passwords do not match!').show();
                    return;
                }

                // Collect form data
                const formData = {
                    school: {
                        name: $('#school_name').val(),
                        address: $('#school_address').val(),
                        contact: $('#school_contact').val(),
                        email: $('#school_email').val()
                    },
                    admin: {
                        first_name: $('#admin_firstname').val(),
                        last_name: $('#admin_lastname').val(),
                        email: $('#admin_email').val(),
                        username: $('#admin_username').val(),
                        password: $('#admin_password').val()
                    }
                };

                // Submit registration
                $.ajax({
                    url: 'ajax/register.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        const data = JSON.parse(response);
                        if(data.success) {
                            $('#success-message').text('Registration successful! Redirecting to login...').show();
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 2000);
                        } else {
                            $('#error-message').text(data.message).show();
                        }
                    },
                    error: function() {
                        $('#error-message').text('An error occurred. Please try again.').show();
                    }
                });
            });
        });
    </script>
</body>
</html>
