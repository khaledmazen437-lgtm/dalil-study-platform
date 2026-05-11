<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$currentPage = '/gpa';
$pageTitle   = 'حساب الـ GPA';
$bp          = BASE_PATH;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container" style="max-width:850px; padding: 40px 15px; min-height: 80vh; margin: 0 auto;">
  
  <!-- Spline 3D Viewer Header -->
  <div style="position:relative; width:100%; height:280px; border-radius:24px; overflow:hidden; margin-bottom:30px; background:linear-gradient(135deg, #0f172a, #1e293b); box-shadow: 0 10px 40px rgba(15, 23, 42, 0.2); display: flex; align-items: center; justify-content: center; isolation: isolate;">
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.0.51/build/spline-viewer.js"></script>
    <spline-viewer url="https://prod.spline.design/iWj6E0W12r69vJ1p/scene.splinecode" style="width: 100%; height: 100%; position: absolute; top:0; left:0; z-index: 1; pointer-events: auto;"></spline-viewer>
    
    <div style="position:relative; z-index:2; text-align:center; padding: 25px 40px; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.2); pointer-events: none;">
      <h1 style="color:#fff; margin:0; font-family:'Tajawal', sans-serif; font-size: clamp(2rem, 5vw, 2.8rem); font-weight: 900; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">حاسبة الـ GPA</h1>
      <p style="color:#cbd5e1; margin-top:8px; font-size: 15px; font-weight: 500;">احسب معدلك التراكمي والفصلي بكل سهولة ودقة ✨</p>
    </div>
  </div>

  <div id="gpa-card-capture" style="background:var(--card); border: 1px solid var(--border); border-radius: 24px; padding: 35px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
    
    <!-- Prior GPA Section -->
    <div style="background: var(--primary-bg); padding: 20px; border-radius: 16px; margin-bottom: 30px; border: 1px solid var(--border); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -10px; right: -10px; font-size: 80px; opacity: 0.05; pointer-events: none;">⏳</div>
        <h3 style="margin:0 0 15px 0; font-size: 1.15rem; color: var(--primary); font-family: 'Tajawal', sans-serif; font-weight: 800; display: flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            السجل السابق (اختياري)
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <label style="display:block; font-size:13px; font-weight: 700; margin-bottom:8px; color:var(--text);">الساعات المكتسبة</label>
                <input type="number" id="prev-credits" placeholder="مثال: 36" min="0" style="width:100%; padding: 12px 18px; border-radius: 12px; border: 2px solid var(--border); background: var(--bg); color:var(--text); font-family: 'Cairo', sans-serif; font-size: 15px; transition: border-color 0.3s; outline: none;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight: 700; margin-bottom:8px; color:var(--text);">المعدل التراكمي السابق</label>
                <input type="number" id="prev-gpa" placeholder="مثال: 3.20" min="0" max="4" step="0.01" style="width:100%; padding: 12px 18px; border-radius: 12px; border: 2px solid var(--border); background: var(--bg); color:var(--text); font-family: 'Cairo', sans-serif; font-size: 15px; transition: border-color 0.3s; outline: none;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
            </div>
        </div>
    </div>

    <!-- Courses Header -->
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin:0; font-family:'Tajawal', sans-serif; font-size: 1.5rem; color:var(--text); font-weight: 900;">مواد الفصل الحالي</h2>
        <button id="add-course-btn" style="background: linear-gradient(135deg, var(--primary), #0ea5c9); color:#fff; border:none; padding: 12px 24px; border-radius: 14px; font-weight: 800; font-family: 'Cairo', sans-serif; font-size: 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(26, 127, 212, 0.3);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            إضافة مادة
        </button>
    </div>

    <div style="overflow-x: auto; margin-bottom: 20px;">
      <table style="width: 100%; text-align: right; border-collapse: separate; border-spacing: 0 12px; min-width: 500px;">
          <thead>
              <tr>
                  <th style="padding: 0 10px; color: var(--muted); font-size: 14px; font-weight: 700;">اسم المادة</th>
                  <th style="padding: 0 10px; color: var(--muted); font-size: 14px; font-weight: 700; width: 110px;">عدد الساعات</th>
                  <th style="padding: 0 10px; color: var(--muted); font-size: 14px; font-weight: 700; width: 160px;">التقدير</th>
                  <th style="padding: 0 10px; width: 60px;"></th>
              </tr>
          </thead>
          <tbody id="courses-list">
              <!-- Courses will be injected here -->
          </tbody>
      </table>
    </div>

    <div style="margin-top: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
        <div style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; padding: 30px; border-radius: 24px; text-align: center; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3); position: relative; overflow: hidden; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="position:absolute; top:-10px; right:-20px; font-size: 120px; opacity: 0.1; line-height: 1;">📈</div>
            <div style="font-size: 16px; font-weight: 800; font-family: 'Tajawal', sans-serif; margin-bottom: 5px; opacity: 0.9;">المعدل الفصلي</div>
            <div id="term-gpa" style="font-family: 'Tajawal', sans-serif; font-weight: 900; font-size: 3.5rem; line-height: 1; text-shadow: 0 4px 12px rgba(0,0,0,0.15);">0.00</div>
        </div>
        <div style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; padding: 30px; border-radius: 24px; text-align: center; box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3); position: relative; overflow: hidden; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="position:absolute; top:-10px; right:-20px; font-size: 120px; opacity: 0.1; line-height: 1;">🎓</div>
            <div style="font-size: 16px; font-weight: 800; font-family: 'Tajawal', sans-serif; margin-bottom: 5px; opacity: 0.9;">المعدل التراكمي</div>
            <div id="cum-gpa" style="font-family: 'Tajawal', sans-serif; font-weight: 900; font-size: 3.5rem; line-height: 1; text-shadow: 0 4px 12px rgba(0,0,0,0.15);">0.00</div>
        </div>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <button id="tour-share-btn" onclick="shareGPA()" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border: none; padding: 14px 28px; border-radius: 14px; font-weight: 800; font-family: 'Cairo', sans-serif; font-size: 16px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" data-intro="شارك نتيجتك مع أصدقائك بضغطة زر كصورة أنيقة!" data-step="4">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
            مشاركة كصورة 📸
        </button>
    </div>
  </div>
