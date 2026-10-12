@php
    $collegeName = $collegeProfile->college_name ?? config('app.name', 'College Management System');
    $storedLogo = $collegeProfile->logo ?? null;
    $logoUrl = $storedLogo && is_file(public_path($storedLogo))
        ? url($storedLogo)
        : asset('logo.png');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07162e">
    <link rel="icon" href="{{ $logoUrl }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ url('vertical/assets/css/icons.css') }}" rel="stylesheet">
    <title>Welcome | {{ $collegeName }}</title>

    <style>
        :root {
            --navy-950: #061225;
            --navy-900: #0a1c38;
            --navy-800: #123159;
            --gold-500: #c99b3c;
            --gold-400: #ddb966;
            --ink: #10213d;
            --muted: #6f788b;
            --line: #e6e8ee;
            --danger: #c74343;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100svh;
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            background: var(--navy-950);
            -webkit-font-smoothing: antialiased;
        }

        button, input { font: inherit; }

        .auth-shell {
            min-height: 100vh;
            min-height: 100svh;
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(440px, .82fr);
            overflow: hidden;
        }

        .brand-panel {
            position: relative;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(2rem, 4vw, 4.5rem);
            color: #fff;
            overflow: hidden;
            background:
                linear-gradient(115deg, rgba(4,17,37,.96), rgba(8,31,62,.86)),
                url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1600&q=85') center/cover no-repeat;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            z-index: -1;
            width: 42rem;
            height: 42rem;
            right: -18rem;
            top: -18rem;
            border: 1px solid rgba(221,185,102,.2);
            border-radius: 50%;
            box-shadow: 0 0 0 5rem rgba(221,185,102,.035), 0 0 0 10rem rgba(221,185,102,.025);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            z-index: -1;
            inset: 0;
            background:
                linear-gradient(90deg, transparent 49.85%, rgba(255,255,255,.045) 50%, transparent 50.15%),
                linear-gradient(transparent 49.85%, rgba(255,255,255,.035) 50%, transparent 50.15%);
            background-size: 180px 180px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.55), transparent 82%);
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .brand-mark {
            width: 70px;
            height: 70px;
            display: grid;
            place-items: center;
            padding: .55rem;
            border-radius: 18px;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(255,255,255,.55);
            box-shadow: 0 18px 45px rgba(0,0,0,.24);
        }

        .college-logo {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-identity span {
            display: block;
            margin-bottom: .25rem;
            color: var(--gold-400);
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .brand-identity strong {
            display: block;
            max-width: 30rem;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1rem, 1.5vw, 1.25rem);
            line-height: 1.25;
        }

        .brand-content {
            max-width: 580px;
            padding: clamp(2rem, 4.5vh, 4rem) 0;
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 0 0 1rem;
            color: var(--gold-400);
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: '';
            width: 32px;
            height: 1px;
            background: var(--gold-400);
        }

        .brand-title {
            margin: 0;
            max-width: 540px;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 2.7vw, 2.75rem);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -.02em;
            color: #ffffff;
        }

        .brand-erp {
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
            font-size: .52em;
            font-weight: 800;
            letter-spacing: .08em;
            vertical-align: middle;
            margin-left: .35rem;
            padding: .18rem .6rem;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(221,185,102,.25), rgba(201,155,60,.12));
            border: 1px solid rgba(221,185,102,.45);
            color: var(--gold-400);
            box-shadow: 0 4px 15px rgba(201,155,60,.15);
            text-transform: uppercase;
        }

        .brand-copy {
            max-width: 480px;
            margin: 1.15rem 0 0;
            color: rgba(255,255,255,.72);
            font-size: clamp(.88rem, 1vw, .96rem);
            line-height: 1.65;
        }

        .feature-row {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            margin-top: 1.6rem;
        }

        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .78rem;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 999px;
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.82);
            font-size: .74rem;
            backdrop-filter: blur(10px);
            transition: background .2s, border-color .2s;
        }

        .feature-pill:hover {
            background: rgba(255,255,255,.1);
            border-color: rgba(221,185,102,.4);
        }

        .feature-pill i { color: var(--gold-400); font-size: .92rem; }

        .brand-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
            color: rgba(255,255,255,.46);
            font-size: .72rem;
        }

        .secure-note { display: inline-flex; align-items: center; gap: .5rem; }

        .secure-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #73d6a1;
            box-shadow: 0 0 0 5px rgba(115,214,161,.1);
        }

        .form-panel {
            position: relative;
            display: grid;
            place-items: center;
            padding: clamp(1.5rem, 4vw, 5rem);
            background:
                radial-gradient(circle at 100% 0, rgba(201,155,60,.12), transparent 31%),
                linear-gradient(145deg, #fff 0%, #f7f6f1 100%);
        }

        .form-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 1px;
            height: 100%;
            background: linear-gradient(transparent, var(--gold-400), transparent);
            opacity: .65;
        }

        .login-card { width: min(100%, 445px); }
        .mobile-logo { display: none; }

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: .48rem;
            margin-bottom: 2.1rem;
            padding: .46rem .7rem;
            color: var(--navy-800);
            background: rgba(18,49,89,.065);
            border: 1px solid rgba(18,49,89,.09);
            border-radius: 999px;
            font-size: .67rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .portal-badge i { color: var(--gold-500); font-size: .9rem; }

        .login-heading {
            margin: 0;
            font-family: 'Playfair Display', serif;
            color: var(--navy-950);
            font-size: clamp(2.3rem, 3.5vw, 3.25rem);
            line-height: 1.08;
            letter-spacing: -.035em;
        }

        .login-subtitle {
            margin: .75rem 0 2.25rem;
            color: var(--muted);
            font-size: .92rem;
            line-height: 1.65;
        }

        .alert {
            display: flex;
            gap: .65rem;
            align-items: flex-start;
            margin-bottom: 1.35rem;
            padding: .85rem 1rem;
            border: 1px solid rgba(199,67,67,.17);
            border-radius: 12px;
            background: #fff5f4;
            color: #9f3131;
            font-size: .82rem;
            line-height: 1.5;
        }

        .field-group { margin-bottom: 1.2rem; }

        .field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .48rem;
        }

        .field-label {
            color: #3b465b;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .055em;
            text-transform: uppercase;
        }

        .forgot-link {
            color: var(--navy-800);
            font-size: .75rem;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-link:hover { color: var(--gold-500); }
        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #8c95a6;
            font-size: 1.08rem;
            pointer-events: none;
        }

        .field-control {
            display: block;
            width: 100%;
            height: 55px;
            padding: 0 3rem 0 2.85rem;
            color: var(--ink);
            background: rgba(255,255,255,.84);
            border: 1px solid var(--line);
            border-radius: 13px;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .field-control::placeholder { color: #a4aab6; }

        .field-control:focus {
            background: #fff;
            border-color: var(--gold-500);
            box-shadow: 0 0 0 4px rgba(201,155,60,.11), 0 10px 30px rgba(16,33,61,.06);
        }

        .field-control.is-invalid { border-color: var(--danger); }

        .toggle-password {
            position: absolute;
            right: .65rem;
            top: 50%;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            padding: 0;
            color: #8c95a6;
            background: transparent;
            border: 0;
            border-radius: 9px;
            cursor: pointer;
        }

        .toggle-password:hover { color: var(--navy-800); background: #f0f1f4; }

        .invalid-feedback {
            display: block;
            margin-top: .4rem;
            color: var(--danger);
            font-size: .75rem;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: .3rem 0 1.6rem;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: .58rem;
            color: #5f6878;
            font-size: .82rem;
            cursor: pointer;
        }

        .remember-label input {
            width: 17px;
            height: 17px;
            margin: 0;
            accent-color: var(--navy-800);
        }

        .login-button {
            width: 100%;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .7rem;
            color: #fff;
            background: linear-gradient(115deg, var(--navy-950), var(--navy-800));
            border: 0;
            border-radius: 13px;
            box-shadow: 0 14px 30px rgba(6,18,37,.2);
            font-weight: 600;
            letter-spacing: .015em;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }

        .login-button:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(6,18,37,.27); }
        .login-button:active { transform: translateY(0); }
        .login-button i { color: var(--gold-400); font-size: 1.15rem; transition: transform .2s; }
        .login-button:hover i { transform: translateX(3px); }

        .form-footer {
            margin: 2rem 0 0;
            color: #969dab;
            font-size: .72rem;
            line-height: 1.6;
            text-align: center;
        }

        @media (max-width: 1050px) {
            .auth-shell { grid-template-columns: minmax(0, .95fr) minmax(430px, 1.05fr); }
            .brand-title { font-size: clamp(2.7rem, 5vw, 4.5rem); }
            .feature-row { max-width: 430px; }
        }

        @media (max-width: 820px) {
            body { background: #f7f6f1; }
            .auth-shell { display: block; min-height: 100svh; overflow: visible; }
            .brand-panel { display: none; }
            .form-panel { min-height: 100svh; padding: 2rem 1.25rem; }
            .form-panel::before { display: none; }
            .login-card { width: min(100%, 470px); }

            .mobile-logo {
                display: flex;
                align-items: center;
                gap: .8rem;
                margin-bottom: 2.5rem;
            }

            .mobile-logo .brand-mark {
                width: 58px;
                height: 58px;
                border: 1px solid var(--line);
                border-radius: 15px;
                box-shadow: 0 10px 25px rgba(16,33,61,.09);
            }

            .mobile-logo span {
                display: block;
                margin-bottom: .15rem;
                color: var(--gold-500);
                font-size: .62rem;
                font-weight: 600;
                letter-spacing: .15em;
                text-transform: uppercase;
            }

            .mobile-logo strong {
                display: block;
                max-width: 260px;
                color: var(--navy-950);
                font-family: 'Playfair Display', serif;
                font-size: 1rem;
                line-height: 1.2;
            }

            .portal-badge { margin-bottom: 1.3rem; }
        }

        @media (max-width: 420px) {
            .form-panel { align-items: start; padding-top: 1.4rem; }
            .mobile-logo { margin-bottom: 2rem; }
            .login-heading { font-size: 2.25rem; }
            .login-subtitle { margin-bottom: 1.7rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="brand-panel" aria-label="{{ $collegeName }} introduction">
            <div class="brand-top">
                <div class="brand-mark">
                    <img src="{{ $logoUrl }}" alt="{{ $collegeName }} logo" class="college-logo">
                </div>
                <div class="brand-identity">
                    <span>College Management Suite</span>
                    <strong class="college-name">{{ $collegeName }}</strong>
                </div>
            </div>

            <div class="brand-content">
                <p class="eyebrow">Welcome to your campus portal</p>
                <h1 class="brand-title">College Management System <span class="brand-erp">ERP</span></h1>
                <p class="brand-copy">One secure place for students, staff, academics and administration—designed to keep your institution moving forward.</p>

                <div class="feature-row" aria-label="Portal features">
                    <span class="feature-pill"><i class='bx bx-user-check'></i> Student records</span>
                    <span class="feature-pill"><i class='bx bx-wallet'></i> Fee management</span>
                    <span class="feature-pill"><i class='bx bx-book-open'></i> Academics</span>
                    <span class="feature-pill"><i class='bx bx-shield-quarter'></i> Secure access</span>
                </div>
            </div>

            <div class="brand-bottom">
                <span class="secure-note"><span class="secure-dot"></span> Systems operational</span>
                <span>Academic excellence, thoughtfully managed.</span>
            </div>
        </section>

        <section class="form-panel">
            <div class="login-card">
                <div class="mobile-logo">
                    <div class="brand-mark">
                        <img src="{{ $logoUrl }}" alt="{{ $collegeName }} logo" class="college-logo">
                    </div>
                    <div>
                        <span>College Management Suite</span>
                        <strong>{{ $collegeName }}</strong>
                    </div>
                </div>

                {{-- <div class="portal-badge"><i class='bx bx-lock-alt'></i> Authorised access only</div> --}}
                <div class="portal-badge"><i class='bx bx-lock-alt'></i> {{ $collegeName }}</div>

                <h2 class="login-heading">Welcome back.</h2>
                <p class="login-subtitle">Enter your credentials to continue to the management portal.</p>

                @if(Session::has('message'))
                    <div class="alert" role="alert"><i class='bx bx-error-circle'></i> {{ Session::get('message') }}</div>
                @endif

                @if(request()->filled('gerr'))
                    <div class="alert" role="alert"><i class='bx bx-error-circle'></i> Sorry, {{ request('gerr') }} is not registered with us.</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field-group">
                        <div class="field-label-row">
                            <label for="email" class="field-label">Email address</label>
                        </div>
                        <div class="field-wrap">
                            <i class='bx bx-envelope field-icon'></i>
                            <input type="email"
                                   class="field-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="name@college.edu"
                                   value="{{ old('email') }}"
                                   autocomplete="email"
                                   required
                                   autofocus>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-group">
                        <div class="field-label-row">
                            <label for="password" class="field-label">Password</label>
                            {{-- @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                            @endif --}}
                        </div>
                        <div class="field-wrap">
                            <i class='bx bx-key field-icon'></i>
                            <input type="password"
                                   class="field-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   autocomplete="current-password"
                                   required>
                            <button type="button" class="toggle-password" aria-label="Show password" aria-pressed="false">
                                <i class='bx bx-hide'></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember-label" for="remember">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Keep me signed in</span>
                        </label>
                    </div>

                    <button type="submit" class="login-button">
                        <span>Sign in securely</span>
                        <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </form>

                <p class="form-footer">Protected institutional portal<br>&copy; {{ date('Y') }} {{ $collegeName }}</p>
            </div>
        </section>
    </main>

    <script>
        const toggle = document.querySelector('.toggle-password');
        const password = document.getElementById('password');

        toggle.addEventListener('click', function () {
            const shouldShow = password.type === 'password';
            password.type = shouldShow ? 'text' : 'password';
            this.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            this.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
            this.querySelector('i').className = shouldShow ? 'bx bx-show' : 'bx bx-hide';
        });
    </script>
</body>
</html>
