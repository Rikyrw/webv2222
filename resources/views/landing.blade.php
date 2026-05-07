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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #2d6a4f;
            --secondary-green: #40916c;
            --light-green: #52b788;
            --accent-green: #74c69d;
            --dark-bg: #1b4332;
            --light-bg: #f1faee;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }

        .logo-white {
            width: 60px;
            height: 60px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        /* Hero Section */
        .hero-section {
            background:
                linear-gradient(
                    135deg,
                    rgba(27, 67, 50, 0.88) 0%,
                    rgba(45, 106, 79, 0.82) 100%
                ),
                url("{{ asset('images/bg-gunung.png') }}");

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-content,
        .hero-image {
            position: relative;
            z-index: 2;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(116, 198, 157, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 5%;
            width: 400px;
            height: 400px;
            background: rgba(74, 158, 102, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image img {
            transform: translateX(245px);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(20px);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: slideInDown 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.6;
            animation: slideInUp 0.8s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-hero {
            background: linear-gradient(135deg, var(--light-green) 0%, var(--accent-green) 100%);
            border: none;
            padding: 14px 40px;
            font-weight: 700;
            border-radius: 50px;
            color: white;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(116, 198, 157, 0.4);
        }

        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(116, 198, 157, 0.6);
            color: white;
        }

        .hero-image {
            animation: float 4s ease-in-out infinite;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: white;
        }

        .feature-card {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 40px rgba(45, 106, 79, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(116, 198, 157, 0.1) 0%, rgba(74, 158, 102, 0.1) 100%);
            border-radius: 15px;
        }

        .feature-card h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-top: 1rem;
        }

        .feature-card img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        /* Tujuan Section */
        .tujuan-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e8f5e9 100%);
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 500px;
            margin: 0 auto;
        }

        .goal-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            border-top: 5px solid var(--light-green);
            position: relative;
        }

        .goal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(45, 106, 79, 0.15);
        }

        .goal-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--secondary-green) 0%, var(--light-green) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
        }

        .goal-card h4 {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .goal-card p {
            color: #666;
            line-height: 1.8;
        }

        /* FAQ Section */
        .faq-section {
            padding: 80px 0;
            background: white;
        }

        .accordion-button {
            background: linear-gradient(135deg, var(--light-bg) 0%, white 100%);
            border: 1px solid #e0e0e0;
            color: var(--primary-green);
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1.2rem;
        }

        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, var(--light-green) 0%, var(--accent-green) 100%);
            color: white;
            box-shadow: none;
        }

        .accordion-button:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.25rem rgba(116, 198, 157, 0.25);
        }

        .accordion-body {
            color: #555;
            line-height: 1.8;
            padding: 1.5rem;
        }

        /* Team Section */
        .team-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e8f5e9 100%);
        }

        .team-member {
            text-align: center;
            transition: all 0.4s ease;
        }

        .member-img {
            position: relative;
            margin-bottom: 1.5rem;
            width: 230px;
            height: 230px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid #52b788;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .member-img img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 20px;
            transition: 0.4s ease;
        }

        .team-member:hover .member-img img {
            transform: scale(1.22) translateY(10px);
        }

        .member-info h4 {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .member-info span {
            color: var(--light-green);
            font-weight: 600;
        }

        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background: white;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .contact-info-item:hover {
            transform: translateX(10px);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--secondary-green) 0%, var(--light-green) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }

        .contact-form {
            background: linear-gradient(135deg, var(--light-bg) 0%, white 100%);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(116, 198, 157, 0.2);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--secondary-green) 0%, var(--light-green) 100%);
            border: none;
            padding: 12px 40px;
            border-radius: 50px;
            color: white;
            font-weight: 700;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.3);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            padding: 3rem 0 1rem;
        }

        footer h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        footer a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #fff;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 0.8rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section-title h2 {
                font-size: 2rem;
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
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo" class="logo-white">
                GreenPoint
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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
    <section id="hero" class="hero-section pt-5" style="margin-top: 60px;">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="hero-title">
                        Kelola Sampah,<br><span style="background: linear-gradient(135deg, #74c69d 0%, #52b788 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Raih Manfaat</span>
                    </h1>
                    <p class="hero-subtitle">
                        Platform bank sampah digital yang mengubah sampah Anda menjadi nilai ekonomi untuk masa depan yang berkelanjutan.
                    </p>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-hero">
                        <i class="fas fa-arrow-right me-2"></i>Mulai Sekarang
                    </a>
                    @else
                    <a href="{{ rtrim($BASE_URL, '/') }}/nasabah/login" class="btn btn-hero">
                        <i class="fas fa-arrow-right me-2"></i>Mulai Sekarang
                    </a>
                    @endif
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                    <div class="hero-image">
                        <img src="{{ asset('images/tangan1.png') }}"
                            alt="Hero Image"
                            class="img-fluid"
                            style="max-width: 110%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section" data-aos="fade-up">
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
    <section id="tujuan" class="tujuan-section" data-aos="fade-up">
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
    <section id="faq" class="faq-section" data-aos="fade-up">
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
    <section id="team" class="team-section" data-aos="fade-up">
        <div class="row justify-content-center g-4">

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team-member">
                    <div class="member-img">
                        <img src="{{ asset('assets/img/team/aditya.jpg') }}" alt="Aditya Fadni">
                    </div>
                    <div class="member-info">
                        <h4>Aditya Fadni</h4>
                        <span>Master & Backend</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team-member">
                    <div class="member-img">
                        <img src="{{ asset('assets/img/team/riky.jpg') }}" alt="Riky Rio">
                    </div>
                    <div class="member-info">
                        <h4>Riky Rio</h4>
                        <span>Frontend & Database</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
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

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="team-member">
                    <div class="member-img">
                        <img src="{{ asset('assets/img/team/dhimas.jpg') }}" alt="Dhimas">
                    </div>
                    <div class="member-info">
                        <h4>Dhimas</h4>
                        <span>UI/UX</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section" data-aos="fade-up">
        <div class="container">
            <div class="section-title" data-aos="fade-up" data-aos-duration="800">
                <h2>Hubungi Kami</h2>
                <p>Jangan ragu untuk menghubungi kami untuk informasi lebih lanjut</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 style="color: var(--primary-green); font-weight: 700; margin-bottom: 0.5rem;">Alamat</h5>
                            <p style="color: #666; margin: 0;">Jl. Lingkungan Hijau No. 123<br>Kelurahan Bersih, Kecamatan Asri<br>Kota Hijau, 12345</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h5 style="color: var(--primary-green); font-weight: 700; margin-bottom: 0.5rem;">Telepon</h5>
                            <p style="color: #666; margin: 0;">(021) 1234-5678<br>0812-3456-7890 (WhatsApp)</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h5 style="color: var(--primary-green); font-weight: 700; margin-bottom: 0.5rem;">Email</h5>
                            <p style="color: #666; margin: 0;">info@greenpoint.id<br>cs@greenpoint.id</p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h5 style="color: var(--primary-green); font-weight: 700; margin-bottom: 0.5rem;">Jam Operasional</h5>
                            <p style="color: #666; margin: 0;">Senin - Jumat: 08.00 - 17.00 WIB<br>Sabtu: 08.00 - 14.00 WIB<br>Minggu & Hari Libur: Tutup</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="color: var(--primary-green); font-weight: 600;">Nama</label>
                                <input type="text" name="name" class="form-control" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="color: var(--primary-green); font-weight: 600;">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="color: var(--primary-green); font-weight: 600;">Subjek</label>
                                <input type="text" name="subject" class="form-control" placeholder="Masukkan subjek" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="color: var(--primary-green); font-weight: 600;">Pesan</label>
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
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-leaf me-2"></i>GreenPoint</h5>
                    <p>Platform bank sampah digital yang mengubah sampah Anda menjadi nilai ekonomi untuk masa depan berkelanjutan.</p>
                    <div class="social-links mt-3">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="#hero">Beranda</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#tujuan">Tujuan</a></li>
                        <li><a href="#contact">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Layanan</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Setoran Sampah</a></li>
                        <li><a href="#">Penarikan Saldo</a></li>
                        <li><a href="#">Program Loyalitas</a></li>
                        <li><a href="#">Laporan Statistik</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Kontak</h5>
                    <p>
                        <i class="fas fa-phone me-2"></i>(021) 1234-5678<br>
                        <i class="fas fa-envelope me-2"></i>info@greenpoint.id<br>
                        <i class="fas fa-map-marker-alt me-2"></i>Jl. Lingkungan Hijau No. 123
                    </p>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.2); margin: 2rem 0;">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <p>&copy; 2024 <strong>GreenPoint</strong>. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p>Designed with <i class="fas fa-heart" style="color: #ff6b6b;"></i> by GreenPoint Team</p>
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
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            offset: 100
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

        // Active nav link on scroll
        window.addEventListener('scroll', () => {
            let current = '';
            const sections = document.querySelectorAll('section');

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                    link.style.borderBottom = '2px solid white';
                } else {
                    link.style.borderBottom = 'none';
                }
            });
        });
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>

</html>