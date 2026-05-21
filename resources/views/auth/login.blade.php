<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — ClinicCare HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    <style>
        body {
            min-height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center;
            font-family: var(--cc-font);
            background: linear-gradient(145deg, #0c4a6e 0%, #0ea5e9 35%, #06b6d4 65%, #0284c7 100%);
            position: relative; overflow: hidden;
        }
        body::before {
            content: ''; position: absolute; width: 600px; height: 600px; border-radius: 50%;
            background: rgba(255,255,255,0.08); top: -200px; right: -150px;
        }
        body::after {
            content: ''; position: absolute; width: 400px; height: 400px; border-radius: 50%;
            background: rgba(255,255,255,0.05); bottom: -100px; left: -100px;
        }
        .login-wrap { position: relative; z-index: 1; width: 100%; max-width: 440px; padding: 1.25rem; }
        .login-card {
            background: rgba(255,255,255,0.97); backdrop-filter: blur(24px);
            border-radius: 24px; padding: 2.5rem 2rem;
            box-shadow: 0 32px 64px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.4);
        }
        .login-brand {
            width: 72px; height: 72px; border-radius: 20px; margin: 0 auto 1.25rem;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2.25rem;
            box-shadow: 0 12px 32px rgba(14, 165, 233, 0.4);
        }
        .login-title { font-weight: 800; font-size: 1.65rem; text-align: center; color: #0f172a; margin-bottom: 0.25rem; }
        .login-sub { text-align: center; color: #64748b; margin-bottom: 2rem; font-size: 0.9rem; }
        .form-control-lg { border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; }
        .form-control-lg:focus { border-color: #0ea5e9; box-shadow: 0 0 0 4px rgba(14,165,233,0.12); }
        .btn-signin {
            width: 100%; padding: 0.85rem; border-radius: 12px; border: none; font-weight: 700;
            background: linear-gradient(90deg, #0ea5e9, #06b6d4); color: #fff;
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.35);
        }
        .btn-signin:hover { transform: translateY(-1px); color: #fff; box-shadow: 0 12px 28px rgba(14, 165, 233, 0.45); }
        .demo-box { background: #f0f9ff; border-radius: 12px; padding: 1rem; font-size: 0.8rem; color: #475569; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-brand"><i class="ti ti-building-hospital"></i></div>
            <h1 class="login-title">ClinicCare HMS</h1>
            <p class="login-sub">Enterprise Hospital Management System</p>
            @if($errors->any())
                <div class="alert alert-danger py-2 mb-3">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email address</label>
                    <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email', 'admin@cliniccare.test') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" value="password" required>
                </div>
                <button type="submit" class="btn btn-signin mb-3">
                    <i class="ti ti-login me-1"></i> Sign in to Dashboard
                </button>
            </form>
            <div class="demo-box">
                <strong>Demo access:</strong> admin@cliniccare.test / password<br>
                <span class="text-muted">Also: reception@ · doctor@ · pharmacy@ · accounts@</span>
            </div>
        </div>
        <p class="text-center text-white-50 small mt-4 mb-0 opacity-75">&copy; {{ date('Y') }} ClinicCare — Medical ERP</p>
    </div>
</body>
</html>