</div>

<style>
.course-input-focus { outline: none; transition: all 0.3s; border: 2px solid var(--border); }
.course-input-focus:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-bg); }
.delete-btn-hover:hover { background: #ef4444 !important; color: #fff !important; transform: scale(1.05); }
select.course-input-focus { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: left 10px center; background-size: 16px; }
[data-theme="dark"] select.course-input-focus { background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); }
</style>

<script>
const gpaScale = {
    '4.0': 'A+ / A (امتياز)',
    '3.7': 'A-',
    '3.3': 'B+ (جيد جداً مرتفع)',
    '3.0': 'B (جيد جداً)',
    '2.7': 'B-',
    '2.3': 'C+ (جيد مرتفع)',
    '2.0': 'C (جيد)',
    '1.7': 'C-',
    '1.3': 'D+ (مقبول مرتفع)',
    '1.0': 'D (مقبول)',
    '0.0': 'F (راسب)'
};

const coursesList = document.getElementById('courses-list');
const addCourseBtn = document.getElementById('add-course-btn');
const termGpaEl = document.getElementById('term-gpa');
const cumGpaEl = document.getElementById('cum-gpa');
const prevCreditsInput = document.getElementById('prev-credits');
const prevGpaInput = document.getElementById('prev-gpa');

let courseCount = 0;

