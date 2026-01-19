<!DOCTYPE html>
<html>
<head>
    <title>Register - To Do List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
                body { font-family: Arial, sans-serif; font-weight: bold; background: #13162d; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form { background: linear-gradient(to bottom, #091b41, #767e99);color: rgb(255, 255, 255); padding: 2rem; border-radius: 30px; box-shadow: 0 2px 10px rgb(114, 132, 167); width: 100%; max-width: 400px; }
        input { width: 100%; padding: 0.75rem; margin: 0.5rem 0; border: 1px solid #ddd; border-radius: 12px; box-sizing: border-box; }
        button { font-weight: bold; width: 100%; padding: 0.75rem; background: #1a2055; color: rgb(255, 255, 255); border: none; border-radius: 4px; cursor: pointer; margin-top: 1rem; }
        button:hover { background: #394175; }
        a { color: #091546; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form">
        <h2 style="text-align: center;">Daftar Akun</h2>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <input type="text" name="name" placeholder="Nama Lengkap" required value="{{ old('name') }}">
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
            <input type="password" name="password" placeholder="Password (min 6 karakter)" required minlength="6">
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
            <button type="submit">Daftar</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            Sudah punya akun? <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</body>
</html>