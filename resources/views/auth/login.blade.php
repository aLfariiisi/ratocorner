<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Staf ERP - Ratocorner</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f7ebe5;
            color: #222222;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .brand {
            text-align: center;
            margin-bottom: 20px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .brand-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #c1753d;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(193, 117, 61, 0.3);
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #c1753d;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .brand-name {
            font-family: 'Yesteryear', cursive;
            font-size: 36px;
            color: #c1753d;
            line-height: 1;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 12px 30px rgba(34, 34, 34, 0.08);
            border: 1px solid #f0dfd5;
        }

        .card-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .card-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }

        .card-header p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #c1753d;
            box-shadow: 0 0 0 3px rgba(193, 117, 61, 0.15);
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            color: #4b5563;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #c1753d;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #a66231;
        }

        .back-link {
            margin-top: 20px;
            font-size: 12px;
            color: #6b7280;
            text-decoration: none;
        }

        .back-link:hover {
            color: #c1753d;
        }
    </style>
</head>

<body>
    @php
        try {
            $cafeSetting = \App\Models\CafeSetting::first();
        } catch (\Throwable $e) {
            $cafeSetting = null;
        }
    @endphp

    <a href="{{ route('home') }}" class="brand">
        @if($cafeSetting && $cafeSetting->logo_path)
            <img src="{{ asset('storage/' . $cafeSetting->logo_path) }}" alt="Ratocorner" class="brand-logo">
        @else
            <div class="brand-circle">R</div>
        @endif
        <span class="brand-name">{{ $cafeSetting->name ?? 'Ratocorner' }}</span>
    </a>

    <div class="card">
        <div class="card-header">
            <h2>Login Staf ERP</h2>
            <p>Khusus akses Owner, Manager, dan Kasir Ratocorner</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Staf</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control">
            </div>

            <div class="form-footer">
                <label for="remember_me" class="remember">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Ingat sesi saya</span>
                </label>
                <span style="color: #9ca3af; font-size: 11px;">Portal Internal</span>
            </div>

            <button type="submit" class="btn-submit">Masuk ke Panel ERP</button>
        </form>
    </div>

    <a href="{{ route('home') }}" class="back-link">&larr; Kembali ke Beranda</a>
</body>

</html>