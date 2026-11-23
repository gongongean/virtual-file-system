
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Virtual File System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg: #080e1a;
            --panel: #0d1728;
            --blue: #2583ff;
            --text: #e8f0fc;
            --muted: #91a3bd;
            --border: rgba(110, 160, 230, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 15% 15%, rgba(37,131,255,.13), transparent 35%),
                radial-gradient(circle at 85% 85%, rgba(37,131,255,.08), transparent 35%),
                var(--bg);
        }

        .login-wrapper {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .login-container {
            width: 100%;
            max-width: 920px;
            height: min(550px, calc(100svh - 24px));
            min-height: 0;
            display: flex;
            overflow: hidden;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 17px;
            box-shadow: 0 20px 55px rgba(0,0,0,.4);
            animation: loginAppear .55s ease both;
        }

        /* BRAND PANEL */

        .brand-panel {
            width: 44%;
            padding: 35px 34px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                radial-gradient(circle at top right, rgba(74,151,255,.2), transparent 40%),
                linear-gradient(145deg, #102440, #0c1b30 75%);
            border-right: 1px solid var(--border);
        }

        .brand-icon {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 23px;
            color: white;
            background: rgba(37,131,255,.16);
            border: 1px solid rgba(89,167,255,.3);
            border-radius: 14px;
            font-size: 27px;
        }

        .brand-title {
            font-size: 25px;
            font-weight: 750;
            line-height: 1.3;
        }

        .brand-description {
            margin-top: 13px;
            margin-bottom: 18px;
            color: #a9bdd8;
            line-height: 1.65;
            font-size: 13px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-top: 16px;
            color: #d7e6fa;
            font-size: 13px;
        }

        .feature-item i {
            color: #59a7ff;
            font-size: 18px;
        }

        .brand-footer {
            color: #7189a9;
            font-size: 11px;
        }

        /* LOGIN PANEL */

        .form-panel {
            width: 56%;
            padding: 35px 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-user-icon {
            color: #59a7ff;
            font-size: 32px;
            margin-bottom: 7px;
        }

        .form-heading {
            color: #f1f6ff;
            font-size: 25px;
            font-weight: 750;
            margin-bottom: 5px;
        }

        .form-subheading {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 24px;
        }

        .form-label {
            color: #dce8f8;
            font-size: 12px;
            font-weight: 600;
        }

        /* INPUTS */

        .input-group-custom {
            position: relative;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #7893b5;
            z-index: 5;
        }

        .input-group-custom .form-control {
            height: 45px;
            padding-left: 42px;
            color: white;
            background: #091323;
            border: 1px solid rgba(110,160,230,.22);
            border-radius: 9px !important;
            font-size: 13px;
            transition: all .25s ease;
        }

        .input-group-custom .form-control::placeholder {
            color: #70839e;
        }

        .input-group-custom .form-control:focus {
            color: white;
            background: #0c1a2e;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37,131,255,.13);
        }

        .password-input {
            padding-right: 45px !important;
        }

        .password-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #91a3bd;
            z-index: 6;
            cursor: pointer;
        }

        .password-toggle:hover {
            color: #59a7ff;
        }

        /* BUTTON */

        .login-button {
            height: 46px;
            color: white;
            background: linear-gradient(100deg, #1769ca, #2583ff);
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 650;
            box-shadow: 0 5px 17px rgba(37,131,255,.17);
            transition: all .25s ease;
        }

        .login-button:hover {
            color: white;
            background: linear-gradient(100deg, #2583ff, #4398ff);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(37,131,255,.3);
        }

        .register-text {
            color: var(--muted);
            font-size: 12px;
        }

        .register-link {
            color: #59a7ff;
            font-weight: 650;
            text-decoration: none;
        }

        .register-link:hover {
            color: white;
            text-decoration: underline;
        }

        .login-error {
            color: #ffb4b4;
            background: rgba(220,53,69,.1);
            border: 1px solid rgba(220,53,69,.3);
            border-radius: 9px;
            font-size: 12px;
        }

        .footer-text {
            color: #71839d;
            font-size: 10px;
            text-align: center;
            margin-top: 20px;
        }

        @keyframes loginAppear {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* TABLET AND MOBILE */

        @media (max-width: 768px) {
            .login-wrapper {
                padding: 15px;
            }

            .login-container {
                height: auto;
                min-height: 0;
                max-width: 450px;
            }

            .brand-panel {
                display: none;
            }

            .form-panel {
                width: 100%;
                padding: 35px 28px;
            }
        }

        @media (max-height: 620px) and (min-width: 769px) {
            .login-container {
                height: calc(100svh - 16px);
            }

            .brand-panel,
            .form-panel {
                padding-top: 22px;
                padding-bottom: 22px;
            }

            .brand-description {
                margin-top: 8px;
                margin-bottom: 10px;
            }

            .feature-item {
                margin-top: 10px;
            }

            .form-subheading {
                margin-bottom: 15px;
            }

            .footer-text {
                margin-top: 12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-container">

        <!-- LEFT BRANDING PANEL -->

        <div class="brand-panel">

            <div>
                <div class="brand-icon">
                    <i class="bi bi-hdd-stack-fill"></i>
                </div>

                <h2 class="brand-title">
                    Virtual File<br>
                    Management System
                </h2>

                <p class="brand-description">
                    A centralized platform for managing virtual disks,
                    organizing files, and monitoring storage allocation.
                </p>

                <div class="feature-item">
                    <i class="bi bi-hdd-stack"></i>
                    <span>Virtual Disk Management</span>
                </div>

                <div class="feature-item">
                    <i class="bi bi-folder2-open"></i>
                    <span>File and Directory Organization</span>
                </div>

                <div class="feature-item">
                    <i class="bi bi-pie-chart"></i>
                    <span>Storage Block Allocation</span>
                </div>
            </div>

            <div class="brand-footer">
                Applied Operating Systems Project
            </div>

        </div>

        <!-- RIGHT LOGIN PANEL -->

        <div class="form-panel">

            <div class="mb-3">
                <div class="form-user-icon">
                    <i class="bi bi-person-circle"></i>
                </div>

                <h3 class="form-heading">
                    Welcome Back!
                </h3>

                <p class="form-subheading">
                    Please sign in to access your account.
                </p>
            </div>

            <!-- ERROR MESSAGES -->

            @if ($errors->any())
                <div class="alert login-error py-2 mb-3">
                    @foreach ($errors->all() as $error)
                        <div>
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- LOGIN FORM -->

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label mb-2">
                        Email Address
                    </label>

                    <div class="input-group-custom">
                        <i class="bi bi-envelope input-icon"></i>

                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="Enter your email address"
                               value="{{ old('email') }}"
                               autocomplete="username"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label mb-2">
                        Password
                    </label>

                    <div class="input-group-custom">
                        <i class="bi bi-lock input-icon"></i>

                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control password-input"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required>

                        <button type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Show password">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn login-button w-100 mt-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Sign In
                </button>

            </form>

            <!-- REGISTER LINK -->

            <div class="text-center mt-3">
                <span class="register-text">
                    Don't have an account?
                </span>

                <a href="{{ route('register') }}" class="register-link small">
                    Create Account
                </a>
            </div>

            <div class="footer-text">
                &copy; {{ date('Y') }} Virtual File System Simulator.
                All rights reserved.
            </div>

        </div>

    </div>

</div>

<!-- SHOW / HIDE PASSWORD -->

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {

        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
            this.setAttribute('aria-label', 'Hide password');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
            this.setAttribute('aria-label', 'Show password');
        }

    });
</script>

</body>
</html>
