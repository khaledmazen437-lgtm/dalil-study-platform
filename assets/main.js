/* دليل — Main JavaScript */
(function () {
  "use strict";

  /* ── Dark Mode ── */
  const root = document.documentElement;
  const saved = localStorage.getItem("dalil-theme") || "light";
  root.setAttribute("data-theme", saved);
  updateThemeBtn(saved);

  function updateThemeBtn(theme) {
    const btn = document.getElementById("btn-theme");
    if (btn) btn.textContent = theme === "dark" ? "☀️" : "🌙";
  }

  window.toggleTheme = function () {
    const cur = root.getAttribute("data-theme") || "light";
    const next = cur === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", next);
    localStorage.setItem("dalil-theme", next);
    updateThemeBtn(next);
  };

  /* ── Mobile Menu ── */
  window.toggleMenu = function () {
    const menu = document.getElementById("mobile-menu");
    if (menu) menu.classList.toggle("open");
  };

  /* ── Active nav link ── */
  const path = window.location.pathname;
  document.querySelectorAll(".nav-link, .mobile-link, .bottom-tab").forEach((el) => {
    const href = el.getAttribute("href") || "";
    if (href === "/" ? path === "/" : path.startsWith(href) && href !== "/") {
      el.classList.add("active");
    }
  });

  /* ── Filter Tabs ── */
  window.setFilter = function (key, val, gridId) {
    document.querySelectorAll(`[data-filter="${key}"]`).forEach((btn) => {
      btn.classList.toggle("active", btn.dataset.value === val);
    });
    document.querySelectorAll(`[data-group]`).forEach((row) => {
      if (gridId && row.closest("#" + gridId) === null) return;
      const show = val === "all" || row.dataset.group === val;
      row.style.display = show ? "" : "none";
    });
  };

  /* ── Notes (localStorage) ── */
  const NOTES_KEY = "dalil_notes";
  window.NotesApp = {
    load: () => JSON.parse(localStorage.getItem(NOTES_KEY) || "[]"),
    save: (notes) => localStorage.setItem(NOTES_KEY, JSON.stringify(notes)),
    render: function () {
      const grid = document.getElementById("notes-grid");
      if (!grid) return;
      const notes = this.load();
      if (notes.length === 0) {
        grid.innerHTML = '<div class="empty-state"><div class="empty-icon">📝</div><div class="empty-text">لا توجد ملاحظات بعد — اضغط + لإضافة ملاحظة جديدة</div></div>';
        return;
      }
      grid.innerHTML = notes.map((n, i) => `
        <div class="note-card fade-up" style="background:${n.color};animation-delay:${i*0.05}s">
          <button class="note-del" onclick="NotesApp.del(${i})" title="حذف">✕</button>
          <textarea class="note-text" placeholder="اكتب ملاحظتك..."
            onchange="NotesApp.edit(${i}, this.value)">${e(n.text)}</textarea>
          <div style="font-size:10px;color:rgba(0,0,0,.4)">${n.date}</div>
        </div>`).join("");
    },
    add: function (color) {
      const notes = this.load();
      notes.unshift({ text: "", color, date: new Date().toLocaleDateString("ar-EG") });
      this.save(notes);
      this.render();
    },
    del: function (i) {
      if (!confirm("حذف الملاحظة؟")) return;
      const notes = this.load();
      notes.splice(i, 1);
      this.save(notes);
      this.render();
    },
    edit: function (i, val) {
      const notes = this.load();
      if (notes[i]) notes[i].text = val;
      this.save(notes);
    },
  };

  function e(s) { return String(s).replace(/</g,"&lt;").replace(/>/g,"&gt;"); }

  /* ── Progress (localStorage) ── */
  const PROG_KEY = "dalil_progress_v2";
  window.ProgressApp = {
    load: () => JSON.parse(localStorage.getItem(PROG_KEY) || "{}"),
    save: function (data) { localStorage.setItem(PROG_KEY, JSON.stringify(data)); },
    set: function (id, status) {
      const data = this.load();
      data[id] = status;
      this.save(data);
      this.renderAll();
    },
    renderAll: function () {
      const data = this.load();
      let total = 0, done = 0;
      document.querySelectorAll("[data-course-id]").forEach((card) => {
        const id = card.dataset.courseId;
        const st = data[id] || "not_started";
        total++;
        if (st === "completed") done++;
        card.querySelectorAll(".prog-btn").forEach((b) => {
          const active = b.dataset.status === st;
          b.style.fontWeight = active ? "800" : "500";
          b.style.opacity = active ? "1" : "0.55";
        });
        const label = card.querySelector(".prog-label");
        const bar   = card.querySelector(".progress-bar-fill");
        const pct = st === "completed" ? 100 : st === "in_progress" ? 50 : 0;
        if (label) label.textContent = st === "completed" ? "✅ مكتمل" : st === "in_progress" ? "⏳ جاري" : "⭕ لم يبدأ";
        if (bar)   bar.style.width = pct + "%";
      });
      const overall = document.getElementById("overall-pct");
      if (overall && total > 0) {
        const pct = Math.round((done / total) * 100);
        overall.textContent = pct + "%";
        const bar = document.getElementById("overall-bar");
        if (bar) bar.style.width = pct + "%";
      }
    },
  };

  /* ── Admin AJAX Helpers ── */
  window.adminFetch = async function (url, method, body) {
    const opts = {
      method: method || "GET",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    if (res.status === 204) return null;
    return res.json();
  };

  window.confirm2 = function (msg) { return confirm(msg || "هل أنت متأكد؟"); };

  /* ── Gamification (XP & Levels) ── */
  window.Gamification = {
    addXP: function(amount) {
      let xp = parseInt(localStorage.getItem('dalil_xp') || '0', 10);
      xp += amount;
      localStorage.setItem('dalil_xp', xp);
      this.updateUI();

      const toast = document.createElement('div');
      toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#10b981;color:#fff;padding:12px 20px;border-radius:12px;font-family:Tajawal,sans-serif;font-weight:700;z-index:10000;box-shadow:0 10px 25px rgba(0,0,0,0.2);transition:all 0.3s;';
      toast.innerHTML = `🌟 ممتاز! اكتسبت ${amount} نقطة (XP)`;
      document.body.appendChild(toast);
      setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
    },
    updateUI: function() {
      let xp = parseInt(localStorage.getItem('dalil_xp') || '0', 10);
      let level = Math.floor(xp / 100) + 1;
      const badge = document.getElementById('user-level-text');
      if (badge) badge.textContent = `مستوى ${level}`;
    }
  };

  /* ── Init ── */
  document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("notes-grid")) NotesApp.render();
    if (document.querySelector("[data-course-id]")) ProgressApp.renderAll();

    if (window.Gamification) Gamification.updateUI();

    if (!localStorage.getItem('dalil_tour_done') && typeof introJs !== 'undefined') {
      setTimeout(() => {
        introJs().setOptions({
          nextLabel: 'التالي',
          prevLabel: 'السابق',
          doneLabel: 'إنهاء',
          showBullets: false
        }).start().oncomplete(function() {
          localStorage.setItem('dalil_tour_done', 'yes');
        }).onexit(function() {
          localStorage.setItem('dalil_tour_done', 'yes');
        });
      }, 1500);
    }
  });
})();
