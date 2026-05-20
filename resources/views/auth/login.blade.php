<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — ClinicCare HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/tabler-icons.min.css" rel="stylesheet">
    @vite(['resources/css/premium.css'])
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 40%, #06b6d4 70%, #0284c7 100%); }
        .login-panel { max-width: 420px; width: 100%; margin: 1rem; }
        .login-card { border-radius: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
            box-shadow: 0 25px 80px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.3); }
        .brand-icon { width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; margin: 0 auto 1rem; }
    </style>
</head>
<body>
    <div class="login-panel">
        <div class="login-card p-4 p-md-5">
            <div class="brand-icon"><i class="ti ti-building-hospital"></i></div>
            <div class="text-center mb-4">
                <h1 class="h2 fw-bold mb-1">ClinicCare HMS</h1>
                <p class="text-muted mb-0">Premium Hospital ERP</p>
            </div>
            @if($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email', 'admin@cliniccare.test') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" value="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100" style="background:linear-gradient(90deg,#0ea5e9,#06b6d4);border:none">
                    <i class="ti ti-login me-1"></i> Sign In
                </button>
            </form>
            <div class="mt-4 p-3 rounded bg-light small text-muted">
                <strong>Demo:</strong> admin@cliniccare.test / password
            </div>
        </div>
    </div>
</body>
</html>
