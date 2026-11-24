<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/notes';
$pageTitle   = 'إدارة المهام (Kanban)';
$bp          = BASE_PATH;

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:rgba(255,255,255,0.15)">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
  </div>
  <h1 class="hero-title">إدارة المهام</h1>
  <p class="hero-sub">لوحة مهام ذكية بتقنية السحب والإفلات (Kanban Board)</p>
</div>

<div class="container" style="max-width: 1200px; overflow-x: auto;">

  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px;background:var(--card);border:1px solid var(--border);border-radius:16px;padding:16px">
    <input type="text" id="kanban-input" placeholder="اكتب اسم المهمة الجديدة واضغط Enter..." style="flex:1; padding:12px 16px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:'Cairo',sans-serif; outline:none; min-width:200px;">
    <button onclick="addKanbanTask()" class="btn-primary" style="padding:12px 24px;">➕ إضافة مهمة</button>
    <button onclick="if(confirm('حذف كل المهام؟')){localStorage.removeItem('dalil_kanban');initKanban()}" style="padding:12px 20px;border-radius:8px;border:1px solid var(--border);background:var(--danger-bg);color:var(--danger);font-weight:700;cursor:pointer; display:flex; align-items:center; gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        حذف الكل
    </button>
  </div>

  <div class="kanban-board" style="display: grid; grid-template-columns: repeat(3, minmax(280px, 1fr)); gap: 24px; align-items: start;">

    <!-- Column: To Do -->
    <div class="kanban-col" data-col="todo" style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; min-height: 400px; display:flex; flex-direction:column;">
      <h3 style="font-family:'Tajawal',sans-serif; margin-bottom:16px; display:flex; align-items:center; gap:8px; color:var(--text);">
        <span style="width:12px; height:12px; border-radius:50%; background:var(--muted);"></span> للعمل (To Do)
      </h3>
      <div class="kanban-dropzone" style="flex:1; display:flex; flex-direction:column; gap:12px;"></div>
    </div>

    <!-- Column: In Progress -->
    <div class="kanban-col" data-col="inprogress" style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; min-height: 400px; display:flex; flex-direction:column;">
      <h3 style="font-family:'Tajawal',sans-serif; margin-bottom:16px; display:flex; align-items:center; gap:8px; color:var(--text);">
        <span style="width:12px; height:12px; border-radius:50%; background:var(--amber);"></span> قيد التنفيذ (In Progress)
      </h3>
      <div class="kanban-dropzone" style="flex:1; display:flex; flex-direction:column; gap:12px;"></div>
    </div>

    <!-- Column: Done -->
    <div class="kanban-col" data-col="done" style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; min-height: 400px; display:flex; flex-direction:column;">
      <h3 style="font-family:'Tajawal',sans-serif; margin-bottom:16px; display:flex; align-items:center; gap:8px; color:var(--text);">
        <span style="width:12px; height:12px; border-radius:50%; background:var(--success);"></span> مكتمل (Done)
      </h3>
      <div class="kanban-dropzone" style="flex:1; display:flex; flex-direction:column; gap:12px;"></div>
    </div>

  </div>
</div>

<script>
// Kanban Data Structure: { id: { text: "...", status: "todo/inprogress/done", id: "..." } }
let kanbanData = {};

function initKanban() {
    const saved = localStorage.getItem('dalil_kanban');
    kanbanData = saved ? JSON.parse(saved) : {};
    renderKanban();
}

function saveKanban() {
    localStorage.setItem('dalil_kanban', JSON.stringify(kanbanData));
}

function generateId() {
    return 'task_' + Math.random().toString(36).substr(2, 9);
}

function addKanbanTask() {
    const input = document.getElementById('kanban-input');
    const text = input.value.trim();
    if(!text) return;

    const id = generateId();
    kanbanData[id] = { id, text, status: 'todo' };
    saveKanban();
    renderKanban();
    input.value = '';
}

document.getElementById('kanban-input').addEventListener('keypress', function(e) {
    if(e.key === 'Enter') addKanbanTask();
});

function deleteKanbanTask(id) {
    if(confirm('هل أنت متأكد من حذف هذه المهمة؟')) {
        delete kanbanData[id];
        saveKanban();
        renderKanban();
    }
}

function renderKanban() {
    document.querySelectorAll('.kanban-dropzone').forEach(zone => zone.innerHTML = '');

    for(const id in kanbanData) {
        const task = kanbanData[id];
        const zone = document.querySelector(`.kanban-col[data-col="${task.status}"] .kanban-dropzone`);
        if(zone) {
            const el = document.createElement('div');
            el.className = 'kanban-item card';
            el.draggable = true;
            el.dataset.id = id;
            el.style.cssText = 'padding: 16px; cursor: grab; display:flex; justify-content:space-between; align-items:flex-start; gap:10px; background:var(--card); box-shadow:0 2px 8px var(--shadow); user-select:none; animation: fadeUp 0.3s; margin:0;';
            el.innerHTML = `
                <div style="font-size:14px; font-weight:600; line-height:1.5;">${task.text}</div>
                <button onclick="deleteKanbanTask('${id}')" style="background:none; border:none; color:var(--danger); cursor:pointer; padding:4px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            `;

            el.addEventListener('dragstart', (e) => {
                el.style.opacity = '0.4';
                e.dataTransfer.setData('text/plain', id);
            });
            el.addEventListener('dragend', () => {
                el.style.opacity = '1';
            });

            zone.appendChild(el);
        }
    }
}

document.querySelectorAll('.kanban-dropzone').forEach(zone => {
    zone.addEventListener('dragover', e => {
        e.preventDefault();
        zone.style.background = 'rgba(230,0,0,0.05)';
        zone.style.borderRadius = '12px';
    });
    zone.addEventListener('dragleave', e => {
        zone.style.background = 'transparent';
    });
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.style.background = 'transparent';
        const id = e.dataTransfer.getData('text/plain');
        if(kanbanData[id]) {
            const newStatus = zone.parentElement.dataset.col;
            kanbanData[id].status = newStatus;
            saveKanban();
            renderKanban();
        }
    });
});

initKanban();
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
