<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ERP System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="auth-body">
    <div class="login-card">
        <div class="auth-header">
            <h1 class="auth-title">ERP<span class="text-slate-500">System</span></h1>
            <p class="auth-subtitle">Please sign in to your account</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="admin@erp.com" required autofocus>
                @error('email')
                    <p class="text-rose-500 text-[10px] mt-1 font-bold uppercase tracking-tight">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">
                Sign In
            </button>
        </form>

        <div class="auth-footer-note">
            <span class="auth-info-label">Development Mode Credentials</span>
            <div class="flex flex-col space-y-1">
                <div class="flex justify-between">
                    <span class="text-[9px] text-slate-400 font-bold uppercase">Email</span>
                    <span class="auth-info-value">admin@erp.com</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[9px] text-slate-400 font-bold uppercase">Pass</span>
                    <span class="auth-info-value">password123</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>