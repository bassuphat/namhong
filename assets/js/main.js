/* ==========================================================================
   NAMHONG GROUP — shared site behaviour
   Pure vanilla JS, no build step, no external dependencies.
   NOTE ON STATE: the language choice is kept in memory (page scope) only.
   This file intentionally avoids localStorage/sessionStorage/cookies so it
   is safe to preview anywhere; when this site is deployed for real, swap
   `memory` below for a small cookie so the choice survives navigation
   (check current PDPA guidance on cookie notices before doing so).
   ========================================================================== */
(function () {
  'use strict';

  var memory = { lang: 'th' };

  /* ------------------------------------------------------------------
     i18n — global chrome + home page are fully bilingual.
     Interior pages translate headings/labels; body copy that has not
     been translated yet simply stays in Thai and a small notice
     (data-i18n-note) explains that, rather than showing blank text.
     Extend by adding more keys here and data-i18n="key.path" in HTML.
     ------------------------------------------------------------------ */
  var dict = {
    th: {
      nav: { home: 'หน้าแรก', about: 'เกี่ยวกับเรา', business: 'ธุรกิจของเรา', sustainability: 'ความยั่งยืน', news: 'ข่าวสาร', career: 'ร่วมงานกับเรา', contact: 'ติดต่อเรา' },
      mega: {
        aboutColTitle: 'ประวัติและวิสัยทัศน์', aboutColTitle2: 'การบริหารจัดการ', aboutHistory: 'ประวัติความเป็นมา', aboutHistoryDesc: 'เส้นทางตั้งแต่ก่อตั้งจนถึงวันนี้',
        aboutVision: 'วิสัยทัศน์ และพันธกิจ', aboutVisionDesc: 'ทิศทางที่เรามุ่งไป', aboutMgmt: 'คณะผู้บริหาร', aboutMgmtDesc: 'ทีมงานที่ขับเคลื่อนองค์กร',
        aboutGov: 'การกำกับดูแลกิจการ', aboutGovDesc: 'หลักธรรมาภิบาลของกลุ่มบริษัท', bizColTitle: 'สองธุรกิจหลัก',
        bizPalm: 'นามหงส์ น้ำมันปาล์ม', bizPalmDesc: 'สกัดน้ำมันปาล์มดิบและเมล็ดในปาล์ม', bizPower: 'นามหงส์ พาวเวอร์', bizPowerDesc: 'พลังงานหมุนเวียนจากชีวมวลและก๊าซชีวภาพ',
        megaFootText: 'อยากรู้จักโครงสร้างกลุ่มบริษัททั้งหมด?', megaFootLink: 'ดูภาพรวมธุรกิจ'
      },
      cta: { contact: 'ติดต่อเรา', explore: 'สำรวจธุรกิจของเรา', readMore: 'อ่านเพิ่มเติม', viewAll: 'ดูทั้งหมด', apply: 'สมัครงานนี้', send: 'ส่งข้อความ', viewDetail: 'ดูรายละเอียด' },
      footer: {
        blurb: 'กลุ่มบริษัทนามหงส์ ผู้ดำเนินธุรกิจสกัดน้ำมันปาล์มและพลังงานหมุนเวียนแบบครบวงจร ตั้งแต่ปี 2545',
        col1: 'องค์กร', col2: 'ธุรกิจ', col3: 'ติดต่อ', rights: 'สงวนลิขสิทธิ์', privacy: 'นโยบายความเป็นส่วนตัว', pdpa: 'PDPA'
      },
      note: { partial: 'เนื้อหาในหน้านี้แสดงผลเป็นภาษาไทยเป็นหลัก ทีมงานกำลังจัดทำฉบับภาษาอังกฤษฉบับเต็ม' }
    },
    en: {
      nav: { home: 'Home', about: 'About Us', business: 'Our Business', sustainability: 'Sustainability', news: 'News', career: 'Careers', contact: 'Contact' },
      mega: {
        aboutColTitle: 'Our History & Vision', aboutColTitle2: 'Management', aboutHistory: 'Our History', aboutHistoryDesc: 'From founding to today',
        aboutVision: 'Vision & Mission', aboutVisionDesc: 'The direction we are heading', aboutMgmt: 'Management Team', aboutMgmtDesc: 'The people driving the group',
        aboutGov: 'Corporate Governance', aboutGovDesc: 'How the group is governed', bizColTitle: 'Two Core Businesses',
        bizPalm: 'Namhong Palm Oil', bizPalmDesc: 'Crude palm oil & palm kernel extraction', bizPower: 'Namhong Power', bizPowerDesc: 'Renewable energy from biomass & biogas',
        megaFootText: 'Want to see how the whole group fits together?', megaFootLink: 'View business overview'
      },
      cta: { contact: 'Contact us', explore: 'Explore our business', readMore: 'Read more', viewAll: 'View all', apply: 'Apply for this role', send: 'Send message', viewDetail: 'View details' },
      footer: {
        blurb: 'Namhong Group has been operating an integrated palm-oil-extraction and renewable-energy business since 2002.',
        col1: 'Company', col2: 'Business', col3: 'Contact', rights: 'All rights reserved.', privacy: 'Privacy Policy', pdpa: 'PDPA'
      },
      note: { partial: 'This page is currently shown in Thai. A full English translation is in progress.' }
    }
  };

  function getPath(obj, path) {
    return path.split('.').reduce(function (o, k) { return (o && o[k] !== undefined) ? o[k] : undefined; }, obj);
  }

  function applyLanguage(lang) {
    memory.lang = lang;
    document.documentElement.setAttribute('lang', lang === 'th' ? 'th' : 'en');
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      var val = getPath(dict[lang], el.getAttribute('data-i18n'));
      if (val !== undefined) el.textContent = val;
    });
    document.querySelectorAll('[data-i18n-ph]').forEach(function (el) {
      var val = getPath(dict[lang], el.getAttribute('data-i18n-ph'));
      if (val !== undefined) el.setAttribute('placeholder', val);
    });
    document.querySelectorAll('.lang-switch button').forEach(function (b) {
      b.classList.toggle('active', b.getAttribute('data-lang') === lang);
    });
    document.querySelectorAll('[data-i18n-note]').forEach(function (el) {
      el.style.display = lang === 'en' ? 'flex' : 'none';
      if (lang === 'en') el.textContent = dict.en.note.partial;
    });
  }

  document.querySelectorAll('.lang-switch button').forEach(function (btn) {
    btn.addEventListener('click', function () { applyLanguage(btn.getAttribute('data-lang')); });
  });

  /* ------------------------------------------------------------------
     Sticky header shrink
     ------------------------------------------------------------------ */
  var header = document.querySelector('.site-header');
  function onScrollHeader() {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 10);
  }
  document.addEventListener('scroll', onScrollHeader, { passive: true });
  onScrollHeader();

  /* ------------------------------------------------------------------
     Desktop mega menu (hover + keyboard focus, click still navigates)
     ------------------------------------------------------------------ */
  var openTimer, closeTimer;
  document.querySelectorAll('.nav-item.has-mega').forEach(function (item) {
    var link = item.querySelector('.nav-link');
    function open() { clearTimeout(closeTimer); openTimer = setTimeout(function () {
      document.querySelectorAll('.nav-item.has-mega').forEach(function (i) { if (i !== item) i.classList.remove('is-open'); });
      item.classList.add('is-open'); link.setAttribute('aria-expanded', 'true');
    }, 60); }
    function close() { clearTimeout(openTimer); closeTimer = setTimeout(function () {
      item.classList.remove('is-open'); link.setAttribute('aria-expanded', 'false');
    }, 150); }
    item.addEventListener('mouseenter', open);
    item.addEventListener('mouseleave', close);
    item.addEventListener('focusin', open);
    item.addEventListener('focusout', function (e) { if (!item.contains(e.relatedTarget)) close(); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.nav-item.has-mega.is-open').forEach(function (i) { i.classList.remove('is-open'); });
    }
  });

  /* ------------------------------------------------------------------
     Mobile drawer + accordion
     ------------------------------------------------------------------ */
  var drawer = document.querySelector('.mobile-drawer');
  var hamburger = document.querySelector('.js-hamburger');
  var drawerClose = document.querySelector('.js-drawer-close');
  function setDrawer(open) {
    if (!drawer) return;
    drawer.classList.toggle('is-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  }
  if (hamburger) hamburger.addEventListener('click', function () { setDrawer(true); });
  if (drawerClose) drawerClose.addEventListener('click', function () { setDrawer(false); });
  var backdrop = document.querySelector('.mobile-drawer-backdrop');
  if (backdrop) backdrop.addEventListener('click', function () { setDrawer(false); });

  document.querySelectorAll('.mobile-acc-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var expanded = btn.getAttribute('aria-expanded') === 'true';
      var panel = document.getElementById(btn.getAttribute('aria-controls'));
      btn.setAttribute('aria-expanded', String(!expanded));
      if (panel) panel.style.maxHeight = expanded ? '0px' : panel.scrollHeight + 'px';
    });
  });

  /* ------------------------------------------------------------------
     Scroll reveal (IntersectionObserver)
     ------------------------------------------------------------------ */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add('in-view'); io.unobserve(entry.target); }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in-view'); });
  }

  /* ------------------------------------------------------------------
     Counters
     ------------------------------------------------------------------ */
  var counters = document.querySelectorAll('[data-count-to]');
  function animateCount(el) {
    var target = parseFloat(el.getAttribute('data-count-to'));
    var suffix = el.getAttribute('data-suffix') || '';
    var dur = 1500, start = null;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      var val = Math.round(target * eased);
      el.textContent = val.toLocaleString('en-US') + suffix;
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  if ('IntersectionObserver' in window && counters.length) {
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { animateCount(entry.target); cio.unobserve(entry.target); }
      });
    }, { threshold: 0.4 });
    counters.forEach(function (el) { cio.observe(el); });
  } else {
    counters.forEach(animateCount);
  }

  /* ------------------------------------------------------------------
     Filter buttons (gallery / news)
     ------------------------------------------------------------------ */
  document.querySelectorAll('.filter-row').forEach(function (row) {
    var targetSelector = row.getAttribute('data-filter-target');
    var items = targetSelector ? document.querySelectorAll(targetSelector) : [];
    row.querySelectorAll('.filter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        row.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var f = btn.getAttribute('data-filter');
        items.forEach(function (it) {
          var cats = (it.getAttribute('data-category') || '').split(' ');
          it.classList.toggle('hidden', f !== 'all' && cats.indexOf(f) === -1);
        });
      });
    });
  });

  /* ------------------------------------------------------------------
     Tabs
     ------------------------------------------------------------------ */
  document.querySelectorAll('.tabs-nav').forEach(function (nav) {
    nav.querySelectorAll('.tab-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var group = nav.closest('[data-tabs-group]') || document;
        group.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
        group.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
        btn.classList.add('active');
        var panel = group.querySelector('[data-tab-panel="' + btn.getAttribute('data-tab') + '"]');
        if (panel) panel.classList.add('active');
      });
    });
  });

  /* ------------------------------------------------------------------
     Back to top
     ------------------------------------------------------------------ */
  var toTop = document.querySelector('.back-to-top');
  if (toTop) {
    document.addEventListener('scroll', function () { toTop.classList.toggle('show', window.scrollY > 600); }, { passive: true });
    toTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }

  /* ------------------------------------------------------------------
     Demo form handling below (no cookie banner anymore — removed by request)
     ------------------------------------------------------------------ */

  /* ------------------------------------------------------------------
     Form handling — submits to the PHP endpoint named in each form's
     data-action attribute (see backend/contact-handler.php and
     backend/apply-handler.php). The PHP script validates again server
     side, stores the submission in MySQL, and returns JSON like
     { ok: true, message: "..." } or { ok: false, message: "..." } —
     whatever message it returns is shown to the visitor, so validation
     errors from the server surface correctly, not just a generic one.
     ------------------------------------------------------------------ */
  document.querySelectorAll('.js-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.reportValidity(); return; }
      var action = form.getAttribute('data-action') || form.getAttribute('action') || '';
      var status = form.querySelector('.form-status');
      var submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;
      if (status) { status.classList.remove('show', 'ok', 'err'); }

      fetch(action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(form)).toString()
      }).then(function (res) {
        return res.json().catch(function () { return { ok: false, message: '' }; }).then(function (data) {
          return { httpOk: res.ok, data: data };
        });
      }).then(function (result) {
        var ok = result.httpOk && result.data.ok;
        if (status) {
          status.textContent = result.data.message ||
            (ok ? (form.getAttribute('data-success-text') || 'ส่งข้อมูลเรียบร้อยแล้ว')
                : 'ระบบขัดข้อง กรุณาลองใหม่อีกครั้ง หรือติดต่อเราโดยตรงทางอีเมล/โทรศัพท์');
          status.classList.add('show', ok ? 'ok' : 'err');
        }
        if (ok) form.reset();
      }).catch(function () {
        if (status) {
          status.textContent = 'ขออภัย ระบบส่งข้อมูลขัดข้อง กรุณาลองใหม่อีกครั้ง หรือติดต่อเราโดยตรงทางอีเมล/โทรศัพท์';
          status.classList.add('show', 'err');
        }
      }).finally(function () {
        if (submitBtn) submitBtn.disabled = false;
      });
    });
  });

  /* ------------------------------------------------------------------
     Footer year
     ------------------------------------------------------------------ */
  document.querySelectorAll('.js-year').forEach(function (el) { el.textContent = new Date().getFullYear() + 543; });
  document.querySelectorAll('.js-year-ce').forEach(function (el) { el.textContent = new Date().getFullYear(); });

})();
