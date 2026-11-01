document.addEventListener('DOMContentLoaded', function () {
  const pwdInput = document.querySelector('[data-password-strength]');
  const pwdBar = document.querySelector('.password-strength-bar');
  if (pwdInput && pwdBar) {
    pwdInput.addEventListener('input', function () {
      const val = pwdInput.value;
      let score = 0;
      if (val.length >= 8) score += 40;
      if (/[A-Za-z]/.test(val)) score += 30;
      if (/[0-9]/.test(val)) score += 30;
      pwdBar.style.width = score + '%';
      pwdBar.style.background = score < 40 ? '#B23B3B' : (score < 100 ? '#C9972F' : '#3A7D63');
    });
  }

  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });

  document.querySelectorAll('.alert[data-autohide]').forEach(function (alertEl) {
    setTimeout(function () {
      alertEl.classList.remove('show');
      alertEl.classList.add('fade');
      setTimeout(() => alertEl.remove(), 400);
    }, 5000);
  });

  const noteRange = document.querySelector('[data-note-range]');
  const noteOutput = document.querySelector('[data-note-output]');
  if (noteRange && noteOutput) {
    const update = () => { noteOutput.textContent = noteRange.value; };
    noteRange.addEventListener('input', update);
    update();
  }

  const chatList = document.querySelector('.chat-list');
  if (chatList) {
    chatList.scrollTop = chatList.scrollHeight;
  }
});
