/* PERF admin panel: repeatable rows, icon previews, unsaved-change warning and small helpers. */
(function () {
  'use strict';
  var $$ = function (sel, el) { return Array.prototype.slice.call((el || document).querySelectorAll(sel)); };

  // Mobile menu
  var menuBtn = document.querySelector('[data-menu]');
  if (menuBtn) menuBtn.addEventListener('click', function () {
    var open = document.body.classList.toggle('menu-open');
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  // Ask before destructive actions
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) { if (!confirm(f.getAttribute('data-confirm'))) e.preventDefault(); });
  });

  // Warn about leaving with unsaved changes
  var dirty = false;
  $$('form[data-dirty]').forEach(function (f) {
    var mark = function () { dirty = true; f.classList.add('is-dirty'); };
    f.addEventListener('input', mark);
    f.addEventListener('change', mark);
    f.addEventListener('submit', function () { dirty = false; });
  });
  window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  // Icon preview next to icon drop-downs
  var icons = document.getElementById('icons');
  function showIcon(sel) {
    var prev = sel.parentNode.querySelector('.icon-prev');
    var svg = icons && icons.content.querySelector('[data-name="' + sel.value + '"]');
    if (prev && svg) prev.innerHTML = svg.innerHTML;
  }
  document.addEventListener('change', function (e) { if (e.target.matches('select[data-icon]')) showIcon(e.target); });

  // Repeatable rows (events, services, FAQs ...)
  var uid = Date.now();
  $$('fieldset[data-rows]').forEach(function (fs) {
    var list = fs.querySelector('.row-list');
    var tpl = fs.querySelector('template');
    var count = fs.querySelector('legend .count');
    var changed = function () {
      if (count) count.textContent = '(' + list.children.length + ')';
      fs.closest('form').dispatchEvent(new Event('change'));
    };

    fs.addEventListener('click', function (e) {
      var b = e.target.closest('button');
      if (!b || !fs.contains(b)) return;
      var row = b.closest('details.row');
      if (b.hasAttribute('data-add')) {
        var html = tpl.innerHTML.replace(/__i__/g, 'n' + (uid++));
        list.insertAdjacentHTML('beforeend', html);
        var added = list.lastElementChild;
        added.open = true;
        var first = added.querySelector('input:not([type=hidden]):not([type=checkbox]), textarea');
        if (first) first.focus();
        changed();
      } else if (b.hasAttribute('data-expand') || b.hasAttribute('data-collapse')) {
        $$('details.row', list).forEach(function (d) { d.open = b.hasAttribute('data-expand'); });
      } else if (row) {
        e.preventDefault();   // buttons sit inside <summary>: don't toggle the row
        if (b.hasAttribute('data-del')) {
          var name = row.querySelector('.row-title').textContent.trim();
          if (!confirm('Delete "' + name + '"? (It is removed when you click Save changes.)')) return;
          row.remove();
        } else if (b.hasAttribute('data-up') && row.previousElementSibling) {
          list.insertBefore(row, row.previousElementSibling);
        } else if (b.hasAttribute('data-down') && row.nextElementSibling) {
          list.insertBefore(row.nextElementSibling, row);
        }
        b.focus();
        changed();
      }
    });

    // Keep the row heading in sync with its title field
    fs.addEventListener('input', function (e) {
      var row = e.target.closest('details.row');
      if (!row) return;
      var key = row.getAttribute('data-title-field');
      if (key && e.target.name && e.target.name.slice(-(key.length + 2)) === '[' + key + ']') {
        var t = row.querySelector('.row-title');
        t.textContent = e.target.value.trim() || 'New item';
      }
    });
  });

  // Search box that filters a table
  $$('input[data-filter]').forEach(function (inp) {
    var rows = $$(inp.getAttribute('data-filter') + ' tbody tr');
    inp.addEventListener('input', function () {
      var q = inp.value.toLowerCase();
      rows.forEach(function (r) { r.hidden = q !== '' && r.textContent.toLowerCase().indexOf(q) === -1; });
    });
  });
})();
