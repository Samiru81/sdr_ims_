// SDR IMS password field protection
// Blocks copy/cut/paste/drop/right-click on password fields.
// Screenshot blocking is not fully possible in browsers, but this hides visible passwords on PrintScreen or tab blur.

(function () {
  function securePasswordFields() {
    const fields = document.querySelectorAll('input[type="password"], input[data-secure-password="1"], #loginPassword, #newPassword, #confirmPassword, #addUserPassword');

    fields.forEach(function (field) {
      field.setAttribute('data-secure-password', '1');
      field.setAttribute('autocomplete', 'new-password');
      field.setAttribute('oncopy', 'return false');
      field.setAttribute('oncut', 'return false');
      field.setAttribute('onpaste', 'return false');
      field.setAttribute('ondrop', 'return false');
      field.setAttribute('oncontextmenu', 'return false');

      ['copy', 'cut', 'paste', 'drop', 'contextmenu'].forEach(function (eventName) {
        field.addEventListener(eventName, function (e) {
          e.preventDefault();
          showPasswordSecurityMessage('Copy, paste and right-click are disabled for password fields.');
          return false;
        });
      });

      field.addEventListener('keydown', function (e) {
        const key = (e.key || '').toLowerCase();

        if ((e.ctrlKey || e.metaKey) && ['c', 'x', 'v', 'insert'].includes(key)) {
          e.preventDefault();
          showPasswordSecurityMessage('Copy, cut and paste are disabled for password fields.');
          return false;
        }

        if (e.shiftKey && key === 'insert') {
          e.preventDefault();
          showPasswordSecurityMessage('Paste is disabled for password fields.');
          return false;
        }
      });
    });
  }

  function hideVisiblePasswords() {
    document.querySelectorAll('input[data-secure-password="1"]').forEach(function (field) {
      field.type = 'password';
    });

    const iconIds = ['passwordToggleIcon', 'newPassIcon', 'confirmPassIcon', 'addUserPasswordIcon'];
    iconIds.forEach(function (id) {
      const icon = document.getElementById(id);
      if (icon) icon.textContent = '👁';
    });
  }

  function showPasswordSecurityMessage(message) {
    let box = document.getElementById('passwordSecurityNotice');

    if (!box) {
      box = document.createElement('div');
      box.id = 'passwordSecurityNotice';
      box.className = 'password-security-notice';
      document.body.appendChild(box);
    }

    box.textContent = message;
    box.classList.add('show');

    clearTimeout(window.__passwordSecurityNoticeTimer);
    window.__passwordSecurityNoticeTimer = setTimeout(function () {
      box.classList.remove('show');
    }, 2200);
  }

  document.addEventListener('DOMContentLoaded', securePasswordFields);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'PrintScreen') {
      hideVisiblePasswords();
      showPasswordSecurityMessage('Visible passwords were hidden for security.');
    }
  });

  window.addEventListener('blur', hideVisiblePasswords);
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) hideVisiblePasswords();
  });

  // Re-scan if page content changes dynamically.
  setInterval(securePasswordFields, 1500);
})();
