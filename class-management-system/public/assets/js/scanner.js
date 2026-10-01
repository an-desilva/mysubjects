// Barcode & QR Code Attendance Scanner Handler
document.addEventListener('DOMContentLoaded', () => {
    const barcodeInput = document.getElementById('barcodeInput');
    const courseSelect = document.getElementById('scanCourseId');
    const resultBox = document.getElementById('scanResultBox');
    const resultIcon = document.getElementById('scanResultIcon');
    const resultName = document.getElementById('scanResultName');
    const resultDetail = document.getElementById('scanResultDetail');
    const resultTime = document.getElementById('scanResultTime');
    const logTableBody = document.getElementById('attendanceLogTableBody');

    if (!barcodeInput) return;

    // Audio feedback synth for scan beep
    function playBeep(success = true) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = success ? 'sine' : 'sawtooth';
            osc.frequency.value = success ? 880 : 220; // A5 for pass, A3 for error
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + (success ? 0.15 : 0.4));
        } catch (e) {
            console.log('Audio API not available:', e);
        }
    }

    // Auto focus input on page load and click anywhere
    barcodeInput.focus();
    document.addEventListener('click', (e) => {
        if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
            barcodeInput.focus();
        }
    });

    // Handle scan keypress
    barcodeInput.addEventListener('keypress', async (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = barcodeInput.value.trim();
            const courseId = courseSelect ? courseSelect.value : 0;
            const statusRadio = document.querySelector('input[name="scanStatus"]:checked');
            const status = statusRadio ? statusRadio.value : 'present';

            if (!code) return;

            barcodeInput.disabled = true;
            barcodeInput.classList.add('opacity-50');

            try {
                const apiUrl = window.SCAN_API_URL || 'api/attendance/scan';
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        student_code: code,
                        course_id: courseId,
                        status: status
                    })
                });

                const res = await response.json();

                resultBox.classList.remove('hidden', 'bg-emerald-950/60', 'border-emerald-500/40', 'bg-rose-950/60', 'border-rose-500/40');

                if (res.success) {
                    playBeep(true);
                    resultBox.classList.add('flex', 'bg-emerald-950/60', 'border-emerald-500/40');
                    resultIcon.className = 'h-12 w-12 rounded-xl flex items-center justify-center text-xl shrink-0 bg-emerald-500/20 text-emerald-400';
                    resultIcon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
                    resultName.textContent = `${res.student.name} (${res.student.code})`;
                    resultDetail.textContent = `Course: ${res.student.course_title} | Status: ${status.toUpperCase()}`;
                    resultTime.textContent = res.student.time;

                    // Append row to log table dynamically
                    if (logTableBody) {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-800/30 animate-pulse';
                        tr.innerHTML = `
                            <td class="py-3 px-4 font-semibold text-white">${res.student.name}</td>
                            <td class="py-3 px-4 font-mono text-xs text-indigo-300">${res.student.code}</td>
                            <td class="py-3 px-4 text-xs text-slate-300">${res.student.course_title}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">${status}</span>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-slate-400">${res.student.time}</td>
                            <td class="py-3 px-4 text-xs text-slate-400">Scanner</td>
                        `;
                        logTableBody.insertBefore(tr, logTableBody.firstChild);
                    }
                } else {
                    playBeep(false);
                    resultBox.classList.add('flex', 'bg-rose-950/60', 'border-rose-500/40');
                    resultIcon.className = 'h-12 w-12 rounded-xl flex items-center justify-center text-xl shrink-0 bg-rose-500/20 text-rose-400';
                    resultIcon.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                    resultName.textContent = 'Scan Verification Error';
                    resultDetail.textContent = res.message || 'Student not registered or code invalid.';
                    resultTime.textContent = new Date().toLocaleTimeString();
                }
            } catch (err) {
                console.error('Scan Error:', err);
                playBeep(false);
            } finally {
                barcodeInput.value = '';
                barcodeInput.disabled = false;
                barcodeInput.classList.remove('opacity-50');
                barcodeInput.focus();
            }
        }
    });
});
