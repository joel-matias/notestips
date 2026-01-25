(() => {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const pass = document.getElementById('password');

  const toggle1 = document.getElementById('toggle1');

  function toggleVisibility(input, btn) {
    input.type = (input.type === 'password') ? 'text' : 'password';
    btn.textContent = (input.type === 'password') ? 'Mostrar' : 'Ocultar';
  }

  toggle1?.addEventListener('click', () => toggleVisibility(pass, toggle1));

  const el = document.getElementById('lockMsg');
  if (!el) return;

  let seconds = parseInt(el.dataset.seconds, 10);
  if (!Number.isFinite(seconds) || seconds <= 0) return;

  const tick = () => {
    if (seconds <= 0) {
      el.textContent = '';
      return;
    }
    el.textContent = `Demasiados intentos. Intenta de nuevo en ${seconds}s.`;
    seconds--;
    setTimeout(tick, 1000);
  };

  tick();
})();
