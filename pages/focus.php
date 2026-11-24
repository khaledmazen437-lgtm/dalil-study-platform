<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$currentPage = '/focus';
$pageTitle   = 'مركز التركيز (Pomodoro)';
$bp          = BASE_PATH;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container" style="max-width:900px; padding: 40px 15px; min-height: 85vh;">

    <div style="text-align: center; margin-bottom: 40px;">
        <div style="display:inline-flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:20px;background:var(--card);border:1px solid var(--border);box-shadow:0 10px 30px var(--shadow);font-size:32px;margin-bottom:20px; color: var(--primary);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <h1 style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:clamp(2rem, 5vw, 2.5rem);color:var(--text);margin-bottom:10px;">مركز التركيز</h1>
        <p style="color:var(--muted); font-size: 15px;">استخدم تقنية بومودورو لزيادة إنتاجيتك وتقليل التشتت</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">

        <div class="card" style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px;">
            <div id="timer-mode" style="font-weight: 700; color: var(--accent); margin-bottom: 15px; font-size: 16px; letter-spacing: 1px;">وقت الدراسة</div>

            <div style="position: relative; width: 220px; height: 220px; display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
                <svg width="220" height="220" viewBox="0 0 220 220" style="position: absolute; transform: rotate(-90deg);">
                    <circle cx="110" cy="110" r="100" fill="none" stroke="var(--border)" stroke-width="8"></circle>
                    <circle id="timer-progress" cx="110" cy="110" r="100" fill="none" stroke="var(--primary)" stroke-width="8" stroke-dasharray="628" stroke-dashoffset="0" style="transition: stroke-dashoffset 1s linear;"></circle>
                </svg>
                <div id="timer-display" style="font-family: 'Tajawal', sans-serif; font-size: 4rem; font-weight: 900; color: var(--text);">25:00</div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: center;">
                <button id="btn-start" onclick="toggleTimer()" style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary); color: var(--card); border: none; font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 8px 20px var(--shadow-lg); transition: 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <svg id="icon-play" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <svg id="icon-pause" style="display:none;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                </button>
                <button onclick="resetTimer()" style="width: 60px; height: 60px; border-radius: 50%; background: var(--card); color: var(--text); border: 2px solid var(--border); font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s;" onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border)'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                </button>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button onclick="setMode('work')" style="background:var(--primary-bg); border:none; color:var(--primary); padding:8px 16px; border-radius:10px; font-weight:700; cursor:pointer; font-family:'Cairo',sans-serif;">دراسة (25)</button>
                <button onclick="setMode('break')" style="background:var(--bg); border:1px solid var(--border); color:var(--text); padding:8px 16px; border-radius:10px; font-weight:700; cursor:pointer; font-family:'Cairo',sans-serif;">راحة (5)</button>
            </div>
        </div>

        <!-- Ambient Noise & Quick Tasks -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <div class="card" style="padding: 24px;">
                <h3 style="font-family:'Tajawal',sans-serif; margin-bottom: 16px; display:flex; align-items:center; gap:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                    أصوات محيطية
                </h3>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <button class="sound-btn" data-sound="rain" style="padding:16px; border:1px solid var(--border); background:var(--bg); border-radius:12px; display:flex; flex-direction:column; align-items:center; gap:8px; cursor:pointer; transition:0.2s;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 16.2A4.5 4.5 0 0 0 17.5 8h-1.8A7 7 0 1 0 4 14.9"></path><path d="M16 14v6"></path><path d="M8 14v6"></path><path d="M12 16v6"></path></svg>
                        <span style="font-size:13px; font-weight:600;">مطر</span>
                    </button>
                    <button class="sound-btn" data-sound="cafe" style="padding:16px; border:1px solid var(--border); background:var(--bg); border-radius:12px; display:flex; flex-direction:column; align-items:center; gap:8px; cursor:pointer; transition:0.2s;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                        <span style="font-size:13px; font-weight:600;">مقهى</span>
                    </button>
                    <button class="sound-btn" data-sound="lofi" style="padding:16px; border:1px solid var(--border); background:var(--bg); border-radius:12px; display:flex; flex-direction:column; align-items:center; gap:8px; cursor:pointer; transition:0.2s;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                        <span style="font-size:13px; font-weight:600;">Lo-Fi</span>
                    </button>
                    <button class="sound-btn" data-sound="nature" style="padding:16px; border:1px solid var(--border); background:var(--bg); border-radius:12px; display:flex; flex-direction:column; align-items:center; gap:8px; cursor:pointer; transition:0.2s;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22v-6"></path><path d="M8 12.5C8 8.5 9 5 12 5s4 3.5 4 7.5"></path><path d="M16 12.5c0 4-1 7.5-4 7.5s-4-3.5-4-7.5"></path><path d="M12 5A4 4 0 0 0 8 1C6 1 4 3 4 5s2 4 4 4"></path><path d="M12 5a4 4 0 0 1 4-4c2 0 4 2 4 4s-2 4-4 4"></path></svg>
                        <span style="font-size:13px; font-weight:600;">طبيعة</span>
                    </button>
                </div>
            </div>

            <div class="card" style="padding: 24px; flex:1;">
                <h3 style="font-family:'Tajawal',sans-serif; margin-bottom: 16px; display:flex; align-items:center; gap:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    مهام الجلسة
                </h3>
                <div style="display:flex; gap:8px; margin-bottom:16px;">
                    <input type="text" id="task-input" placeholder="أضف مهمة للتركيز عليها..." style="flex:1; padding:10px 14px; border:1px solid var(--border); border-radius:10px; background:var(--bg); color:var(--text); outline:none; font-family:'Cairo',sans-serif;">
                    <button onclick="addTask()" style="background:var(--primary); color:var(--card); border:none; border-radius:10px; width:42px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </div>
                <div id="tasks-list" style="display:flex; flex-direction:column; gap:8px;">

                </div>
            </div>
        </div>
    </div>
