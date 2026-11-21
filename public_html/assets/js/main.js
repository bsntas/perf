(function () {
  // mobile menu
  var mb = document.querySelector('.menu-btn'), nav = document.getElementById('nav');
  if (mb) mb.addEventListener('click', function () {
    var o = nav.classList.toggle('open');
    mb.setAttribute('aria-expanded', o);
    mb.setAttribute('aria-label', o ? 'Close menu' : 'Open menu');
  });

  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  // header shadow, reading progress bar and back-to-top button
  var header = document.querySelector('.site-header'), bar = document.getElementById('progress'), top = document.getElementById('to-top');
  var onScroll = function () {
    var y = window.scrollY, h = document.documentElement.scrollHeight - innerHeight;
    if (header) header.classList.toggle('scrolled', y > 10);
    if (bar) bar.style.transform = 'scaleX(' + (h > 0 ? y / h : 0) + ')';
    if (top) top.classList.toggle('show', y > 600);
  };
  addEventListener('scroll', onScroll, { passive: true }); onScroll();
  if (top) top.addEventListener('click', function () { scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }); });

  // count-up numbers ("500+" counts from 0 to 500 and keeps the "+")
  function countUp(el) {
    var m = /^(\D*)(\d+)(.*)$/.exec(el.dataset.count || '');
    if (!m || reduce || el.counted) return;
    el.counted = true;
    var end = +m[2], t0 = null;
    var step = function (t) {
      t0 = t0 || t;
      var p = Math.min((t - t0) / 1400, 1), v = Math.round(end * (1 - Math.pow(1 - p, 3)));
      el.textContent = m[1] + v + m[3];
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  // gentle fade-up of content as it scrolls into view
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target;
        el.classList.add('in');
        el.querySelectorAll && el.querySelectorAll('[data-count]').forEach(countUp);
        io.unobserve(el);
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.sec-head, .card, .goal, .steps li, .stat, .tile, .dest, .faq, .two > div, .filters, .faq-search').forEach(function (el) {
      if (el.getBoundingClientRect().top < innerHeight) return; // already on screen: leave as is
      var p = el.parentNode;
      p._rv = (p._rv || 0) + 1; // stagger siblings in the same row
      el.style.setProperty('--d', Math.min(p._rv - 1, 5));
      el.classList.add('rv'); io.observe(el);
    });
    document.querySelectorAll('.stats, .journey').forEach(function (el) { io.observe(el); });
  } else document.querySelectorAll('[data-count]').forEach(countUp);

  // "Find your path": tap a goal to see matching programs
  var goals = document.querySelectorAll('.goal');
  goals.forEach(function (g) {
    g.addEventListener('click', function (e) {
      e.preventDefault();
      var id = g.dataset.goal, res = document.querySelector('[data-result="' + id + '"]');
      goals.forEach(function (x) { x.classList.toggle('on', x === g); x.setAttribute('aria-pressed', x === g); });
      document.querySelectorAll('.goal-result').forEach(function (r) { r.hidden = r !== res; });
      if (res) {
        var r = res.getBoundingClientRect();
        if (r.bottom > innerHeight) scrollTo({ top: scrollY + r.bottom - innerHeight + 24, behavior: reduce ? 'auto' : 'smooth' });
      }
    });
  });

  // FAQ search: type a word to filter questions
  var q = document.getElementById('faq-q');
  if (q) {
    var faqs = [].slice.call(document.querySelectorAll('.faq'));
    var heads = [].slice.call(document.querySelectorAll('.faq-group'));
    faqs.forEach(function (d) { d.dataset.text = d.textContent.toLowerCase(); });
    q.addEventListener('input', function () {
      var v = q.value.trim().toLowerCase(), any = false;
      faqs.forEach(function (d) {
        var hit = !v || d.dataset.text.indexOf(v) > -1;
        d.hidden = !hit; any = any || hit; d.classList.add('in');
        d.open = !!v && hit && v.length > 2;
      });
      heads.forEach(function (h) {
        var n = h.nextElementSibling, vis = false;
        while (n && n.classList.contains('faq')) { if (!n.hidden) vis = true; n = n.nextElementSibling; }
        h.hidden = !vis;
      });
      document.getElementById('faq-none').hidden = any;
    });
  }

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
          var show = f === 'all' || el.dataset.cat === f;
          el.style.display = show ? '' : 'none';
          el.classList.remove('fade-in');
          if (show) { void el.offsetWidth; el.classList.add('fade-in'); }
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

  // "Enquire now" links pass ?interest=<service> to pre-select the course
  var f = document.getElementById('enquiry');
  if (f) {
    var pre = new URLSearchParams(location.search).get('interest');
    if (pre && f.interest.querySelector('option[value="' + pre.replace(/[^a-z0-9-]/g, '') + '"]')) f.interest.value = pre;
  }

  // enquiry forms (contact page + home page call-back form)
  document.querySelectorAll('form[data-enquiry]').forEach(function (f) {
    var st = f.querySelector('.status'), btn = f.querySelector('button[type=submit]');
    var v = function (n) { return f[n] ? (f[n].value || '').trim() : ''; };
    var invalid = function () {
      if (f.checkValidity()) return false;
      st.className = 'status err'; st.textContent = 'Please enter your name and a valid phone number.';
      f.reportValidity && f.reportValidity();
      return true;
    };

    // Static preview (GitHub Pages): no server, so hand the enquiry to WhatsApp or the email app
    if (f.dataset.static) f.addEventListener('submit', function (e) {
      e.preventDefault();
      if (invalid()) return;
      var lines = ['New ' + (f.id === 'callback' ? 'call-back request' : 'enquiry') + ' from the PERF website', '',
        'Name: ' + v('name'), 'Phone: ' + v('phone')];
      if (f.email) lines.push('Email: ' + v('email'));
      lines.push('I am a: ' + v('role'));
      if (f.city) lines.push('City: ' + v('city'));
      lines.push('Interested in: ' + f.interest.options[f.interest.selectedIndex].text);
      if (f.message) lines.push('', 'Message:', v('message') || '-');
      var text = lines.join('\n');
      if (f.dataset.wa) {
        window.open('https://wa.me/' + f.dataset.wa + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
        st.className = 'status ok'; st.textContent = 'WhatsApp has opened with your enquiry. Press send there and our team will reply shortly.';
      } else {
        location.href = 'mailto:' + f.dataset.email + '?subject=' + encodeURIComponent('Enquiry: ' + v('name')) + '&body=' + encodeURIComponent(text);
        st.className = 'status ok'; st.textContent = 'Your email app has opened with the enquiry. Press send there and our team will reply shortly.';
      }
    });

    // Hostinger: send with fetch; without JS it posts normally and redirects back
    else if (window.fetch && window.FormData) f.addEventListener('submit', function (e) {
      e.preventDefault();
      if (invalid()) return;
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
  });
})();