function addCourseRow() {
    courseCount++;
    const tr = document.createElement('tr');
    tr.style.background = 'var(--bg)';
    tr.style.boxShadow = '0 2px 10px rgba(0,0,0,0.02)';
    tr.style.borderRadius = '14px';
    
    tr.innerHTML = `
        <td style="padding: 6px 12px; border-radius: 0 14px 14px 0;">
            <input type="text" placeholder="مادة ${courseCount}" class="course-name course-input-focus" style="width:100%; padding: 12px 16px; border-radius: 10px; background: var(--card); color:var(--text); font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 600;">
        </td>
        <td style="padding: 6px 12px;">
            <input type="number" min="1" max="10" value="3" class="course-credits course-input-focus" style="width:100%; padding: 12px; border-radius: 10px; background: var(--card); color:var(--text); text-align: center; font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: bold;">
        </td>
        <td style="padding: 6px 12px;">
            <select class="course-grade course-input-focus" style="width:100%; padding: 12px 16px; padding-left: 35px; border-radius: 10px; background-color: var(--card); color:var(--text); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer;">
                <option value="-1">اختر التقدير</option>
                <option value="4.0">A+ / A (امتياز)</option>
                <option value="3.7">A-</option>
                <option value="3.3">B+ (جيد جداً مرتفع)</option>
                <option value="3.0">B (جيد جداً)</option>
                <option value="2.7">B-</option>
                <option value="2.3">C+ (جيد مرتفع)</option>
                <option value="2.0">C (جيد)</option>
                <option value="1.7">C-</option>
                <option value="1.3">D+ (مقبول مرتفع)</option>
                <option value="1.0">D (مقبول)</option>
                <option value="0.0">F (راسب)</option>
            </select>
        </td>
        <td style="padding: 6px 12px; border-radius: 14px 0 0 14px; text-align:center;">
            <button class="delete-btn delete-btn-hover" style="background: var(--card); border: 2px solid #fee2e2; color: #ef4444; width: 42px; height: 42px; border-radius: 10px; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
        </td>
    `;
    
    tr.querySelector('.delete-btn').addEventListener('click', () => {
        tr.style.opacity = '0';
        tr.style.transform = 'translateX(30px)';
        tr.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        setTimeout(() => {
            tr.remove();
            calculateGPA();
        }, 300);
    });

    const inputs = tr.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', calculateGPA);
        input.addEventListener('change', calculateGPA);
    });

    coursesList.appendChild(tr);
    
    // Animation for adding
    tr.style.opacity = '0';
    tr.style.transform = 'translateY(15px)';
    setTimeout(() => {
        tr.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        tr.style.opacity = '1';
        tr.style.transform = 'translateY(0)';
    }, 10);
}

function calculateGPA() {
    const rows = document.querySelectorAll('#courses-list tr');
    let totalPoints = 0;
    let totalCredits = 0;

    rows.forEach(row => {
        const credit = parseFloat(row.querySelector('.course-credits').value);
        const grade = parseFloat(row.querySelector('.course-grade').value);
        
        if (!isNaN(credit) && grade >= 0) {
            totalPoints += credit * grade;
            totalCredits += credit;
        }
    });

    let termGPA = totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : "0.00";
    animateValue(termGpaEl, parseFloat(termGpaEl.innerText), parseFloat(termGPA), 600);

    const prevCredits = parseFloat(prevCreditsInput.value) || 0;
    const prevGpa = parseFloat(prevGpaInput.value) || 0;

    if (prevCredits > 0 && prevGpa > 0) {
        const cumPoints = (prevCredits * prevGpa) + totalPoints;
        const cumCredits = prevCredits + totalCredits;
        let cumGPA = (cumPoints / cumCredits).toFixed(2);
        animateValue(cumGpaEl, parseFloat(cumGpaEl.innerText), parseFloat(cumGPA), 600);
    } else {
        animateValue(cumGpaEl, parseFloat(cumGpaEl.innerText), parseFloat(termGPA), 600);
    }
}

function animateValue(obj, start, end, duration) {
    if(isNaN(start)) start = 0;
    if(isNaN(end)) end = 0;
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const easeProgress = 1 - Math.pow(1 - progress, 3); // Cubic ease out
        const val = easeProgress * (end - start) + start;
        obj.innerHTML = val.toFixed(2);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            obj.innerHTML = end.toFixed(2);
            // Add a little pop effect when it finishes
            obj.style.transform = 'scale(1.1)';
            setTimeout(() => {
                obj.style.transition = 'transform 0.2s';
                obj.style.transform = 'scale(1)';
            }, 50);
        }
    };
    window.requestAnimationFrame(step);
}

// Event Listeners for Prior GPA
prevCreditsInput.addEventListener('input', calculateGPA);
prevGpaInput.addEventListener('input', calculateGPA);

addCourseBtn.addEventListener('click', addCourseRow);

// Init with 5 rows for better initial look
for(let i=0; i<5; i++) addCourseRow();

// Interactive Spline viewer fallback (in case it fails to load)
window.addEventListener('load', () => {
    const viewer = document.querySelector('spline-viewer');
    viewer.addEventListener('error', () => {
        viewer.parentElement.style.background = 'linear-gradient(135deg, #1A7FD4, #0EA5C9)';
    });
});

// --- Share GPA Logic ---
function shareGPA() {
    const target = document.getElementById('gpa-card-capture');
    html2canvas(target, {
        backgroundColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#0f172a' : '#ffffff',
        scale: 2
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'dalil-gpa-result.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
