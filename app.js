
(() => {
  'use strict';

  // ---------- Constants ----------
  const STORAGE_KEYS = {
    theme: 'scms_theme',
    complaints: 'scms_complaints'
  };

  const STATUS = {
    pending: 'Pending',
    progress: 'In Progress',
    resolved: 'Resolved'
  };

  const CATEGORY_ICONS = {
    'Fan Not Working': '🌀',
    'Light Not Working': '💡',
    'Classroom Cleaning Issue': '🧹',
    'Washroom Cleaning Issue': '🚿',
    'Water Supply Problem': '💧',
    'Wi-Fi Issue': '📶',
    'Electrical Issue': '⚡',
    'Furniture Damage': '🪑',
    'Classroom Maintenance': '🏫',
    'Other': '📌'
  };

  // ---------- Utility ----------
  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const uid = () => 'CMP-' + Date.now().toString(36).toUpperCase() + Math.random().toString(36).slice(2, 5).toUpperCase();
  const escapeHtml = (str = '') => String(str).replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));

  // ---------- Theme ----------
  const initTheme = () => {
    const saved = localStorage.getItem(STORAGE_KEYS.theme);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = saved || (prefersDark ? 'dark' : 'light');
    applyTheme(theme);
  };

  const applyTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEYS.theme, theme);
    const btn = $('.mode-toggle');
    if (btn) btn.textContent = theme === 'dark' ? '☀️' : '🌙';
  };

  const toggleTheme = () => {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    applyTheme(current === 'dark' ? 'light' : 'dark');
  };

  // ---------- Navigation ----------
  const initNav = () => {
    const toggle = $('.nav-toggle');
    const links = $('.nav-links');
    if (toggle && links) {
      toggle.addEventListener('click', () => {
        links.classList.toggle('show');
        toggle.setAttribute('aria-expanded', links.classList.contains('show'));
      });
      // Close mobile menu on link click
      $$('.nav-links a').forEach(a => {
        a.addEventListener('click', () => {
          if (window.innerWidth <= 720) links.classList.remove('show');
        });
      });
    }
  };

  // ---------- Seed Data ----------
  const getSeedComplaints = () => [
    {
      id: 'CMP-K9A2X7',
      name: 'Aarav Sharma',
      studentId: 'CS21045',
      email: 'aarav@college.edu',
      department: 'Computer Science',
      category: 'Fan Not Working',
      description: 'Two ceiling fans in lecture hall A-201 have stopped working, causing discomfort during afternoon classes.',
      building: 'Block A',
      floor: '2',
      room: 'A-201',
      priority: 'High',
      status: STATUS.pending,
      image: null,
      createdAt: new Date(Date.now() - 1000 * 60 * 60 * 3).toISOString()
    },
    {
      id: 'CMP-M3B8P1',
      name: 'Priya Nair',
      studentId: 'EC20118',
      email: 'priya.n@college.edu',
      department: 'Electronics',
      category: 'Wi-Fi Issue',
      description: 'Wi-Fi in the east wing library drops frequently between 2-4 PM. Affects study sessions.',
      building: 'Library',
      floor: '1',
      room: 'Reading Hall',
      priority: 'Medium',
      status: STATUS.progress,
      image: null,
      createdAt: new Date(Date.now() - 1000 * 60 * 60 * 26).toISOString()
    },
    {
      id: 'CMP-Q7R4T2',
      name: 'Rahul Verma',
      studentId: 'ME22077',
      email: 'rahul.v@college.edu',
      department: 'Mechanical',
      category: 'Washroom Cleaning Issue',
      description: 'Men\'s washroom on 3rd floor needs urgent cleaning and sanitizer refill.',
      building: 'Block B',
      floor: '3',
      room: 'Men\'s Washroom',
      priority: 'High',
      status: STATUS.resolved,
      image: null,
      createdAt: new Date(Date.now() - 1000 * 60 * 60 * 72).toISOString()
    },
    {
      id: 'CMP-Y1W9L6',
      name: 'Sneha Iyer',
      studentId: 'IT21092',
      email: 'sneha.i@college.edu',
      department: 'Information Technology',
      category: 'Furniture Damage',
      description: 'Broken chair in lab 404. Risk of injury if not replaced.',
      building: 'Block C',
      floor: '4',
      room: 'Lab 404',
      priority: 'Low',
      status: STATUS.pending,
      image: null,
      createdAt: new Date(Date.now() - 1000 * 60 * 60 * 8).toISOString()
    }
  ];

  // ---------- Data Store ----------
  const loadComplaints = () => {
    try {
      const raw = localStorage.getItem(STORAGE_KEYS.complaints);
      if (!raw) {
        const seed = getSeedComplaints();
        localStorage.setItem(STORAGE_KEYS.complaints, JSON.stringify(seed));
        return seed;
      }
      return JSON.parse(raw);
    } catch {
      return [];
    }
  };

  const saveComplaints = (list) => {
    localStorage.setItem(STORAGE_KEYS.complaints, JSON.stringify(list));
  };

  let complaints = [];

  // ---------- Stats ----------
  const computeStats = () => {
    const total = complaints.length;
    const pending = complaints.filter(c => c.status === STATUS.pending).length;
    const progress = complaints.filter(c => c.status === STATUS.progress).length;
    const resolved = complaints.filter(c => c.status === STATUS.resolved).length;
    return { total, pending, progress, resolved };
  };

  const updateStatsUI = () => {
    const stats = computeStats();
    const animate = (selector, value) => {
      $$(selector).forEach(el => {
        const current = parseInt(el.textContent, 10) || 0;
        if (current === value) { el.textContent = value; return; }
        const diff = value - current;
        const steps = 20;
        let step = 0;
        const iv = setInterval(() => {
          step++;
          el.textContent = Math.round(current + (diff * step / steps));
          if (step >= steps) clearInterval(iv);
        }, 20);
      });
    };
    animate('[data-stat="total"]', stats.total);
    animate('[data-stat="pending"]', stats.pending);
    animate('[data-stat="progress"]', stats.progress);
    animate('[data-stat="resolved"]', stats.resolved);
  };

  // ---------- Form ----------
  const initForm = () => {
    const form = $('#complaint-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      const data = Object.fromEntries(fd.entries());

      // Basic validation
      if (!data.name || !data.studentId || !data.email || !data.department ||
          !data.category || !data.description || !data.building || !data.floor || !data.room) {
        showAlert('Please fill in all required fields.', 'danger');
        return;
      }

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(data.email)) {
        showAlert('Please enter a valid email address.', 'danger');
        return;
      }

      // Image handling
      const fileInput = form.querySelector('input[type="file"]');
      const file = fileInput && fileInput.files[0];

      const addComplaint = (imageDataUrl) => {
        const complaint = {
          id: uid(),
          name: data.name.trim(),
          studentId: data.studentId.trim(),
          email: data.email.trim(),
          department: data.department,
          category: data.category,
          description: data.description.trim(),
          building: data.building.trim(),
          floor: data.floor,
          room: data.room.trim(),
          priority: data.priority || 'Medium',
          status: STATUS.pending,
          image: imageDataUrl || null,
          createdAt: new Date().toISOString()
        };
        complaints.unshift(complaint);
        saveComplaints(complaints);
        form.reset();
        showAlert(`Complaint ${complaint.id} registered successfully!`, 'success');
        updateStatsUI();
        renderDashboard();
        setTimeout(() => {
          document.getElementById('dashboard')?.scrollIntoView({ behavior: 'smooth' });
        }, 600);
      };

      if (file) {
        const reader = new FileReader();
        reader.onload = () => addComplaint(reader.result);
        reader.onerror = () => addComplaint(null);
        reader.readAsDataURL(file);
      } else {
        addComplaint(null);
      }
    });
  };

  // ---------- Alert ----------
  const showAlert = (message, type = 'success') => {
    const alertEl = $('.alert-message');
    if (!alertEl) return;
    const colors = {
      success: { bg: 'rgba(40, 167, 69, 0.12)', border: 'rgba(40, 167, 69, 0.4)' },
      danger:  { bg: 'rgba(231, 76, 60, 0.12)',  border: 'rgba(231, 76, 60, 0.4)' },
      warning: { bg: 'rgba(255, 159, 67, 0.14)', border: 'rgba(255, 159, 67, 0.4)' }
    };
    const icons = { success: '✓', danger: '⚠', warning: '⚠' };
    const c = colors[type] || colors.success;
    alertEl.style.background = c.bg;
    alertEl.style.borderColor = c.border;
    alertEl.textContent = `${icons[type] || ''}  ${message}`;
    alertEl.classList.add('active');
    clearTimeout(alertEl._t);
    alertEl._t = setTimeout(() => alertEl.classList.remove('active'), 4500);
  };

  // ---------- Dashboard ----------
  const getStatusClass = (status) => {
    if (status === STATUS.pending) return 'badge-pending';
    if (status === STATUS.progress) return 'badge-progress';
    if (status === STATUS.resolved) return 'badge-resolved';
    return '';
  };

  const formatDate = (iso) => {
    const d = new Date(iso);
    return d.toLocaleString(undefined, {
      year: 'numeric', month: 'short', day: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  };

  const renderCard = (c) => {
    const icon = CATEGORY_ICONS[c.category] || '📌';
    const imgBlock = c.image
      ? `<img src="${c.image}" alt="Issue image" style="width:100%;max-height:180px;object-fit:cover;border-radius:16px;margin-top:12px;">`
      : '';
    return `
      <article class="complaint-card" data-id="${c.id}">
        <div class="complaint-header">
          <div>
            <div class="card-category">
              <span>${icon}</span> ${escapeHtml(c.category)}
            </div>
            <h3 style="margin:6px 0 0;">Complaint ${escapeHtml(c.id)}</h3>
          </div>
          <span class="badge-pill ${getStatusClass(c.status)}">${c.status}</span>
        </div>
        <div class="card-body">
          <p>${escapeHtml(c.description)}</p>
          ${imgBlock}
          <div class="card-row">
            <div class="card-location">
              <small>Location</small>
              <strong>${escapeHtml(c.building)} · Floor ${escapeHtml(c.floor)} · ${escapeHtml(c.room)}</strong>
            </div>
            <div class="card-meta">
              <small>Submitted by</small>
              <strong>${escapeHtml(c.name)} <span style="color:var(--text-muted);font-weight:500;">(${escapeHtml(c.studentId)})</span></strong>
            </div>
          </div>
          <div class="card-row">
            <div class="card-meta">
              <small>Department</small>
              <strong>${escapeHtml(c.department)}</strong>
            </div>
            <div class="card-meta">
              <small>Submitted on</small>
              <strong>${formatDate(c.createdAt)}</strong>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <span class="card-priority">Priority: ${escapeHtml(c.priority)}</span>
            <select class="status-select" data-id="${c.id}" style="padding:10px 14px;border-radius:16px;border:1px solid rgba(90,120,200,0.18);background:rgba(255,255,255,0.78);color:var(--text);">
              <option value="${STATUS.pending}" ${c.status === STATUS.pending ? 'selected' : ''}>Pending</option>
              <option value="${STATUS.progress}" ${c.status === STATUS.progress ? 'selected' : ''}>In Progress</option>
              <option value="${STATUS.resolved}" ${c.status === STATUS.resolved ? 'selected' : ''}>Resolved</option>
            </select>
            <button class="btn btn-secondary delete-btn" data-id="${c.id}" style="padding:10px 18px;">Delete</button>
          </div>
        </div>
      </article>
    `;
  };

  const renderDashboard = () => {
    const grid = $('.complaint-grid');
    const emptyEl = $('#empty-state');
    if (!grid) return;

    const searchVal = ($('#search-input')?.value || '').trim().toLowerCase();
    const statusFilter = $('#status-filter')?.value || '';
    const categoryFilter = $('#category-filter')?.value || '';
    const priorityFilter = $('#priority-filter')?.value || '';

    const filtered = complaints.filter(c => {
      const matchSearch = !searchVal ||
        c.id.toLowerCase().includes(searchVal) ||
        c.name.toLowerCase().includes(searchVal) ||
        c.description.toLowerCase().includes(searchVal) ||
        c.studentId.toLowerCase().includes(searchVal);
      const matchStatus = !statusFilter || c.status === statusFilter;
      const matchCategory = !categoryFilter || c.category === categoryFilter;
      const matchPriority = !priorityFilter || c.priority === priorityFilter;
      return matchSearch && matchStatus && matchCategory && matchPriority;
    });

    if (filtered.length === 0) {
      grid.innerHTML = '';
      if (emptyEl) {
        emptyEl.style.display = 'block';
        emptyEl.textContent = complaints.length === 0
          ? 'No complaints yet. Register the first one above!'
          : 'No complaints match the current filters.';
      }
      return;
    }

    if (emptyEl) emptyEl.style.display = 'none';
    grid.innerHTML = filtered.map(renderCard).join('');

    // Attach event listeners to new cards
    $$('.status-select', grid).forEach(sel => {
      sel.addEventListener('change', (e) => {
        const id = sel.getAttribute('data-id');
        const newStatus = e.target.value;
        const idx = complaints.findIndex(c => c.id === id);
        if (idx >= 0) {
          complaints[idx].status = newStatus;
          saveComplaints(complaints);
          updateStatsUI();
          renderDashboard();
          showAlert(`Complaint ${id} marked as ${newStatus}.`, 'success');
        }
      });
    });

    $$('.delete-btn', grid).forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        if (confirm(`Delete complaint ${id}?`)) {
          complaints = complaints.filter(c => c.id !== id);
          saveComplaints(complaints);
          updateStatsUI();
          renderDashboard();
          showAlert(`Complaint ${id} deleted.`, 'warning');
        }
      });
    });
  };

  const initDashboardControls = () => {
    const search = $('#search-input');
    const statusF = $('#status-filter');
    const catF = $('#category-filter');
    const priF = $('#priority-filter');

    [search, statusF, catF, priF].forEach(el => {
      if (!el) return;
      el.addEventListener('input', renderDashboard);
      el.addEventListener('change', renderDashboard);
    });
  };

  // ---------- Smooth scroll for anchor links ----------
  const initSmoothScroll = () => {
    $$('a[href^="#"]').forEach(link => {
      link.addEventListener('click', (e) => {
        const targetId = link.getAttribute('href').slice(1);
        if (!targetId) return;
        const target = document.getElementById(targetId);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  };

  // ---------- Init ----------
  const init = () => {
    initTheme();
    initNav();
    initSmoothScroll();

    // Theme toggle
    const themeBtn = $('.mode-toggle');
    if (themeBtn) themeBtn.addEventListener('click', toggleTheme);

    // Load data
    complaints = loadComplaints();

    // Build UI
    updateStatsUI();
    renderDashboard();
    initForm();
    initDashboardControls();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();