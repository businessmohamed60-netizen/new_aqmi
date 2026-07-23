/* NOVAQYS — Supabase client (shared across auth pages) */
(function (window) {
  'use strict';

  const SUPABASE_URL = 'https://0ec90b57d6e95fcbda19832f.supabase.co';
  const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJib2x0IiwicmVmIjoiMGVjOTBiNTdkNmU5NWZjYmRhMTk4MzJmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTg4ODE1NzQsImV4cCI6MTc1ODg4MTU3NH0.9I8-U0x86Ak8t2DGaIk0HfvTSLsAyzdnz-Nw00mMkKw';

  // Load supabase-js from CDN, then expose a singleton
  function loadScript(src) {
    return new Promise(function (resolve, reject) {
      var existing = document.querySelector('script[data-supabase-src]');
      if (existing) { resolve(); return; }
      var s = document.createElement('script');
      s.src = src;
      s.dataset.supabaseSrc = 'true';
      s.onload = resolve;
      s.onerror = function () { reject(new Error('Failed to load Supabase library')); };
      document.head.appendChild(s);
    });
  }

  var _client = null;

  window.novaSupabase = {
    init: function () {
      if (_client) return Promise.resolve(_client);
      return loadScript('https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2.39.7/dist/umd/supabase.min.js')
        .then(function () {
          _client = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
            auth: {
              persistSession: true,
              autoRefreshToken: true,
              detectSessionInUrl: false
            }
          });
          return _client;
        });
    },
    getClient: function () { return _client; }
  };
})(window);
