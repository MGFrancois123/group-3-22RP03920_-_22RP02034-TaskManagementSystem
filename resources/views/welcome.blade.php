<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Task Management System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            text-align: center;
            padding: 80px 20px;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(90deg, #ff9800, #ff5722);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .card-glass:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.4);
        }

        .auth-links a {
            color: white;
            margin: 0 15px;
            font-weight: bold;
            transition: 0.3s;
            font-size: 1.1rem;
        }

        .auth-links a:hover {
            color: #ff9800;
            text-decoration: underline;
        }

        .btn-custom {
            background: linear-gradient(90deg, #ff9800, #ff5722);
            border: none;
            color: white;
            font-size: 1rem;
            padding: 12px 24px;
            border-radius: 50px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.3);
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

    </style>
</head>
<body>

    <div class="container">
        <!-- Navigation -->
        <nav class="d-flex justify-content-end p-4 auth-links">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-tasks"></i> Dashboard</a>
                @else
                    <a href="{{ route('login') }}"><i class="fa fa-sign-in-alt"></i> Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"><i class="fa fa-user-plus"></i> Register</a>
                    @endif
                @endauth
            @endif
        </nav>

        <!-- Hero Section -->
        <div class="hero">
            <h1>Welcome to Task Management System</h1>
            <p class="lead">Organize, assign, and track tasks efficiently.</p>
            <a href="{{ route('login') }}" class="btn btn-custom"><i class="fa fa-arrow-right"></i> Get Started</a>
        </div>

        <!-- Features Section -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-glass">
                    <h2><i class="fa fa-tasks"></i> Task Assignment</h2>
                    <p>Managers assign tasks with deadlines and priorities.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-glass">
                    <h2><i class="fa fa-chart-line"></i> Task Tracking</h2>
                    <p>Users update status, and managers monitor progress.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-glass">
                    <h2><i class="fa fa-star"></i> Task Evaluation</h2>
                    <p>Managers grade users based on quality and timeliness.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-glass">
                    <h2><i class="fa fa-user-tie"></i> Role-Based Access</h2>
                    <p>Admins, managers, and users have secure access.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-glass">
                    <h2><i class="fa fa-bell"></i> Notifications</h2>
                    <p>Users receive reminders and grade updates.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-glass">
                    <h2><i class="fa fa-file-alt"></i> Reports & Analytics</h2>
                    <p>Admins generate task performance reports.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-5">
        <p>&copy; {{ date('Y') }} Task Management System. All rights reserved.</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
