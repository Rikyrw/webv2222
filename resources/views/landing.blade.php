<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenPoint - Bank Sampah Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #2d6a4f;
            --secondary-green: #40916c;
            --light-green: #52b788;
            --accent-green: #74c69d;
            --dark-green: #1b4332;
            --soft-green: #edf7f1;
            --page-bg: #ffffff;
            --section-bg: #f7faf8;
            --border-soft: #e4ece7;
            --text-main: #102018;
            --text-muted: #68766d;
            --shadow-soft: 0 16px 40px rgba(16, 32, 24, 0.08);
            --shadow-hover: 0 20px 48px rgba(45, 106, 79, 0.14);
            --radius-card: 14px;
            --radius-control: 10px;
            --transition-fast: 180ms ease;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 86px;
        }

        body {
            margin: 0;
            background: var(--page-bg);
            color: var(--text-main);
            font-family: Inter, "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
            text-rendering: optimizeLegibility;
        }

        a {
            text-decoration: none;
        }

        img {
            max-width: 100%;
        }

        .container {
            --bs-gutter-x: 1.5rem;
        }

        .navbar-landing {
            min-height: 72px;
            padding: 0;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid var(--border-soft);
            box-shadow: none;
            backdrop-filter: blur(12px) saturate(140%);
            -webkit-backdrop-filter: blur(12px) saturate(140%);
            transition: background-color var(--transition-fast), box-shadow var(--transition-fast), border-color var(--transition-fast);
        }

        .navbar-landing.is-scrolled {
            background: rgba(255, 255, 255, 0.98);
            border-bottom-color: #dfe7e1;
            box-shadow: 0 10px 28px rgba(16, 32, 24, 0.06);
        }

        .landing-nav-shell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 1rem 1.5rem;
        }

        .navbar-landing .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            order: 1;
            color: var(--primary-green) !important;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 0;
            transition: opacity var(--transition-fast), transform var(--transition-fast);
        }

        .navbar-landing .navbar-brand:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .brand-logo {
            width: 30px;
            height: 30px;
            object-fit: contain;
            opacity: 0.96;
        }

        .navbar-landing .navbar-toggler {
            border: 1px solid rgba(45, 106, 79, 0.18);
            border-radius: 10px;
            padding: 0.45rem 0.6rem;
            color: var(--primary-green);
            box-shadow: none;
        }

        .navbar-landing .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.12);
        }

        .navbar-landing .navbar-toggler-icon {
            filter: none;
        }

        .landing-nav-menu {
            order: 2;
            flex-grow: 0;
            margin-left: auto;
        }

        .navbar-landing .navbar-nav {
            align-items: center;
            gap: 1.75rem;
        }

        .navbar-landing .nav-link {
            position: relative;
            color: #33443a !important;
            border: 0;
            border-radius: 0;
            padding: 0 !important;
            font-size: 0.88rem;
            font-weight: 650;
            transition: color var(--transition-fast), transform var(--transition-fast);
        }

        .navbar-landing .nav-link:hover {
            color: var(--primary-green) !important;
            background: transparent;
            transform: translateY(-1px);
        }

        .navbar-landing .nav-link:active {
            color: var(--primary-green) !important;
            background: transparent;
            font-weight: 800;
            transform: none;
        }

        .nav-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            order: 3;
            margin-left: 1.5rem;
        }

        .btn-nav-primary {
            min-height: 40px;
            padding: 0.58rem 0.95rem;
            border: 1px solid transparent;
            border-radius: 10px;
            background: var(--primary-green);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(45, 106, 79, 0.16);
            font-size: 0.9rem;
            font-weight: 750;
        }

        .btn-nav-primary:hover,
        .btn-nav-primary:focus {
            background: var(--dark-green);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(45, 106, 79, 0.2);
        }

        .btn-nav-primary:active {
            transform: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: var(--radius-control);
            font-weight: 750;
            line-height: 1.2;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast), border-color var(--transition-fast), background-color var(--transition-fast), color var(--transition-fast);
        }

        .btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(82, 183, 136, 0.22);
        }

        .btn-primary-green,
        .btn-hero,
        .btn-submit {
            background: var(--primary-green);
            border: 1px solid var(--primary-green);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(45, 106, 79, 0.2);
        }

        .btn-primary-green:hover,
        .btn-hero:hover,
        .btn-submit:hover {
            background: var(--dark-green);
            border-color: var(--dark-green);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(45, 106, 79, 0.24);
        }

        .btn-hero {
            min-height: 48px;
            padding: 0.86rem 1.25rem;
            border-radius: 999px;
            font-size: 0.96rem;
        }

        .btn-outline-clean {
            min-height: 42px;
            padding: 0.72rem 1rem;
            border: 1px solid rgba(45, 106, 79, 0.18);
            background: rgba(255, 255, 255, 0.84);
            color: var(--primary-green);
            box-shadow: none;
        }

        .btn-outline-clean:hover {
            border-color: rgba(45, 106, 79, 0.34);
            background: var(--soft-green);
            color: var(--primary-green);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(45, 106, 79, 0.1);
        }

        .hero-section {
            position: relative;
            min-height: 640px;
            margin-top: 72px;
            padding: clamp(5.5rem, 9vw, 8rem) 0 clamp(4.5rem, 8vw, 7rem);
            overflow: hidden;
            color: #ffffff;
            background:
                linear-gradient(90deg, rgba(12, 31, 23, 0.84) 0%, rgba(27, 67, 50, 0.67) 44%, rgba(27, 67, 50, 0.24) 74%, rgba(255, 255, 255, 0.08) 100%),
                url("{{ asset('images/bg-gunung.png') }}") center / cover no-repeat;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: min(68vh, 620px);
            background: linear-gradient(
                180deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.24) 18%,
                rgba(255, 255, 255, 0.74) 42%,
                rgba(255, 255, 255, 0.96) 68%,
                #ffffff 100%
            );
            pointer-events: none;
            z-index: 1;
        }

        .hero-content,
        .hero-media {
            position: relative;
            z-index: 2;
        }

        .hero-section > .container {
            position: relative;
            z-index: 2;
        }

        .hero-section > .container > .row {
            position: relative;
            z-index: 2;
            transform: translateY(-104px);
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding: 0.4rem 0.72rem;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.8rem;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }

        .hero-title {
            max-width: 640px;
            margin-bottom: 1.25rem;
            font-size: clamp(2.55rem, 6vw, 5rem);
            font-weight: 850;
            line-height: 0.98;
            letter-spacing: 0;
        }

        .hero-title .accent-text {
            color: #b7f4cd;
        }

        .hero-subtitle {
            max-width: 560px;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.86);
            font-size: clamp(1rem, 2vw, 1.18rem);
            line-height: 1.72;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex-wrap: wrap;
        }

        .hero-actions .btn-outline-clean {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.26);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .hero-actions .btn-outline-clean:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.42);
        }

        .hero-image-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            min-height: 430px;
            pointer-events: none;
        }

        .hero-image {
            width: min(670px, 60vw);
            margin-right: -10vw;
            filter: drop-shadow(0 28px 34px rgba(0, 0, 0, 0.22));
        }

        .hero-image img {
            display: block;
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .section-shell {
            padding: clamp(4.5rem, 8vw, 6.5rem) 0;
            background: #ffffff;
        }

        .section-soft {
            background: var(--section-bg);
        }

        .section-title {
            max-width: 680px;
            margin: 0 auto 3rem;
            text-align: center;
        }

        .section-title h2 {
            margin-bottom: 0.8rem;
            color: var(--primary-green);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 850;
            line-height: 1.08;
            letter-spacing: 0;
        }

        .section-title p {
            margin: 0 auto;
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .feature-card,
        .goal-card,
        .team-member,
        .contact-form,
        .contact-info-panel {
            height: 100%;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-card);
            box-shadow: 0 10px 28px rgba(16, 32, 24, 0.05);
        }

        .feature-card,
        .goal-card,
        .team-member,
        .contact-info-item,
        .accordion-item {
            transition: transform var(--transition-fast), box-shadow var(--transition-fast), border-color var(--transition-fast), background-color var(--transition-fast);
        }

        .feature-card:hover,
        .goal-card:hover,
        .team-member:hover,
        .accordion-item:hover {
            border-color: rgba(45, 106, 79, 0.24);
            box-shadow: var(--shadow-hover);
            transform: translateY(-4px);
        }

        .feature-card {
            padding: 2rem;
        }

        .feature-icon,
        .goal-icon,
        .contact-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: var(--soft-green);
            color: var(--primary-green);
            border: 1px solid rgba(45, 106, 79, 0.12);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 1.35rem;
            border-radius: 14px;
        }

        .feature-card img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .feature-card h4,
        .goal-card h4,
        .member-info h4,
        .contact-info-item h5 {
            color: var(--primary-green);
            font-weight: 800;
            letter-spacing: 0;
        }

        .feature-card h4 {
            margin-bottom: 0.75rem;
            font-size: 1.08rem;
        }

        .feature-card p,
        .goal-card p,
        .contact-info-item p,
        footer p {
            color: var(--text-muted);
            line-height: 1.72;
        }

        .goal-card {
            position: relative;
            padding: 1.65rem;
            overflow: hidden;
        }

        .goal-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 3px;
            background: var(--primary-green);
        }

        .goal-icon {
            width: 52px;
            height: 52px;
            margin-bottom: 1.2rem;
            border-radius: 12px;
            font-size: 1.25rem;
        }

        .goal-card h4 {
            margin-bottom: 0.75rem;
            font-size: 1.02rem;
            line-height: 1.35;
        }

        .goal-card p {
            margin: 0;
            font-size: 0.93rem;
        }

        .faq-section .accordion {
            display: grid;
            gap: 0.85rem;
        }

        .accordion-item {
            overflow: hidden;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-card) !important;
            box-shadow: 0 10px 28px rgba(16, 32, 24, 0.04);
        }

        .accordion-button {
            gap: 0.65rem;
            padding: 1.1rem 1.25rem;
            background: #ffffff;
            color: var(--text-main);
            border: 0;
            font-size: 0.98rem;
            font-weight: 750;
            line-height: 1.35;
            box-shadow: none;
        }

        .accordion-button i {
            display: none;
        }

        .accordion-button:not(.collapsed) {
            background: #ffffff;
            color: var(--primary-green);
            box-shadow: inset 0 -1px 0 var(--border-soft);
        }

        .accordion-button:focus {
            border-color: transparent;
            box-shadow: 0 0 0 4px rgba(82, 183, 136, 0.16);
        }

        .accordion-body {
            padding: 1.25rem;
            color: var(--text-muted);
            line-height: 1.76;
        }

        .team-member {
            overflow: hidden;
            text-align: left;
        }

        .member-img {
            aspect-ratio: 4 / 5;
            overflow: hidden;
            background: var(--soft-green);
            border-bottom: 1px solid var(--border-soft);
        }

        .member-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            transition: transform 240ms ease;
        }

        .team-member:hover .member-img img {
            transform: scale(1.035);
        }

        .member-info {
            padding: 1.15rem 1.2rem 1.25rem;
        }

        .member-info h4 {
            margin-bottom: 0.25rem;
            font-size: 1.02rem;
        }

        .member-info span {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 650;
        }

        .contact-info-panel {
            display: grid;
            gap: 1rem;
            padding: 1.35rem;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid transparent;
            border-radius: 12px;
        }

        .contact-info-item:hover {
            border-color: var(--border-soft);
            background: var(--section-bg);
            transform: translateY(-2px);
        }

        .contact-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            font-size: 1rem;
        }

        .contact-info-item h5 {
            margin-bottom: 0.35rem;
            font-size: 0.95rem;
        }

        .contact-info-item p {
            margin: 0;
            font-size: 0.9rem;
        }

        .contact-form {
            padding: clamp(1.35rem, 4vw, 2.25rem);
            box-shadow: var(--shadow-soft);
        }

        .form-label {
            color: var(--primary-green);
            font-size: 0.9rem;
            font-weight: 750;
        }

        .form-control {
            min-height: 46px;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-control);
            background: #fbfdfc;
            color: var(--text-main);
            font-size: 0.95rem;
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast), background-color var(--transition-fast);
        }

        textarea.form-control {
            min-height: 140px;
        }

        .form-control:focus {
            border-color: rgba(45, 106, 79, 0.42);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(82, 183, 136, 0.16);
        }

        .btn-submit {
            min-height: 48px;
            width: 100%;
            border-radius: var(--radius-control);
            font-size: 0.96rem;
        }

        footer {
            padding: 3.5rem 0 1.5rem;
            background: #f7faf8;
            border-top: 1px solid var(--border-soft);
            color: var(--text-main);
        }

        footer h5 {
            margin-bottom: 1.15rem;
            color: var(--text-main);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        footer p {
            color: var(--text-muted);
            margin-bottom: 0;
        }

        footer a {
            display: inline-flex;
            color: var(--text-muted);
            font-weight: 650;
            transition: color var(--transition-fast), transform var(--transition-fast), background-color var(--transition-fast);
        }

        footer a:hover {
            color: var(--primary-green);
            transform: translateY(-1px);
        }

        footer li + li {
            margin-top: 0.8rem;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary-green);
            font-size: 1.35rem;
            font-weight: 850;
            letter-spacing: 0;
        }

        .footer-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .footer-description {
            max-width: 360px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(140px, 1fr));
            gap: 2rem;
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-bottom span {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .social-links a {
            width: 28px;
            height: 28px;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--text-muted);
        }

        .social-links a:hover {
            background: #edf7f1;
            color: var(--primary-green);
        }

        .footer-divider {
            border-color: var(--border-soft);
            opacity: 1;
            margin: 2rem 0 1.3rem;
        }

        .footer-love {
            color: var(--accent-green);
        }

        @media (max-width: 991.98px) {
            .landing-nav-shell {
                gap: 0.85rem;
            }

            .landing-nav-menu {
                order: 4;
                width: 100%;
                margin-left: 0;
            }

            .navbar-landing .navbar-collapse {
                margin-top: 0.85rem;
                padding: 0.85rem;
                border: 1px solid rgba(228, 236, 231, 0.86);
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.9);
                box-shadow: 0 18px 42px rgba(16, 32, 24, 0.12);
                backdrop-filter: blur(18px) saturate(140%);
                -webkit-backdrop-filter: blur(18px) saturate(140%);
            }

            .navbar-landing .navbar-nav {
                align-items: stretch;
                gap: 0;
            }

            .navbar-landing .nav-link {
                display: block;
                padding: 0.7rem 0.85rem !important;
                border-radius: 8px;
            }

            .navbar-landing .nav-link:hover {
                background: var(--soft-green);
                transform: none;
            }

            .nav-actions {
                margin-left: auto;
            }

            .nav-actions .btn {
                width: auto;
            }

            .hero-section {
                min-height: auto;
                padding-top: 5rem;
            }

            .hero-section > .container > .row {
                transform: translateY(-48px);
            }

            .hero-image-container {
                min-height: auto;
                justify-content: center;
                margin-top: 2rem;
            }

            .hero-image {
                width: min(430px, 86vw);
                margin-right: -8vw;
                opacity: 0.94;
            }
        }

        @media (max-width: 767.98px) {
            html {
                scroll-padding-top: 74px;
            }

            .navbar-landing {
                min-height: 66px;
            }

            .hero-section {
                margin-top: 66px;
                padding: 4.3rem 0 4.8rem;
                background:
                    linear-gradient(180deg, rgba(12, 31, 23, 0.86) 0%, rgba(27, 67, 50, 0.68) 58%, rgba(27, 67, 50, 0.36) 100%),
                    url("{{ asset('images/bg-gunung.png') }}") center / cover no-repeat;
            }

            .hero-section > .container > .row {
                transform: none;
            }

            .hero-actions,
            .hero-actions .btn {
                width: 100%;
            }

            .section-title {
                margin-bottom: 2rem;
                text-align: left;
            }

            .section-title p {
                margin-left: 0;
            }

            .feature-card,
            .goal-card {
                padding: 1.35rem;
            }

            .contact-info-panel {
                padding: 0.8rem;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .footer-bottom {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    @php
    if (!isset($BASE_URL)) {
    $BASE_URL = url('/');
    }
    @endphp

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-landing fixed-top">
        <div class="container landing-nav-shell">
            <a class="navbar-brand" href="#hero">
                <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo" class="brand-logo">
                GreenPoint
            </a>

            <div class="nav-actions">
                <a href="{{ route('nasabah.login') }}" class="btn btn-nav-primary">
                    Sign In
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-sticky" aria-controls="navbar-sticky" aria-expanded="false" aria-label="Buka menu navigasi">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse landing-nav-menu" id="navbar-sticky">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#hero">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tujuan">Tujuan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#team">Tim</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-duration="800">
                    <h1 class="hero-title">
                        Kelola Sampah,<br><span class="accent-text">Raih Manfaat</span>
                    </h1>
                    <p class="hero-subtitle">
                        Platform bank sampah digital yang mengubah sampah Anda menjadi nilai ekonomi untuk masa depan yang berkelanjutan.
                    </p>
                    <div class="hero-actions">
                        @if (Route::has('nasabah.register'))
                        <a href="{{ route('nasabah.register') }}" class="btn btn-hero">
                            Mulai Sekarang
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        @elseif (Route::has('nasabah.login'))
                        <a href="{{ route('nasabah.login') }}" class="btn btn-hero">
                            Mulai Sekarang
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        @else
                        <a href="{{ rtrim($BASE_URL, '/') }}/nasabah/login" class="btn btn-hero">
                            Mulai Sekarang
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 hero-media" data-aos="fade-left" data-aos-duration="800">
                    <div class="hero-image-container">
                        <div class="hero-image">
                            <img src="{{ asset('images/tangan1.png') }}" 
                                alt="Tangan memegang bibit tanaman"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-shell features-section" data-aos="fade-up">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="800">
                <h2>Mengapa Memilih GreenPoint?</h2>
                <p>Fitur-fitur unggulan yang membuat pengelolaan sampah lebih mudah dan menguntungkan</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <img src="{{ asset('images/Recycle.png') }}" alt="Feature 1">
                        </div>
                        <h4>Sampah Bernilai</h4>
                        <p>Tukarkan sampah Anda dengan uang tunai atau poin yang dapat ditukar dengan berbagai hadiah menarik!</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <img src="{{ asset('images/dolar.png') }}" alt="Feature 2">
                        </div>
                        <h4>Menabung Mudah</h4>
                        <p>Sistem tabungan digital yang fleksibel dengan bunga menarik untuk setiap setoran sampah Anda!</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <img src="{{ asset('images/daun.png') }}" alt="Feature 3">
                        </div>
                        <h4>Lingkungan Hijau</h4>
                        <p>Bersama kita wujudkan bumi yang lebih bersih dan hijau untuk generasi masa depan!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Goals Section -->
    <section id="tujuan" class="section-shell section-soft tujuan-section" data-aos="fade-up">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="800">
                <h2>Tujuan Kami</h2>
                <p>Komitmen kami dalam mengubah sampah menjadi aset berharga</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="goal-card">
                        <div class="goal-icon">
                            <i class="fas fa-tree"></i>
                        </div>
                        <h4>Pelestarian Lingkungan</h4>
                        <p>Mengurangi dampak negatif sampah terhadap lingkungan melalui pengelolaan yang bertanggung jawab.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="goal-card">
                        <div class="goal-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h4>Pemberdayaan Ekonomi</h4>
                        <p>Memberikan nilai ekonomi dari sampah dan meningkatkan pendapatan masyarakat.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="goal-card">
                        <div class="goal-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h4>Peningkatan Kesadaran</h4>
                        <p>Meningkatkan kesadaran masyarakat tentang pentingnya pengelolaan sampah berkelanjutan.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="goal-card">
                        <div class="goal-icon">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <h4>Ekonomi Sirkular</h4>
                        <p>Mengubah sampah menjadi sumber daya berharga melalui prinsip ekonomi sirkular.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="section-shell faq-section" data-aos="fade-up">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="800">
                <h2>Pertanyaan Umum</h2>
                <p>Temukan jawaban untuk pertanyaan seputar GreenPoint</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion" data-aos="fade-up" data-aos-delay="100">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="fas fa-circle me-2"></i> Apa Itu Bank Sampah?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Bank sampah adalah sistem pengelolaan sampah dengan prinsip 3R (Reduce, Reuse, Recycle) yang melibatkan partisipasi aktif masyarakat. Masyarakat dapat menabung sampah yang telah dipilah dan mendapatkan imbalan berupa uang atau poin yang dapat ditukarkan dengan barang kebutuhan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="fas fa-circle me-2"></i> Bagaimana Cara Bergabung?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Untuk bergabung dengan GreenPoint, Anda dapat mendaftar melalui website kami atau mengunjungi kantor bank sampah terdekat. Proses pendaftaran mudah, gratis, dan hanya membutuhkan beberapa data pribadi dasar.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="fas fa-circle me-2"></i> Sampah Apa Saja yang Diterima?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Kami menerima berbagai jenis sampah yang dapat didaur ulang seperti plastik, kertas, kaca, logam, dan elektronik. Setiap jenis sampah memiliki nilai poin yang berbeda berdasarkan jenis dan beratnya.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <i class="fas fa-circle me-2"></i> Bagaimana Sistem Poin Bekerja?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sistem poin berdasarkan jenis, berat, dan kondisi sampah. Poin yang terkumpul dapat ditukarkan dengan uang tunai atau barang kebutuhan melalui katalog GreenPoint. Semakin banyak sampah yang Anda setorkan, semakin banyak keuntungan yang Anda dapatkan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="section-shell section-soft team-section" data-aos="fade-up">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="800">
                <h2>Tim Kami</h2>
                <p>Kenali tim yang membantu GreenPoint berjalan lebih rapi, mudah, dan bermanfaat.</p>
            </div>

            <div class="row justify-content-center g-4">
                <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="{{ asset('images/aditya.jpeg') }}" alt="Aditya Fadni">
                        </div>
                        <div class="member-info">
                            <h4>Aditya Fadni</h4>
                            <span>Master</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="{{ asset('images/riky.jpeg') }}" alt="Riky Rio">
                        </div>
                        <div class="member-info">
                            <h4>Riky Rio</h4>
                            <span>Frontend & Backend</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-member">
                        <div class="member-img profile-agung">
                            <img src="{{ asset('images/agung.jpeg') }}" alt="Rizki Agung">
                        </div>

                        <div class="member-info">
                            <h4>Rizki Agung</h4>
                            <span>Frontend</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="{{ asset('images/dhimas.jpeg') }}" alt="Dhimas">
                        </div>
                        <div class="member-info">
                            <h4>Dhimas Ananta</h4>
                            <span>UI/UX</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-shell contact-section" data-aos="fade-up">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="800">
                <h2>Hubungi Kami</h2>
                <p>Jangan ragu untuk menghubungi kami untuk informasi lebih lanjut</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="contact-info-panel">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5>Alamat</h5>
                                <p>Jl. Lingkungan Hijau No. 123<br>Kelurahan Bersih, Kecamatan Asri<br>Kota Hijau, 12345</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h5>Telepon</h5>
                                <p>(021) 1234-5678<br>0812-3456-7890 (WhatsApp)</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5>Email</h5>
                                <p>info@greenpoint.id<br>cs@greenpoint.id</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5>Jam Operasional</h5>
                                <p>Senin - Jumat: 08.00 - 17.00 WIB<br>Sabtu: 08.00 - 14.00 WIB<br>Minggu & Hari Libur: Tutup</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Subjek</label>
                                <input type="text" name="subject" class="form-control" placeholder="Masukkan subjek" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Pesan</label>
                                <textarea class="form-control" name="message" rows="5" placeholder="Masukkan pesan Anda" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="d-md-flex justify-content-between gap-5">
                <div class="mb-5 mb-md-0">
                    <a href="#hero" class="footer-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo">
                        <span>GreenPoint</span>
                    </a>
                    <p class="footer-description mt-3">Platform bank sampah digital yang mengubah sampah Anda menjadi nilai ekonomi untuk masa depan berkelanjutan.</p>
                </div>

                <div class="footer-grid">
                    <div>
                        <h5>Menu</h5>
                        <ul class="list-unstyled mb-0">
                            <li><a href="#hero">Beranda</a></li>
                            <li><a href="#features">Fitur</a></li>
                            <li><a href="#tujuan">Tujuan</a></li>
                            <li><a href="#faq">FAQ</a></li>
                        </ul>
                    </div>

                    <div>
                        <h5>Layanan</h5>
                        <ul class="list-unstyled mb-0">
                            <li><a href="#">Setoran Sampah</a></li>
                            <li><a href="#">Penarikan Saldo</a></li>
                            <li><a href="#">Program Loyalitas</a></li>
                            <li><a href="#">Laporan Statistik</a></li>
                        </ul>
                    </div>

                    <div>
                        <h5>Kontak</h5>
                        <ul class="list-unstyled mb-0">
                            <li><a href="#team">Tim Kami</a></li>
                            <li><a href="#contact">Hubungi Kami</a></li>
                            <li><a href="mailto:info@greenpoint.id">info@greenpoint.id</a></li>
                            <li><a href="tel:02112345678">(021) 1234-5678</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="footer-bottom">
                <span>&copy; 2024 <a href="#hero">GreenPoint</a>. All Rights Reserved.</span>
                <div class="social-links">
                    <a href="#" title="Facebook" aria-label="Facebook GreenPoint"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Instagram" aria-label="Instagram GreenPoint"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="Twitter" aria-label="Twitter GreenPoint"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="GitHub" aria-label="GitHub GreenPoint"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 700,
            easing: 'ease-in-out',
            once: true,
            offset: 80
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Glass effect on scroll
        const landingNavbar = document.querySelector('.navbar-landing');

        const updateNavbarState = () => {
            if (landingNavbar) {
                landingNavbar.classList.toggle('is-scrolled', window.scrollY > 12);
            }
        };

        window.addEventListener('scroll', updateNavbarState);
        updateNavbarState();
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>

</html>
