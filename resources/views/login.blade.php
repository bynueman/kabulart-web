<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>login admin</title>
</head>
<body>
    <form action="{{ route('login.action') }}" method="post">
        <div class="imgcontainer">
            <img src="img/logokabul.png" alt="Avatar_login" class="avatar">
        </div>
        @csrf
        <div class="container">
            <label for="email"><b>Email</b></label>
            <input type="text" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email" name="email" required>
            @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
            <label for="password"><b>Password</b></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter Password" name="password" required>
            @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
            <button type="submit">LOGIN</button>
        </div>
    </form>
</body>
</html>