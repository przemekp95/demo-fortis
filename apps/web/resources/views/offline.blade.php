<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tryb offline | Loteria Paragonowa</title>
    <meta name="theme-color" content="#020617">
    <style>
        :root {
            color-scheme: dark;
            --bg: #020617;
            --panel: rgba(15, 23, 42, 0.78);
            --panel-border: rgba(255, 255, 255, 0.12);
            --text: #f8fafc;
            --muted: #cbd5e1;
            --accent: #67e8f9;
            --success: #6ee7b7;
            --grid: rgba(148, 163, 184, 0.1);
            --shadow: 0 24px 80px rgba(2, 6, 23, 0.45);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --font-body: "Manrope", "Inter", "Segoe UI", sans-serif;
            --font-display: "Space Grotesk", "Avenir Next", "Segoe UI", sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            overflow: hidden;
            font-family: var(--font-body);
            color: var(--text);
            background:
                radial-gradient(circle at 18% 16%, rgba(34, 211, 238, 0.24), transparent 28%),
                radial-gradient(circle at 85% 25%, rgba(52, 211, 153, 0.18), transparent 24%),
                radial-gradient(circle at 10% 92%, rgba(125, 211, 252, 0.2), transparent 28%),
                linear-gradient(180deg, #020617 0%, #020617 45%, #040b1f 100%);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(to right, var(--grid) 1px, transparent 1px),
                linear-gradient(to bottom, var(--grid) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: radial-gradient(circle at center, black 30%, transparent 92%);
            opacity: 0.5;
            pointer-events: none;
        }

        .page {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 32px 20px;
        }

        .card {
            width: min(760px, 100%);
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-xl);
            background: var(--panel);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }

        .content {
            padding: 28px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border: 1px solid rgba(103, 232, 249, 0.25);
            border-radius: 999px;
            background: rgba(34, 211, 238, 0.08);
            color: #cffafe;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--success);
            box-shadow: 0 0 0 6px rgba(110, 231, 183, 0.16);
            flex: none;
        }

        .hero {
            display: grid;
            gap: 24px;
            margin-top: 22px;
        }

        .hero-copy h1 {
            margin: 0;
            font-family: var(--font-display);
            font-size: clamp(2rem, 5vw, 3.5rem);
            line-height: 0.98;
            letter-spacing: -0.04em;
        }

        .hero-copy p {
            margin: 18px 0 0;
            max-width: 44rem;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .info-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 28px;
        }

        .info-card {
            padding: 18px 18px 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            background: rgba(2, 6, 23, 0.56);
        }

        .info-card strong {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: white;
        }

        .info-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            transition:
                transform 160ms ease,
                background-color 160ms ease,
                border-color 160ms ease,
                color 160ms ease;
        }

        .button:hover,
        .button:focus-visible {
            transform: translateY(-1px);
        }

        .button-primary {
            background: var(--accent);
            color: #082f49;
        }

        .button-primary:hover,
        .button-primary:focus-visible {
            background: #a5f3fc;
        }

        .button-secondary {
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.04);
            color: white;
        }

        .button-secondary:hover,
        .button-secondary:focus-visible {
            border-color: rgba(34, 211, 238, 0.4);
            background: rgba(34, 211, 238, 0.1);
        }

        .footer {
            padding: 18px 28px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            font-size: 0.83rem;
            line-height: 1.6;
        }

        @media (max-width: 720px) {
            .content {
                padding: 22px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .footer {
                padding: 18px 22px 22px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card" aria-labelledby="offline-title">
            <div class="content">
                <div class="eyebrow">
                    <span class="status-dot" aria-hidden="true"></span>
                    Tryb offline
                </div>

                <div class="hero">
                    <div class="hero-copy">
                        <h1 id="offline-title">Brak połączenia z siecią</h1>
                        <p>
                            Interfejs aplikacji jest dostępny, ale operacje wymagające kontaktu z serwerem,
                            takie jak logowanie, rejestracja i wysyłka zgłoszeń paragonów, wrócą dopiero po
                            odzyskaniu połączenia.
                        </p>
                    </div>
                </div>

                <div class="info-grid">
                    <article class="info-card">
                        <strong>Co działa teraz</strong>
                        <p>
                            Możesz wrócić na stronę główną, odczytać zapisane zasoby i sprawdzić, czy połączenie
                            zostało już przywrócone.
                        </p>
                    </article>

                    <article class="info-card">
                        <strong>Co wymaga internetu</strong>
                        <p>
                            Formularze, konto uczestnika, publikacja nowych danych i synchronizacja wyników
                            wymagają ponownego dostępu do sieci.
                        </p>
                    </article>
                </div>

                <div class="actions">
                    <a class="button button-primary" href="/" onclick="window.location.reload(); return false;">
                        Spróbuj ponownie
                    </a>
                    <a class="button button-secondary" href="{{ route('home') }}">
                        Wróć do strony głównej
                    </a>
                </div>
            </div>

            <div class="footer">
                Ekran awaryjny PWA uruchamiany przez service workera, gdy żądanie nawigacyjne nie może
                zostać pobrane z sieci.
            </div>
        </section>
    </main>
</body>
</html>
