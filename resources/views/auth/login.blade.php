<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk ke Akun - To Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #004e92;
            --secondary-color: #000428;
            --accent-color: #0072ff;
            --text-color: #333;
            --bg-color: #f0f4f8;
            --white: #ffffff;
            --gray: #eef2f5;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
            padding: 10px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .auth-container {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 78, 146, 0.35);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 78, 146, 0.4);
        }

        .header {
            background: linear-gradient(120deg, var(--primary-color), var(--accent-color));
            padding: 35px 30px;
            text-align: center;
            color: var(--white);
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            z-index: 0;
        }

        .header h1 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 15px;
            opacity: 0.9;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }

        .form-container {
            padding: 40px 35px;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #d1d8e0;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: all 0.3s;
            background-color: var(--gray);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: #e8edf3;
            box-shadow: 0 0 0 3px rgba(0, 78, 146, 0.15);
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            font-size: 18px;
            z-index: 10;
            user-select: none;
            transition: color 0.2s;
            -webkit-tap-highlight-color: transparent;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            margin: 0;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
            white-space: nowrap;
        }

        .forgot-password:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(120deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 12px;
            color: var(--white);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 78, 146, 0.4);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(100, 100);
                opacity: 0;
            }
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #999;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e0e6ed;
        }

        .divider::before {
            margin-right: 15px;
        }

        .divider::after {
            margin-left: 15px;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 15px;
            color: #666;
        }

        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-block;
        }

        .signup-link a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }

        .error-message {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 6px;
            display: block;
        }

        .success-message {
            color: var(--success-color);
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background: rgba(46, 204, 113, 0.1);
            border-radius: 8px;
        }

        /* Toast Notification */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 350px;
        }

        .toast {
            min-width: 280px;
            padding: 16px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.35s ease forwards;
            border-left: 5px solid;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
        }

        .toast.success { 
            border-left-color: var(--success-color); 
            background: #e8f5e9;
        }
        .toast.error { 
            border-left-color: var(--error-color); 
            background: #ffebee;
        }
        .toast i {
            font-size: 20px;
        }
        .toast.success i { color: var(--success-color); }
        .toast.error i { color: var(--error-color); }

        .toast.hide {
            animation: slideOut 0.3s ease forwards;
        }

        @keyframes slideIn {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(120%); opacity: 0; }
        }

        /* Responsive Design - Mobile First */
        @media (max-width: 480px) {
            body {
                padding: 10px;
                min-height: auto;
            }

            .auth-container {
                max-width: 100%;
                border-radius: 16px;
                margin: 0 auto;
            }

            .header {
                padding: 25px 20px;
            }

            .header h1 {
                font-size: 24px;
                line-height: 1.2;
            }

            .header p {
                font-size: 13px;
            }

            .form-container {
                padding: 30px 25px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 6px;
            }

            .form-control {
                padding: 12px 14px;
                font-size: 14px;
                border-radius: 10px;
            }

            .password-toggle {
                font-size: 16px;
                right: 14px;
            }

            .remember-forgot {
                margin-bottom: 20px;
                font-size: 13px;
            }

            .remember input {
                width: 14px;
                height: 14px;
            }

            .btn {
                padding: 16px;
                font-size: 16px;
                border-radius: 10px;
            }

            .divider {
                margin: 25px 0;
                font-size: 13px;
            }

            .divider::before,
            .divider::after {
                height: 0.8px;
            }

            .signup-link {
                margin-top: 15px;
                font-size: 14px;
            }

            #toast-container {
                top: 15px;
                right: 15px;
                max-width: 90%;
            }

            .toast {
                min-width: auto;
                width: 100%;
                max-width: 320px;
                font-size: 14px;
                padding: 14px 16px;
            }

            .toast i {
                font-size: 18px;
            }
        }

        @media (max-width: 360px) {
            .header h1 {
                font-size: 22px;
            }

            .header p {
                font-size: 12px;
            }

            .form-container {
                padding: 25px 20px;
            }

            .form-control {
                padding: 11px 12px;
                font-size: 13px;
            }

            .btn {
                padding: 15px;
                font-size: 15px;
            }

            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .forgot-password {
                margin-left: 0;
                display: block;
            }
        }

        /* Tablet Optimization */
        @media (min-width: 481px) and (max-width: 768px) {
            body {
                padding: 20px;
            }

            .auth-container {
                max-width: 450px;
                border-radius: 18px;
            }

            .header {
                padding: 30px 25px;
            }

            .header h1 {
                font-size: 26px;
            }

            .form-container {
                padding: 35px 30px;
            }

            .btn {
                padding: 15px;
            }
        }

        /* Small Desktop */
        @media (min-width: 769px) and (max-width: 1024px) {
            body {
                padding: 30px;
            }

            .auth-container {
                max-width: 400px;
                border-radius: 18px;
            }

            .header {
                padding: 32px 28px;
            }
        }

        /* Desktop Optimization */
        @media (min-width: 1025px) {
            body {
                padding: 40px;
            }

            .auth-container {
                max-width: 420px;
                border-radius: 20px;
            }
        }

        /* High DPI Screens */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .header h1 {
                letter-spacing: -0.3px;
            }
        }

        /* Accessibility - Reduce Motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Auth Container -->
    <div class="auth-container">
        <div class="header">
            <h1>👋 Selamat Datang</h1>
            <p>Masuk untuk mengelola catatan Anda</p>
        </div>
        
        <div class="form-container">
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form" autocomplete="off">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="email"
                           autocapitalize="none"
                           autocorrect="off"
                           spellcheck="false"
                           class="form-control"
                           placeholder="contoh@email.com">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="password-group">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               class="form-control"
                               placeholder="••••••••">
                        <span class="password-toggle" id="toggle-password" role="button" tabindex="0">👁️</span>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="remember-forgot">
                    <label class="remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} id="remember">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">
                        Lupa kata sandi?
                    </a>
                    @endif
                </div>
                
                <button type="submit" class="btn" id="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
            
            <div class="divider">ATAU</div>
            
            <div class="signup-link">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
        </div>
    </div>

    <script>
        // Password Toggle
        document.getElementById('toggle-password').addEventListener('click', function(e) {
            e.preventDefault();
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                icon.textContent = '👁️';
            }
            
            // Focus back to input
            passwordInput.focus();
        });

        // Also support keyboard enter on password toggle
        document.getElementById('toggle-password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });

        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'polite');
            
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            toast.innerHTML = `
                <i class="${icon}"></i>
                <span>${message}</span>
            `;
            
            container.appendChild(toast);
            
            // Auto dismiss after 3.5 seconds
            setTimeout(() => {
                toast.classList.add('hide');
                toast.addEventListener('animationend', () => {
                    toast.remove();
                }, {once: true});
            }, 3500);
        }

        // Show Laravel session flash messages as toast
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @elseif(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Form submission with loading state
        document.getElementById('login-form')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('login-btn');
            
            // Validate form first
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!email || !password) {
                e.preventDefault();
                showToast('Silakan isi email dan password', 'error');
                return;
            }
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;
            
            // Re-enable button after 5 seconds if no response
            setTimeout(() => {
                if (btn.disabled) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }, 5000);
        });

        // Auto-focus on error fields
        @if($errors->any())
            window.addEventListener('DOMContentLoaded', () => {
                const firstError = document.querySelector('.error-message');
                if (firstError) {
                    // Find the input field
                    const input = firstError.previousElementSibling;
                    if (input && input.tagName === 'INPUT') {
                        input.focus();
                    }
                    
                    // Scroll to error
                    setTimeout(() => {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center',
                            inline: 'nearest'
                        });
                    }, 100);
                }
            });
        @endif

        // Prevent zoom on iOS when focusing inputs
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, { passive: false });

        // Handle orientation change
        window.addEventListener('orientationchange', function() {
            // Force reflow to fix layout issues
            setTimeout(() => {
                document.body.style.display = 'none';
                document.body.offsetHeight;
                document.body.style.display = '';
            }, 100);
        });
    </script>
</body>
</html>