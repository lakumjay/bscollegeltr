<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access restricted | {{ $collegeProfile?->college_name ?? config('app.name', 'College Management') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@500;600&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0b1f3a;
            --navy-light: #1b416f;
            --gold: #c9a84c;
            --gold-light: #e8d080;
            --ink: #1d2b42;
            --muted: #738096;
            --surface: #f0f2f7;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 28px;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 14%, rgba(201, 168, 76, .15), transparent 25%),
                radial-gradient(circle at 88% 82%, rgba(27, 65, 111, .12), transparent 28%),
                var(--surface);
            font-family: 'DM Sans', Arial, sans-serif;
        }

        .access-card {
            width: min(900px, 100%);
            display: grid;
            grid-template-columns: minmax(280px, .82fr) minmax(360px, 1.18fr);
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(11, 31, 58, .08);
            border-radius: 24px;
            box-shadow: 0 24px 70px rgba(11, 31, 58, .16);
        }

        .access-visual {
            min-height: 430px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 42px 34px;
            color: #fff;
            text-align: center;
            background:
                radial-gradient(circle at 50% 42%, rgba(232, 208, 128, .13), transparent 32%),
                linear-gradient(145deg, var(--navy), var(--navy-light));
        }

        .permission-orbit {
            position: relative;
            width: 68px;
            height: 68px;
            display: grid;
            place-items: center;
            margin-bottom: 24px;
        }

        .permission-orbit::before {
            position: absolute;
            inset: 0;
            content: '';
            border: 2px solid rgba(255, 255, 255, .16);
            border-top-color: var(--gold-light);
            border-right-color: rgba(232, 208, 128, .58);
            border-radius: 50%;
            animation: permission-spin 1.6s linear infinite;
        }

        .permission-orbit::after {
            position: absolute;
            inset: 10px;
            content: '';
            border: 1px solid rgba(232, 208, 128, .3);
            border-radius: 50%;
            animation: permission-pulse 1.8s ease-in-out infinite;
        }

        .permission-orbit span {
            position: relative;
            z-index: 1;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            color: #33280d;
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            border-radius: 50%;
            font-size: 15px;
            font-weight: 800;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .22);
        }

        .error-code {
            margin: 0;
            color: #fff;
            font-family: 'Crimson Pro', Georgia, serif;
            font-size: clamp(76px, 10vw, 112px);
            font-weight: 600;
            letter-spacing: -.045em;
            line-height: .8;
        }

        .error-label {
            margin: 20px 0 0;
            color: var(--gold-light);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
        }

        .access-content {
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 52px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 34px;
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            overflow: hidden;
            color: var(--gold);
            background: #fffaf0;
            border: 1px solid #ead795;
            border-radius: 12px;
            font-family: 'Crimson Pro', Georgia, serif;
            font-size: 19px;
        }

        .brand-mark img { width: 100%; height: 100%; object-fit: contain; }

        .eyebrow {
            display: block;
            margin-bottom: 10px;
            color: #9b7518;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            color: var(--navy);
            font-family: 'Crimson Pro', Georgia, serif;
            font-size: clamp(34px, 5vw, 46px);
            font-weight: 600;
            line-height: 1;
        }

        .message {
            max-width: 460px;
            margin: 18px 0 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .hint {
            margin: 22px 0 0;
            padding: 13px 15px;
            color: #66511c;
            background: #fff9e9;
            border: 1px solid #ebd58f;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.55;
        }

        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 28px; }

        .button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 10px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            color: #fff;
            background: linear-gradient(125deg, var(--navy), var(--navy-light));
            border: 1px solid var(--navy);
            box-shadow: 0 8px 18px rgba(11, 31, 58, .17);
        }

        .button-secondary { color: #806315; background: #fffaf0; border: 1px solid #ddc261; }

        .button:hover { transform: translateY(-1px); }
        .button:focus-visible { outline: 3px solid rgba(201, 168, 76, .35); outline-offset: 3px; }

        @keyframes permission-spin { to { transform: rotate(360deg); } }
        @keyframes permission-pulse { 50% { transform: scale(.88); opacity: .55; } }

        @media (max-width: 720px) {
            body { padding: 16px; }
            .access-card { grid-template-columns: 1fr; }
            .access-visual { min-height: 235px; padding: 32px 24px; }
            .permission-orbit { margin-bottom: 18px; }
            .error-code { font-size: 76px; }
            .access-content { padding: 34px 28px 38px; }
            .brand { margin-bottom: 25px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .permission-orbit::before, .permission-orbit::after { animation: none; }
            .button:hover { transform: none; }
        }
    </style>
</head>
<body>
    <main class="access-card" role="alert" aria-labelledby="access-title">
        <section class="access-visual" aria-hidden="true">
            <div class="permission-orbit"><span>!</span></div>
            <p class="error-code">403</p>
            <p class="error-label">Restricted module</p>
        </section>

        <section class="access-content">
            <div class="brand">
                <span class="brand-mark">
                    @if(!empty($collegeProfile?->logo))
                        <img src="{{ url($collegeProfile->logo) }}" alt="">
                    @else
                        {{ strtoupper(substr($collegeProfile?->college_name ?? 'CM', 0, 2)) }}
                    @endif
                </span>
                <span>{{ $collegeProfile?->college_name ?? config('app.name', 'College Management') }}</span>
            </div>

            <span class="eyebrow">Access control</span>
            <h1 id="access-title">Permission required.</h1>
            <p class="message">{{ $exception->getMessage() ?: 'Your account does not currently have permission to open this module.' }}</p>
            <p class="hint">If you need access, ask an administrator to add the required module permission to your assigned role.</p>

            <div class="actions">
                <a class="button button-primary" href="{{ url('/home') }}"><span aria-hidden="true">←</span> Back to dashboard</a>
                <button class="button button-secondary" type="button" onclick="history.length > 1 ? history.back() : window.location.assign('{{ url('/home') }}')">Previous page</button>
            </div>
        </section>
    </main>
</body>
</html>
