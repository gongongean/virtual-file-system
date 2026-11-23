
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register | Virtual File System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
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
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;

            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--text);

            background:
                radial-gradient(circle at 15% 15%, rgba(37,131,255,.13), transparent 35%),
                radial-gradient(circle at 85% 85%, rgba(37,131,255,.08), transparent 35%),
                var(--bg);
        }

        /* REGISTER CONTAINER */

        .register-card {
            width: 100%;
            max-width: 450px;
            padding: 30px 38px;

            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 17px;

            box-shadow:
                0 20px 55px rgba(0,0,0,.4),
                0 0 30px rgba(37,131,255,.06);

            animation: registerAppear .55s ease both;
        }

        /* HEADER */

        .register-icon {
            width: 55px;
            height: 55px;
            margin: 0 auto 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #59a7ff;
            background: rgba(37,131,255,.14);
            border: 1px solid rgba(89,167,255,.3);
            border-radius: 14px;

            font-size: 26px;
        }

        .register-heading {
            color: #f1f6ff;
            font-size: 25px;
            font-weight: 750;
            margin-bottom: 5px;
        }

        .register-subheading {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 22px;
        }

        /* FORM */

        .form-label {
            color: #dce8f8;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            height: 43px;
            padding: 9px 13px;

            color: white;
            background: #091323;

            border: 1px solid rgba(110,160,230,.22);
            border-radius: 9px;

            font-size: 13px;
            transition: all .25s ease;
        }

        .form-control::placeholder {
            color: #70839e;
        }

        .form-control:focus {
            color: white;
            background: #0c1a2e;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37,131,255,.13);
        }

        /* BUTTON */

        .register-button {
            height: 45px;
            color: white;

            background: linear-gradient(100deg, #1769ca, #2583ff);
            border: none;
            border-radius: 9px;

            font-size: 14px;
            font-weight: 650;

            box-shadow: 0 5px 17px rgba(37,131,255,.17);
            transition: all .25s ease;
        }

        .register-button:hover {
            color: white;
            background: linear-gradient(100deg, #2583ff, #4398ff);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(37,131,255,.3);
        }

        /* LOGIN LINK */

        .login-text {
            color: var(--muted);
            font-size: 12px;
        }

        .login-link {
            color: #59a7ff;
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
            transition: color .2s ease;
        }

        .login-link:hover {
            color: white;
            text-decoration: underline;
        }

        /* ERROR MESSAGES */

        .register-error {
            color: #ffb4b4;
            background: rgba(220,53,69,.1);
            border: 1px solid rgba(220,53,69,.3);
            border-radius: 9px;
            font-size: 12px;
        }

        /* FOOTER */

        .register-footer {
            color: #71839d;
            font-size: 10px;
            text-align: center;
            margin-top: 17px;
        }

        /* ANIMATION */

        @keyframes registerAppear {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* SMALL SCREENS */

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .register-card {
                padding: 27px 22px;
            }

            .register-heading {
                font-size: 23px;
            }
        }

        @media (max-height: 650px) {
            body {
                align-items: flex-start;
            }

            .register-card {
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .register-icon {
                width: 45px;
                height: 45px;
                margin-bottom: 8px;
            }

            .register-subheading {
                margin-bottom: 14px;
            }

            .register-card .mb-3 {
                margin-bottom: 10px !important;
            }

            .register-footer {
                margin-top: 10px;
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

    <div class="register-card">

        <!-- HEADER -->

        <div class="text-center mb-3">

            <div class="register-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>

            <h3 class="register-heading">
                Create Account
            </h3>

            <p class="register-subheading">
                Join the Virtual File Management System
            </p>

        </div>


        <!-- ERROR MESSAGES -->

        @if ($errors->any())
            <div class="alert register-error py-2 mb-3">

                @foreach ($errors->all() as $error)
                    <div>
                        <i class="bi bi-exclamation-circle me-1"></i>
                        {{ $error }}
                    </div>
                @endforeach

            </div>
        @endif


        <!-- REGISTRATION FORM -->

        <form method="POST" action="{{ route('register.submit') }}">

            @csrf

            <!-- FULL NAME -->

            <div class="mb-3">

                <label class="form-label">
                    Full Name
                </label>

                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Enter your full name"
                       value="{{ old('name') }}"
                       autocomplete="name"
                       required>

            </div>


            <!-- EMAIL -->

            <div class="mb-3">

                <label class="form-label">
                    Email Address
                </label>

                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="Enter your email address"
                       value="{{ old('email') }}"
                       autocomplete="email"
                       required>

            </div>


            <!-- PASSWORD -->

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Create a password"
                       autocomplete="new-password"
                       required>

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="mb-3">

                <label class="form-label">
                    Confirm Password
                </label>

                <input type="password"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Confirm your password"
                       autocomplete="new-password"
                       required>

            </div>


            <!-- REGISTER BUTTON -->

            <button type="submit"
                    class="btn register-button w-100 mt-2">

                <i class="bi bi-person-plus me-2"></i>
                Create Account

            </button>

        </form>


        <!-- LOGIN LINK -->

        <div class="text-center mt-3">

            <span class="login-text">
                Already registered?
            </span>

            <a href="{{ route('login') }}"
               class="login-link">
                Login
            </a>

        </div>


        <!-- FOOTER -->

        <div class="register-footer">
            &copy; {{ date('Y') }} Virtual File System Simulator.
            All rights reserved.
        </div>

    </div>

</body>
</html>