</div>

<script>

let TIME_LIMIT = 25 * 60;
let timePassed = 0;
let timeLeft = TIME_LIMIT;
let timerInterval = null;
let isRunning = false;
let currentMode = 'work'; // work, break

const timerDisplay = document.getElementById('timer-display');
const timerProgress = document.getElementById('timer-progress');
const iconPlay = document.getElementById('icon-play');
const iconPause = document.getElementById('icon-pause');
const modeText = document.getElementById('timer-mode');

function formatTime(time) {
    const minutes = Math.floor(time / 60);
    let seconds = time % 60;
    if (seconds < 10) seconds = `0${seconds}`;
    return `${minutes}:${seconds}`;
}

function setMode(mode) {
    clearInterval(timerInterval);
    isRunning = false;
    iconPlay.style.display = 'block';
    iconPause.style.display = 'none';
    currentMode = mode;
    timePassed = 0;

    if(mode === 'work') {
        TIME_LIMIT = 25 * 60;
        modeText.innerText = 'وقت الدراسة';
        modeText.style.color = 'var(--accent)';
        timerProgress.style.stroke = 'var(--primary)';
    } else {
        TIME_LIMIT = 5 * 60;
        modeText.innerText = 'وقت الراحة';
        modeText.style.color = 'var(--success)';
        timerProgress.style.stroke = 'var(--success)';
    }

    timeLeft = TIME_LIMIT;
    timerDisplay.innerText = formatTime(timeLeft);
    setCircleDasharray();
}

function toggleTimer() {
    if (isRunning) {
        clearInterval(timerInterval);
        isRunning = false;
        iconPlay.style.display = 'block';
        iconPause.style.display = 'none';
    } else {
        timerInterval = setInterval(() => {
            timePassed += 1;
            timeLeft = TIME_LIMIT - timePassed;
            timerDisplay.innerText = formatTime(timeLeft);
            setCircleDasharray();

            if (timeLeft === 0) {
                clearInterval(timerInterval);
                isRunning = false;
                iconPlay.style.display = 'block';
                iconPause.style.display = 'none';

                if (currentMode === 'work') {
                    let stats = JSON.parse(localStorage.getItem('dalil-study-stats')) || {};
                    let today = new Date().toISOString().split('T')[0];
                    stats[today] = (stats[today] || 0) + 25;
                    localStorage.setItem('dalil-study-stats', JSON.stringify(stats));

                    if (window.Gamification) {
                        window.Gamification.addXP(50);
                    } else {
                        alert("عظيم جداً! لقد أتممت جلسة دراسة (25 دقيقة) وتم تسجيلها في لوحة الإحصائيات الخاصة بك 🏆");
                    }
                } else {
                    alert("انتهى وقت الراحة، يمكنك العودة للتركيز الآن!");
                }
            }
        }, 1000);
        isRunning = true;
        iconPlay.style.display = 'none';
        iconPause.style.display = 'block';
    }
}

function resetTimer() {
    setMode(currentMode);
}

function setCircleDasharray() {
    const rawTimeFraction = timeLeft / TIME_LIMIT;
    const timeFraction = rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
    const dashoffset = 628 - (timeFraction * 628);
    timerProgress.style.strokeDashoffset = dashoffset;
}

function addTask() {
    const input = document.getElementById('task-input');
    const val = input.value.trim();
    if(!val) return;

    const div = document.createElement('div');
    div.style.cssText = 'display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:var(--bg); border:1px solid var(--border); border-radius:8px; font-size:13px; animation: fadeUp 0.3s;';

    div.innerHTML = `
        <div style="display:flex; align-items:center; gap:10px;">
            <input type="checkbox" style="width:16px; height:16px; accent-color:var(--primary); cursor:pointer;" onchange="this.nextElementSibling.style.textDecoration = this.checked ? 'line-through' : 'none'; this.nextElementSibling.style.opacity = this.checked ? '0.5' : '1';">
            <span>${val}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:var(--danger); cursor:pointer; display:flex; align-items:center;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
    `;

    document.getElementById('tasks-list').appendChild(div);
    input.value = '';
}

document.getElementById('task-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') addTask();
});

// Sound buttons styling mock
document.querySelectorAll('.sound-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const isActive = this.style.borderColor === 'var(--primary)';
        document.querySelectorAll('.sound-btn').forEach(b => {
            b.style.borderColor = 'var(--border)';
            b.style.background = 'var(--bg)';
        });
        if(!isActive) {
            this.style.borderColor = 'var(--primary)';
            this.style.background = 'var(--primary-bg)';
        }
        // Audio playing logic would go here
    });
});

setMode('work');
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
