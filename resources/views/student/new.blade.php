<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
</head>
<body>
    <h2>Register New Student</h2>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('student.save') }}" method="POST">
        @csrf <!-- ✅ Laravel CSRF Protection -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>

        <label for="nid">National ID:</label>
        <input type="number" id="nid" name="nid" required>
        <br>

        <button type="submit">Save Student</button>
    </form>
</body>
</html>
