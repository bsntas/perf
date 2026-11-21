(function () {
  // mobile menu
  var mb = document.querySelector('.menu-btn'), nav = document.getElementById('nav');
  if (mb) mb.addEventListener('click', function () {
    var o = nav.classList.toggle('open');
    mb.setAttribute('aria-expanded', o);
    mb.setAttribute('aria-label', o ? 'Close menu' : 'Open menu');
  });

  // category filters (services + gallery)
  function filters(barId, itemsSel) {
    var bar = document.getElementById(barId);
    if (!bar) return;
    var chips = bar.querySelectorAll('.chip');
    chips.forEach(function (c) {
      c.addEventListener('click', function () {
        chips.forEach(function (x) { x.classList.remove('on'); x.setAttribute('aria-pressed', 'false'); });
        c.classList.add('on'); c.setAttribute('aria-pressed', 'true');
        var f = c.dataset.f;
        document.querySelectorAll(itemsSel).forEach(function (el) {
          el.style.display = (f === 'all' || el.dataset.cat === f) ? '' : 'none';
        });
      });
    });
  }
  filters('filters', '#svc-grid .svc');
  filters('gfilters', '#gallery .tile');

  // testimonials slider
  var s = document.getElementById('slider');
  if (s) {
    var track = s.querySelector('.slides'), n = track.children.length, i = 0, dots = document.getElementById('dots'), t;
    var go = function (x) {
      i = (x + n) % n;
      track.style.transform = 'translateX(-' + i * 100 + '%)';
      [].forEach.call(dots.children, function (d, j) { d.classList.toggle('on', j === i); });
    };
    var reset = function () { clearInterval(t); if (n > 1) t = setInterval(function () { go(i + 1); }, 6000); };
    if (n > 1) for (var k = 0; k < n; k++) (function (k) {
      var b = document.createElement('button');
      b.type = 'button'; b.setAttribute('aria-label', 'Show testimonial ' + (k + 1));
      b.onclick = function () { go(k); reset(); };
      dots.appendChild(b);
    })(k);
    s.addEventListener('mouseenter', function () { clearInterval(t); });
    s.addEventListener('mouseleave', reset);
    go(0); reset();
  }

  // gallery lightbox
  var lb = document.getElementById('lightbox');
  if (lb) {
    var img = lb.querySelector('img'), cap = lb.querySelector('figcaption');
    var close = function () { lb.hidden = true; img.src = ''; };
    document.querySelectorAll('#gallery a').forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        img.src = a.href; img.alt = a.dataset.caption; cap.textContent = a.dataset.caption;
        lb.hidden = false; lb.querySelector('.lb-close').focus();
      });
    });
    lb.addEventListener('click', function (e) { if (e.target === lb || e.target.classList.contains('lb-close')) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !lb.hidden) close(); });
  }

  var f = document.getElementById('enquiry');

  // "Enquire now" links pass ?interest=<service> to pre-select the course
  if (f) {
    var pre = new URLSearchParams(location.search).get('interest');
    if (pre && f.interest.querySelector('option[value="' + pre.replace(/[^a-z0-9-]/g, '') + '"]')) f.interest.value = pre;
  }

  // Static preview (GitHub Pages): no server, so hand the enquiry to WhatsApp or the email app
  if (f && f.dataset.static) {
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      var st = document.getElementById('status');
      if (!f.checkValidity()) {
        st.className = 'status err'; st.textContent = 'Please enter your name and a valid phone number.';
        f.reportValidity && f.reportValidity(); return;
      }
      var v = function (n) { return (f[n].value || '').trim(); };
      var lines = ['New enquiry from the PERF website', '',
        'Name: ' + v('name'), 'Phone: ' + v('phone'), 'Email: ' + v('email'), 'I am a: ' + v('role'),
        'City: ' + v('city'), 'Interested in: ' + f.interest.options[f.interest.selectedIndex].text, '', 'Message:', v('message') || '-'];
      var text = lines.join('\n');
      if (f.dataset.wa) {
        window.open('https://wa.me/' + f.dataset.wa + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
        st.className = 'status ok'; st.textContent = 'WhatsApp has opened with your enquiry. Press send there and our team will reply shortly.';
      } else {
        location.href = 'mailto:' + f.dataset.email + '?subject=' + encodeURIComponent('Enquiry: ' + v('name')) + '&body=' + encodeURIComponent(text);
        st.className = 'status ok'; st.textContent = 'Your email app has opened with the enquiry. Press send there and our team will reply shortly.';
      }
    });
  }

  // enquiry form (Hostinger): send with fetch; without JS it posts normally and redirects back
  else if (f && window.fetch && window.FormData) {
    f.addEventListener('submit', function (e) {
      var st = document.getElementById('status'), btn = f.querySelector('button[type=submit]');
      e.preventDefault();
      if (!f.checkValidity()) {
        st.className = 'status err';
        st.textContent = 'Please enter your name and a valid phone number.';
        f.reportValidity && f.reportValidity();
        return;
      }
      st.className = 'status'; st.textContent = 'Sending...'; btn.disabled = true;
      fetch(f.action, { method: 'POST', body: new FormData(f), headers: { 'X-Requested-With': 'fetch' } })
        .then(function (r) { return r.json(); })
        .then(function (d) {
          st.className = 'status ' + (d.ok ? 'ok' : 'err'); st.textContent = d.message;
          if (d.ok) { f.reset(); if (window.gtag) gtag('event', 'generate_lead'); }
        })
        .catch(function () { st.className = 'status err'; st.textContent = 'Could not send. Please call or WhatsApp us instead.'; })
        .then(function () { btn.disabled = false; });
    });
  }
})();
