(() => {
  const form = document.getElementById('registerForm');
  if (!form) return;

  const pass = document.getElementById('password');
  const pass2 = document.getElementById('password_confirmation');

  const passMsg = document.getElementById('passMsg');
  const confirmMsg = document.getElementById('confirmMsg');

  const toggle1 = document.getElementById('toggle1');
  const toggle2 = document.getElementById('toggle2');

  function toggleVisibility(input, btn) {
    input.type = (input.type === 'password') ? 'text' : 'password';
    btn.textContent = (input.type === 'password') ? 'Mostrar' : 'Ocultar';
  }

  toggle1?.addEventListener('click', () => toggleVisibility(pass, toggle1));
  toggle2?.addEventListener('click', () => toggleVisibility(pass2, toggle2));

  function validate() {
    passMsg.textContent = '';
    confirmMsg.textContent = '';

    let ok = true;

    if ((pass.value || '').length < 8) {
      passMsg.textContent = 'La contraseña debe tener mínimo 8 caracteres.';
      ok = false;
    }

    if (pass2.value && pass.value !== pass2.value) {
      confirmMsg.textContent = 'Las contraseñas no coinciden.';
      ok = false;
    }

    return ok;
  }

  pass.addEventListener('input', validate);
  pass2.addEventListener('input', validate);

  form.addEventListener('submit', (e) => {
    if (!validate()) e.preventDefault();
  });

})();
