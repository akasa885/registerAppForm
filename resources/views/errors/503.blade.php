@php
    $availableLocales = config('app.available_locales', []);
    $currentLocale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('maintenance.meta_title', ['app' => $appName ?? config('app.name')]) }}</title>
    <style>
        :root {
            --bg: #08131f;
            --bg-soft: #112235;
            --panel: rgba(10, 22, 36, 0.86);
            --line: rgba(163, 190, 140, 0.24);
            --accent: #a3be8c;
            --accent-soft: #d8f0c9;
            --text: #f3f7f1;
            --muted: #b5c2c7;
            --warning: #f6c177;
            --shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top left, rgba(163, 190, 140, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(246, 193, 119, 0.14), transparent 24%),
                linear-gradient(135deg, var(--bg) 0%, #0b1b2c 55%, #10263a 100%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
        }

        .shell {
            width: 100%;
            max-width: 980px;
            border: 1px solid var(--line);
            border-radius: 28px;
            overflow: hidden;
            background: var(--panel);
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .layout {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
        }

        .hero,
        .meta {
            padding: 42px;
        }

        .hero {
            border-right: 1px solid rgba(163, 190, 140, 0.14);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(246, 193, 119, 0.35);
            background: rgba(246, 193, 119, 0.1);
            color: var(--warning);
            font-size: 12px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        h1 {
            margin: 22px 0 18px;
            font-size: clamp(2.4rem, 5vw, 4.4rem);
            line-height: 0.95;
            letter-spacing: -0.04em;
        }

        .lead {
            margin: 0;
            max-width: 36rem;
            color: var(--muted);
            font-size: 1.08rem;
            line-height: 1.75;
        }

        .stamp {
            margin-top: 28px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 20px;
            background: rgba(163, 190, 140, 0.1);
            border: 1px solid rgba(163, 190, 140, 0.24);
            width: 100%;
        }

        .stamp-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent-soft));
            color: #14202c;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stamp-copy strong,
        .panel-value {
            display: block;
            color: var(--text);
        }

        .stamp-copy span,
        .panel-label,
        .helper {
            color: var(--muted);
        }

        .meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 18px;
            background: linear-gradient(180deg, rgba(7, 18, 29, 0.36), rgba(7, 18, 29, 0.08));
        }

        .panel {
            padding: 22px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .panel-label {
            font-size: 0.83rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .panel-value {
            margin-top: 10px;
            font-size: 1.45rem;
            line-height: 1.4;
        }

        .helper {
            margin-top: 10px;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .pulse {
            display: inline-flex;
            gap: 8px;
            margin-top: 26px;
        }

        .pulse span {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--accent);
            animation: pulse 1.2s infinite ease-in-out;
        }

        .pulse span:nth-child(2) {
            animation-delay: 0.18s;
        }

        .pulse span:nth-child(3) {
            animation-delay: 0.36s;
        }

        .locale-switcher {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
        }

        .locale-link,
        .locale-current {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            padding: 10px 14px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.84rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: 180ms ease;
        }

        .locale-link {
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.04);
        }

        .locale-link:hover {
            border-color: rgba(163, 190, 140, 0.4);
            background: rgba(163, 190, 140, 0.14);
        }

        .locale-current {
            color: #14202c;
            border: 1px solid transparent;
            background: linear-gradient(135deg, var(--accent), var(--accent-soft));
            font-weight: 700;
        }

        @keyframes pulse {
            0%, 80%, 100% {
                opacity: 0.2;
                transform: translateY(0);
            }

            40% {
                opacity: 1;
                transform: translateY(-5px);
            }
        }

        @media (max-width: 860px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .hero {
                border-right: 0;
                border-bottom: 1px solid rgba(163, 190, 140, 0.14);
            }
        }

        @media (max-width: 640px) {
            .hero,
            .meta {
                padding: 26px;
            }

            h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="layout">
            <div class="hero">
                <div class="eyebrow">{{ __('maintenance.badge') }}</div>
                <h1>{{ __('maintenance.heading') }}</h1>
                <p class="lead">{{ $maintenanceMessage ?? __('maintenance.default_message') }}</p>

                <div class="stamp">
                    <div class="stamp-mark">{{ strtoupper(substr($appName ?? 'A', 0, 1)) }}</div>
                    <div class="stamp-copy">
                        <strong>{{ $appName ?? 'Aplikasi' }}</strong>
                        <span>{{ __('maintenance.brand_note') }}</span>
                    </div>
                </div>

                <div class="pulse" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <h2 style="margin-top: 32px; color: var(--accent); font-size: 1.25rem;">{{ __('maintenance.apology') }}</h2>
            </div>

            <aside class="meta">
                <div class="panel">
                    <div class="panel-label">{{ __('maintenance.status_label') }}</div>
                    <div class="panel-value">{{ __('maintenance.status_value') }}</div>
                    <div class="helper">{{ __('maintenance.status_helper') }}</div>
                </div>

                <div class="panel">
                    <div class="panel-label">{{ __('maintenance.started_label') }}</div>
                    <div class="panel-value">{{ $maintenanceStartedAt ?? now()->format('d/m/Y H:i') }}</div>
                </div>

                @if(($maintenanceUnit ?? 'minutes') !== 'minutes' && !empty($maintenanceEndsAt))
                    <div class="panel">
                        <div class="panel-label">{{ __('maintenance.estimated_label') }}</div>
                        <div class="panel-value">{{ $maintenanceEndsAt }}</div>
                        <div class="helper">{{ __('maintenance.estimated_helper') }}</div>
                    </div>
                @else
                    <div class="panel">
                        <div class="panel-label">{{ __('maintenance.estimate_label') }}</div>
                        <div class="panel-value">{{ __('maintenance.asap_value') }}</div>
                        <div class="helper">{{ __('maintenance.asap_helper') }}</div>
                    </div>
                @endif
            </aside>
        </section>
    </main>
</body>
</html>
