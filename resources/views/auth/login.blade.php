<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="{{asset('img/Fixed.ico')}}" type="image/x-icon">
    <title>Login — Fixed Asset System</title>

    @vite(['resources/css/app.css'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /*
         * Teal palette derived from #4fd1c5
         * --teal:        #4fd1c5  (primary — buttons, accents)
         * --teal-dark:   #2c9e98  (hover states)
         * --teal-deep:   #0d3535  (left panel bg)
         * --teal-ink:    #174040  (text on light teal)
         * --teal-mist:   #e6fffe  (hover bg on cards)
         * --teal-fog:    #d0f5f2  (badge bg, ring tint)
         */

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0fffe;
        }

        /* ── Left panel ── */
        .panel-left {
            width: 42%;
            min-height: 100vh;
            background: #0d3535;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        /* decorative circles */
        .panel-left::before {
            content: '';
            position: absolute;
            width: 520px; height: 520px;
            border-radius: 50%;
            border: 1px solid rgba(79,209,197,0.1);
            top: -160px; left: -160px;
        }
        .panel-left::after {
            content: '';
            position: absolute;
            width: 340px; height: 340px;
            border-radius: 50%;
            border: 1px solid rgba(79,209,197,0.08);
            bottom: -80px; right: -80px;
        }

        .panel-left .inner-ring {
            position: absolute;
            width: 260px; height: 260px;
            border-radius: 50%;
            border: 1px solid rgba(79,209,197,0.05);
            bottom: 20px; right: -60px;
        }

        /* teal glow blob behind headline */
        .panel-left .glow {
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79,209,197,0.12) 0%, transparent 70%);
            top: 38%; left: -60px;
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .brand-name {
            font-family: 'DM Serif Display', serif;
            color: #fff;
            font-size: 1.05rem;
            line-height: 1.25;
            letter-spacing: 0.01em;
        }

        .brand-name span {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.7rem;
            font-weight: 300;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
        }

        .panel-left .headline {
            position: relative;
            z-index: 1;
        }

        .headline h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 2.6rem;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .headline h2 em {
            font-style: italic;
            color: #4fd1c5;
        }

        .headline p {
            color: rgba(255,255,255,0.42);
            font-size: 0.875rem;
            font-weight: 300;
            line-height: 1.7;
            max-width: 280px;
        }

        .stat-row {
            display: flex;
            gap: 2rem;
            position: relative;
            z-index: 1;
        }

        .stat-item {
            border-top: 1px solid rgba(79,209,197,0.2);
            padding-top: 0.75rem;
        }

        .stat-item .num {
            font-family: 'DM Serif Display', serif;
            font-size: 1.6rem;
            color: #4fd1c5;
        }

        .stat-item .lbl {
            font-size: 0.7rem;
            font-weight: 300;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.32);
            margin-top: 2px;
        }

        /* ── Right panel ── */
        .panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: #fff;
        }

        .form-card {
            width: 100%;
            max-width: 400px;
        }

        .form-card .eyebrow {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #2c9e98;
            margin-bottom: 0.5rem;
        }

        .form-card h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            color: #111;
            margin-bottom: 0.4rem;
        }

        .form-card .subtitle {
            font-size: 0.875rem;
            color: #999;
            font-weight: 300;
            margin-bottom: 2.25rem;
        }

        .error-msg {
            background: #fff1f0;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .field {
            margin-bottom: 1.1rem;
        }

        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            color: #555;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
        }

        .field input {
            width: 100%;
            border: 1px solid #e2e8e8;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: #111;
            background: #fafffe;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .field input:focus {
            border-color: #4fd1c5;
            box-shadow: 0 0 0 3px rgba(79, 209, 197, 0.18);
            background: #fff;
        }

        .field input.has-error {
            border-color: #f87171;
        }

        .field .input-wrap {
            position: relative;
        }

        .field .input-wrap input {
            padding-right: 2.75rem;
        }

        .field .toggle-btn {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            cursor: pointer;
            color: #b0bfbf;
            transition: color 0.15s;
        }

        .field .toggle-btn:hover { color: #4fd1c5; }

        .field .toggle-btn svg {
            width: 18px;
            height: 18px;
        }

        .btn-login {
            width: 100%;
            background: #4fd1c5;
            color: #0d3535;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            margin-top: 0.25rem;
        }

        .btn-login:hover { background: #38c3b7; }
        .btn-login:active { transform: scale(0.99); }

        .turnstile-wrap {
            margin: 1.25rem 0;
            display: flex;
            justify-content: center;
        }

        .footer-note {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #bbb;
        }

        .footer-note strong {
            color: #2c9e98;
            font-weight: 500;
        }

        /* ── Local accounts panel ── */
        .local-panel {
            margin-top: 2.5rem;
            padding-top: 1.75rem;
            border-top: 1px solid #e8f5f5;
        }

        .local-panel .local-title {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #c5dcdc;
            margin-bottom: 1rem;
        }

        .accounts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .account-btn {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1px;
            background: #fafffe;
            border: 1px solid #e0f0ef;
            border-radius: 8px;
            padding: 8px 10px;
            cursor: pointer;
            text-align: left;
            transition: border-color 0.15s, background 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .account-btn:hover {
            background: #e6fffe;
            border-color: #4fd1c5;
        }

        .account-btn .role {
            font-size: 0.78rem;
            font-weight: 600;
            color: #2a4040;
        }

        .account-btn .email {
            font-size: 0.68rem;
            color: #2c9e98;
            word-break: break-all;
        }

        .local-footer {
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .local-footer p {
            font-size: 0.75rem;
            color: #bbb;
        }

        .local-footer p strong { color: #555; }

        .copied-badge {
            font-size: 0.75rem;
            color: #174040;
            background: #d0f5f2;
            border: 1px solid #4fd1c5;
            border-radius: 6px;
            padding: 2px 8px;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .panel-left {
                width: 100%;
                min-height: auto;
                padding: 2rem;
            }
            .headline h2 { font-size: 1.8rem; }
            .stat-row { display: none; }
        }
    </style>
</head>

<body>
    <!-- Left branding panel -->
    <div class="panel-left">
        <div class="inner-ring"></div>
        <div class="glow"></div>

        <div class="brand">
            <img src="{{ asset('img/BGC.png') }}" alt="BFC Group logo" />
            <div class="brand-name">
                BFC Group
                <span>Fixed Asset System</span>
            </div>
        </div>

        <!-- <div class="headline">
            <h2>Track every<br/><em>asset,</em><br/>everywhere.</h2>
            <p>A centralised platform for managing, monitoring, and auditing fixed assets across all business units.</p>
        </div>

        <div class="stat-row">
            <div class="stat-item">
                <div class="num">100%</div>
                <div class="lbl">Visibility</div>
            </div>
            <div class="stat-item">
                <div class="num">Real-time</div>
                <div class="lbl">Tracking</div>
            </div>
            <div class="stat-item">
                <div class="num">Audit</div>
                <div class="lbl">Ready</div>
            </div>
        </div> -->
    </div>

    <!-- Right form panel -->
    <div class="panel-right">
        <div class="form-card">
            <p class="eyebrow">Welcome back</p>
            <h1>Sign in</h1>
            <p class="subtitle">Enter your credentials to continue.</p>

            @if(request('redirect_to'))
            <div style="background:#e6fffe;border:1px solid #4fd1c5;border-radius:10px;padding:0.65rem 1rem;font-size:0.85rem;color:#174040;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;">
                <svg style="width:16px;height:16px;flex-shrink:0;color:#2c9e98" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Sign in to submit an audit entry for the scanned asset.
            </div>
            @endif

            @if ($errors->any())
            <div class="error-msg">{{ $errors->first('login') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">

                <div class="field">
                    <label for="email">Email address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="you@bfcgroup.org"
                        value="{{ old('email') }}"
                        required
                        class="{{ $errors->has('email') ? 'has-error' : '' }}"
                    />
                </div>

                <div class="field" x-data="{ show: false }">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input
                            id="password"
                            name="password"
                            :type="show ? 'text' : 'password'"
                            placeholder="••••••••"
                            required
                            class="{{ $errors->has('password') ? 'has-error' : '' }}"
                        />
                        <button type="button" class="toggle-btn" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'">
                            <svg x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="turnstile-wrap">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAAxN8pk_QXuP294_" data-callback="javascriptCallback"></div>
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" defer></script>
                </div>

                <button type="submit" class="btn-login">Sign in →</button>
            </form>

            <p class="footer-note">
                Forgot your password? Contact <strong>IT Department</strong>.
            </p>

            @if(config('auth_mode.mode') === 'local')
            <div class="local-panel" x-data="{ copied: '' }">
                <p class="local-title">Local test accounts</p>

                <div class="accounts-grid">
                    @foreach([
                        'IT Admin'      => 'admin@bfcgroup.org',
                        'Accounting'    => 'accounting@bfcgroup.org',
                        'PFC Farm Mgr'  => 'pfc.farmmanager@bfcgroup.org',
                        'BDL Farm Mgr'  => 'bdl.farmmanager@bfcgroup.org',
                        'VP Approver'   => 'vp@bfcgroup.org',
                        'HR'            => 'hr@bfcgroup.org',
                        'SME'           => 'sme@bfcgroup.org',
                        'Auditor'       => 'auditor@bfcgroup.org',
                        'Purchasing'    => 'purchasing@bfcgroup.org',
                        'PFC Div Head'  => 'pfc.divisionhead@bfcgroup.org',
                        'BDL Div Head'  => 'bdl.divisionhead@bfcgroup.org',
                    ] as $label => $email)
                        <button type="button"
                            class="account-btn"
                            @click="navigator.clipboard.writeText('{{ $email }}'); copied = '{{ $email }}'">
                            <span class="role">{{ $label }}</span>
                            <span class="email">{{ $email }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="local-footer">
                    <p>Password for all: <strong>1234</strong></p>
                    <span x-show="copied" x-cloak class="copied-badge" x-text="'✓ ' + copied.split('@')[0]"></span>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
