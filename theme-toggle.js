// SDR IMS animated dark / light mode
(function () {
  const STORAGE_KEY = 'sdr-ims-theme';

  function getSavedTheme() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch (e) {
      return null;
    }
  }

  function saveTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {}
  }

  function getPreferredTheme() {
    const saved = getSavedTheme();
    if (saved === 'light' || saved === 'dark') return saved;

    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
      return 'light';
    }
    return 'dark';
  }

  function setTheme(theme, animate) {
    if (theme !== 'light' && theme !== 'dark') theme = 'dark';

    if (animate) {
      document.documentElement.classList.add('theme-changing');
      window.setTimeout(function () {
        document.documentElement.classList.remove('theme-changing');
      }, 650);
    }

    document.documentElement.setAttribute('data-theme', theme);
    document.body && document.body.setAttribute('data-theme', theme);
    saveTheme(theme);
    updateThemeButtons(theme);
  }

  function updateThemeButtons(theme) {
    document.querySelectorAll('.theme-toggle').forEach(function (btn) {
      btn.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
      btn.setAttribute('data-theme-state', theme);

      const text = btn.querySelector('.theme-toggle-text');
      if (text) {
        text.textContent = theme === 'light' ? 'Light' : 'Dark';
      }
    });
  }

  window.toggleThemeMode = function () {
    const current = document.documentElement.getAttribute('data-theme') || getPreferredTheme();
    setTheme(current === 'light' ? 'dark' : 'light', true);
  };

  window.setThemeMode = function (theme) {
    setTheme(theme, true);
  };

  document.addEventListener('DOMContentLoaded', function () {
    setTheme(getPreferredTheme(), false);
  });
})();
