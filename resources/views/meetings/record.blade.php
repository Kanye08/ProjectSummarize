<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-microphone text-red-600 mr-3"></i>Record Meeting
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                    Record live audio directly in your browser
                </p>
            </div>
            <a href="{{ route('meetings.index') }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ── Meeting details ─────────────────────────────────────────── --}}
            <div id="detailsCard"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 transition-opacity duration-300">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">
                    <i class="fas fa-info-circle text-primary mr-2"></i>Meeting Details
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Meeting Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="meetingTitle"
                               placeholder="e.g., Weekly Team Standup"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary">
                        <p id="titleError" class="mt-1 text-sm text-red-600 hidden">
                            Please enter a meeting title before recording.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description <span class="text-gray-400 text-xs">(optional)</span>
                        </label>
                        <textarea id="meetingDescription"
                                  rows="3"
                                  placeholder="Add notes about this meeting..."
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </div>
            </div>

            {{-- ── Recorder ────────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">

                {{-- Status pill --}}
                <div class="flex justify-center mb-6">
                    <div id="statusPill"
                         class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">
                        <span id="statusDot" class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
                        <span id="statusText">Ready to Record</span>
                    </div>
                </div>

                {{-- Timer --}}
                <div id="timerDisplay"
                     class="text-center text-5xl font-mono font-bold text-gray-800 dark:text-white mb-6 hidden">
                    00:00:00
                </div>

                {{-- Waveform --}}
                <div id="waveWrap" class="mb-6 hidden">
                    <canvas id="visualizer"
                            class="w-full rounded-xl bg-gray-50 dark:bg-gray-700"
                            height="80"></canvas>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-wrap justify-center gap-4">
                    <button id="startBtn"
                            class="btn-record px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-full shadow-lg transition transform hover:scale-105 flex items-center gap-3">
                        <i class="fas fa-microphone text-xl"></i>
                        <span>Start Recording</span>
                    </button>

                    <button id="pauseBtn"
                            class="btn-record hidden px-8 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-full shadow-lg transition transform hover:scale-105 flex items-center gap-3">
                        <i class="fas fa-pause text-xl"></i>
                        <span>Pause</span>
                    </button>

                    <button id="resumeBtn"
                            class="btn-record hidden px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full shadow-lg transition transform hover:scale-105 flex items-center gap-3">
                        <i class="fas fa-play text-xl"></i>
                        <span>Resume</span>
                    </button>

                    <button id="stopBtn"
                            class="btn-record hidden px-8 py-4 bg-gray-800 hover:bg-gray-900 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-bold rounded-full shadow-lg transition transform hover:scale-105 flex items-center gap-3">
                        <i class="fas fa-stop text-xl"></i>
                        <span>Stop & Save</span>
                    </button>

                    <button id="discardBtn"
                            class="btn-record hidden px-8 py-4 bg-red-200 hover:bg-red-300 text-red-800 font-bold rounded-full shadow transition flex items-center gap-3">
                        <i class="fas fa-trash text-xl"></i>
                        <span>Discard</span>
                    </button>
                </div>

                {{-- Upload progress --}}
                <div id="uploadSection" class="mt-6 hidden">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 text-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Uploading and processing your recording…
                    </p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div id="uploadBar"
                             class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-300"
                             style="width: 0%"></div>
                    </div>
                </div>
            </div>

            {{-- ── Tips ─────────────────────────────────────────────────────── --}}
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl p-5">
                <div class="flex gap-3">
                    <i class="fas fa-lightbulb text-blue-600 dark:text-blue-400 mt-1 flex-shrink-0"></i>
                    <div class="text-sm text-blue-800 dark:text-blue-300">
                        <p class="font-semibold mb-2">Tips for best results:</p>
                        <ul class="space-y-1 list-disc ml-4">
                            <li>Allow microphone access when prompted</li>
                            <li>Use a quiet environment for clearer transcription</li>
                            <li>You can pause and resume at any time</li>
                            <li>Recording is transcribed automatically after saving</li>
                            <li>Supported in Chrome, Firefox, Edge and Safari</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    (() => {
        // ── DOM refs ──────────────────────────────────────────────────────────
        const startBtn      = document.getElementById('startBtn');
        const pauseBtn      = document.getElementById('pauseBtn');
        const resumeBtn     = document.getElementById('resumeBtn');
        const stopBtn       = document.getElementById('stopBtn');
        const discardBtn    = document.getElementById('discardBtn');
        const timerDisplay  = document.getElementById('timerDisplay');
        const waveWrap      = document.getElementById('waveWrap');
        const canvas        = document.getElementById('visualizer');
        const ctx           = canvas.getContext('2d');
        const statusPill    = document.getElementById('statusPill');
        const statusDot     = document.getElementById('statusDot');
        const statusText    = document.getElementById('statusText');
        const detailsCard   = document.getElementById('detailsCard');
        const uploadSection = document.getElementById('uploadSection');
        const uploadBar     = document.getElementById('uploadBar');

        // ── State ─────────────────────────────────────────────────────────────
        let mediaRecorder   = null;
        let audioChunks     = [];
        let audioContext    = null;
        let analyser        = null;
        let animationId     = null;
        let timerInterval   = null;
        let startTime       = null;
        let pausedMs        = 0;
        let pauseStart      = null;
        let recordingStart  = null; // wall-clock start for duration calc

        // ── Helpers ───────────────────────────────────────────────────────────
        function show(el)  { el.classList.remove('hidden'); }
        function hide(el)  { el.classList.add('hidden'); }

        function setStatus(type, text) {
            statusText.textContent = text;
            statusDot.className = 'w-2.5 h-2.5 rounded-full ';
            statusPill.className = 'inline-flex items-center gap-2 px-5 py-2 rounded-full font-medium ';

            const map = {
                idle:       ['bg-gray-400',  'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'],
                recording:  ['bg-red-500 animate-pulse', 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300'],
                paused:     ['bg-yellow-400', 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'],
                uploading:  ['bg-blue-500 animate-pulse', 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'],
                done:       ['bg-green-500', 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300'],
            };

            const [dot, pill] = map[type] || map.idle;
            statusDot.className  += dot;
            statusPill.className += pill;
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                const elapsed = Date.now() - startTime - pausedMs;
                const h = Math.floor(elapsed / 3600000);
                const m = Math.floor((elapsed % 3600000) / 60000);
                const s = Math.floor((elapsed % 60000) / 1000);
                timerDisplay.textContent =
                    String(h).padStart(2,'0') + ':' +
                    String(m).padStart(2,'0') + ':' +
                    String(s).padStart(2,'0');
            }, 500);
        }

        function stopTimer() {
            clearInterval(timerInterval);
        }

        // ── Visualiser ────────────────────────────────────────────────────────
        function startVisualiser(stream) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser     = audioContext.createAnalyser();
            analyser.fftSize = 512;

            const src = audioContext.createMediaStreamSource(stream);
            src.connect(analyser);

            const bufLen = analyser.frequencyBinCount;
            const dataArr = new Uint8Array(bufLen);

            function draw() {
                animationId = requestAnimationFrame(draw);
                analyser.getByteFrequencyData(dataArr);

                canvas.width = canvas.offsetWidth;  // responsive
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                const barW = (canvas.width / bufLen) * 2;
                let x = 0;

                for (let i = 0; i < bufLen; i++) {
                    const barH = (dataArr[i] / 255) * canvas.height;
                    const g    = ctx.createLinearGradient(0, canvas.height - barH, 0, canvas.height);
                    g.addColorStop(0, '#60A5FA');
                    g.addColorStop(1, '#1D4ED8');
                    ctx.fillStyle = g;
                    ctx.beginPath();
                    ctx.roundRect(x, canvas.height - barH, barW - 1, barH, 2);
                    ctx.fill();
                    x += barW;
                }
            }
            draw();
        }

        function stopVisualiser() {
            cancelAnimationFrame(animationId);
            if (audioContext) audioContext.close();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        // ── Start ─────────────────────────────────────────────────────────────
        startBtn.addEventListener('click', async () => {
            const title = document.getElementById('meetingTitle').value.trim();
            if (!title) {
                document.getElementById('titleError').classList.remove('hidden');
                document.getElementById('meetingTitle').focus();
                return;
            }
            document.getElementById('titleError').classList.add('hidden');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true }
                });

                // Prefer webm; fall back to whatever browser supports
                const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                    ? 'audio/webm;codecs=opus'
                    : MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')
                        ? 'audio/ogg;codecs=opus'
                        : 'audio/webm';

                mediaRecorder = new MediaRecorder(stream, { mimeType });
                audioChunks   = [];
                recordingStart = Date.now();

                mediaRecorder.ondataavailable = e => { if (e.data.size > 0) audioChunks.push(e.data); };
                mediaRecorder.onstop          = handleStop;

                mediaRecorder.start(100);
                startTime = Date.now();
                pausedMs  = 0;
                startTimer();
                startVisualiser(stream);

                // UI
                setStatus('recording', 'Recording…');
                show(timerDisplay); show(waveWrap);
                hide(startBtn); show(pauseBtn); show(stopBtn); show(discardBtn);
                detailsCard.style.opacity        = '0.5';
                detailsCard.style.pointerEvents  = 'none';

            } catch (err) {
                console.error(err);
                alert('Microphone access denied. Please allow it in your browser settings.');
            }
        });

        // ── Pause ─────────────────────────────────────────────────────────────
        pauseBtn.addEventListener('click', () => {
            if (mediaRecorder?.state === 'recording') {
                mediaRecorder.pause();
                pauseStart = Date.now();
                stopTimer();
                stopVisualiser();
                setStatus('paused', 'Paused');
                hide(pauseBtn); show(resumeBtn);
            }
        });

        // ── Resume ────────────────────────────────────────────────────────────
        resumeBtn.addEventListener('click', () => {
            if (mediaRecorder?.state === 'paused') {
                pausedMs += Date.now() - pauseStart;
                mediaRecorder.resume();
                startTimer();
                startVisualiser(mediaRecorder.stream);
                setStatus('recording', 'Recording…');
                hide(resumeBtn); show(pauseBtn);
            }
        });

        // ── Stop ─────────────────────────────────────────────────────────────
        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                stopTimer(); stopVisualiser();
                mediaRecorder.stream.getTracks().forEach(t => t.stop());
                mediaRecorder.stop();  // triggers onstop → handleStop
                setStatus('uploading', 'Saving recording…');
                hide(pauseBtn); hide(resumeBtn); hide(stopBtn); hide(discardBtn);
            }
        });

        // ── Discard ───────────────────────────────────────────────────────────
        discardBtn.addEventListener('click', () => {
            if (!confirm('Discard this recording? This cannot be undone.')) return;
            stopTimer(); stopVisualiser();
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stream.getTracks().forEach(t => t.stop());
                mediaRecorder.stop();
            }
            resetUI();
        });

        // ── Reset UI ──────────────────────────────────────────────────────────
        function resetUI() {
            audioChunks = [];
            setStatus('idle', 'Ready to Record');
            hide(timerDisplay); hide(waveWrap); hide(uploadSection);
            timerDisplay.textContent = '00:00:00';
            show(startBtn);
            hide(pauseBtn); hide(resumeBtn); hide(stopBtn); hide(discardBtn);
            detailsCard.style.opacity       = '1';
            detailsCard.style.pointerEvents = 'auto';
        }

        // ── Handle stop → upload ──────────────────────────────────────────────
        async function handleStop() {
            if (audioChunks.length === 0) { resetUI(); return; }

            // Calculate actual duration in seconds
            const durationSec = Math.round((Date.now() - recordingStart - pausedMs) / 1000);

            // Build blob & file
            const mimeType = mediaRecorder.mimeType || 'audio/webm';
            const ext      = mimeType.includes('ogg') ? 'ogg' : 'webm';
            const blob     = new Blob(audioChunks, { type: mimeType });
            const file     = new File([blob], `recording-${Date.now()}.${ext}`, { type: mimeType });

            // FormData
            const fd = new FormData();
            fd.append('_token',      '{{ csrf_token() }}');
            fd.append('title',       document.getElementById('meetingTitle').value.trim());
            fd.append('description', document.getElementById('meetingDescription').value.trim());
            fd.append('start_time',  new Date().toISOString());
            fd.append('duration',    durationSec);
            fd.append('audio',       file);

            // Show progress bar
            show(uploadSection);
            animateBar(30); // fake first jump

            try {
                const res = await fetch('{{ route("meetings.record.save") }}', {
                    method: 'POST',
                    body:   fd,
                    headers: { 'Accept': 'application/json' }
                });

                animateBar(90);

                const data = await res.json();

                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Upload failed');
                }

                animateBar(100);
                setStatus('done', 'Saved! Redirecting…');

                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 800);

            } catch (err) {
                console.error(err);
                uploadBar.style.width = '0%';
                hide(uploadSection);
                setStatus('idle', 'Upload failed – please try again');
                alert('Could not save recording: ' + err.message);
                resetUI();
            }
        }

        function animateBar(target) {
            uploadBar.style.width = target + '%';
        }

    })();
    </script>
    @endpush
</x-app-layout>