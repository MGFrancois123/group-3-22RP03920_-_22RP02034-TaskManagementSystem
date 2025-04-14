<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome {{ $name }} to the Student's Dashboard</h1>

    <a href="{{ route('student.new') }}">Register</a>
    <a href="{{ route('student.all') }}">All Students</a>
</body>
</html>
