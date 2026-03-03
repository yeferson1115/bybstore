<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BYB Store | Créditos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --byb-primary: #7f39fb;
            --byb-secondary: #20d7f7;
            --byb-accent: #ff7a00;
            --byb-dark: #111827;
        }

        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #f8fbff 55%, #fff7f1 100%);
            color: #1f2937;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        .hero {
            background: linear-gradient(120deg, rgba(127, 57, 251, 0.95), rgba(32, 215, 247, 0.92));
            border-radius: 28px;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            right: -120px;
            top: -80px;
        }

        .hero-badge {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 999px;
            padding: .45rem .9rem;
            display: inline-block;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .btn-cta {
            border-radius: 14px;
            padding: .85rem 1.3rem;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(17, 24, 39, 0.15);
        }

        .btn-gradient {
            border: none;
            color: #fff;
            background: linear-gradient(120deg, var(--byb-accent), #ff2d8d);
        }

        .btn-outline-light-soft {
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: #fff;
            background: transparent;
        }

        .btn-outline-light-soft:hover {
            background: #fff;
            color: var(--byb-primary);
        }

        .feature-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
            height: 100%;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--byb-primary), var(--byb-secondary));
            margin-bottom: .8rem;
        }

        .site-footer {
            background: var(--byb-dark);
            color: #d1d5db;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
        }

        .social-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            margin-right: 1rem;
        }

        .logo-image {
            width: 76px;
            height: 76px;
            object-fit: contain;
            border-radius: 14px;
            background: #fff;
            padding: .3rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .footer-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
            padding: .25rem;
        }
    </style>
</head>
<body>
    <header class="topbar py-3 mb-4">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('imagenes/logo.png') }}" alt="Logo B&B Store" class="logo-image">
                <div>
                    <h1 class="h4 mb-0 fw-bold">B&B Store</h1>
                    <small class="text-muted">Tienda virtual al alcance de todos</small>
                </div>
            </div>
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Ingreso administrativo</a>
        </div>
    </header>

    <main class="container pb-5">
        <section class="hero p-4 p-md-5 mb-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7 position-relative" style="z-index:1;">
                    <span class="hero-badge">Financiación fácil y segura</span>
                    <h2 class="display-5 fw-bold">Impulsa tus compras con crédito BYB</h2>
                    <p class="lead mb-4">En minutos puedes solicitar tu crédito, revisar tu estado y pagar cuotas por Wompi con total tranquilidad.</p>
                    <div class="d-flex flex-column flex-md-row gap-3">
                        <a href="{{ route('credit-applications.create') }}" class="btn btn-cta btn-gradient">Solicitar crédito ahora</a>
                        <a href="{{ route('credit-portal.index') }}" class="btn btn-cta btn-outline-light-soft">Consultar y pagar cuotas</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center position-relative" style="z-index:1;">
                    <img src="{{ asset('imagenes/logo.png') }}" alt="B&B Store" class="img-fluid" style="max-width: 330px;">
                </div>
            </div>
        </section>

        <section class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <span class="feature-icon">1</span>
                    <h5>Solicita en línea</h5>
                    <p class="text-muted mb-0">Completa tu solicitud de crédito con tus datos y documentos en un proceso simple.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <span class="feature-icon">2</span>
                    <h5>Paga con tranquilidad</h5>
                    <p class="text-muted mb-0">Haz pagos rápidos y seguros desde nuestro portal con integración Wompi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <span class="feature-icon">3</span>
                    <h5>Siempre informado</h5>
                    <p class="text-muted mb-0">Consulta el estado de tus transacciones y recibe acompañamiento en cada paso.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer py-4 mt-3">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('imagenes/logo.png') }}" alt="Logo B&B Store" class="footer-logo">
                <div>
                    <div class="fw-semibold text-white">B&B Store</div>
                    <small>Tienda virtual al alcance de todos</small>
                </div>
            </div>
            <div>
                <div class="mb-1"><strong class="text-white">Redes:</strong>
                    <a href="#" class="social-link">Instagram</a>
                    <a href="#" class="social-link">Facebook</a>
                    <a href="#" class="social-link">WhatsApp</a>
                </div>
                <small>Dirección: Calle 123 #45-67, Bogotá, Colombia</small>
            </div>
        </div>
    </footer>
</body>
</html>
