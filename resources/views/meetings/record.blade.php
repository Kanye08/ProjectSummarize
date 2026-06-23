<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center">
                    <span class="flex h-3 w-3 rounded-full bg-red-500 mr-3 animate-pulse"></span>
                    Record Meeting
                </h2>
            </div>
            <a href="{{ route('meetings.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-50 transition-all shadow-sm dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Exit
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen py-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-500">
        <div class="max-w-4xl mx-auto px-4 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left Column: Input Details --}}
                <div class="lg:col-span-5 space-y-6">
                    <div id="detailsCard" class="bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-all duration-500">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                <i class="fas fa-pen-nib text-indigo-700 dark:text-indigo-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Meeting Info</h3>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-400 mb-2">
                                    Meeting Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="meetingTitle" placeholder="e.g. Weekly Sync"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:border-indigo-600 focus:ring-0 transition-all shadow-sm">
                                <p id="titleError" class="mt-2 text-xs text-red-600 font-bold hidden">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Title required to start.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-400 mb-2">
                                    Notes (Optional)
                                </label>
                                <textarea id="meetingDescription" rows="4" placeholder="What's this about?"
                                          class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:border-indigo-600 focus:ring-0 transition-all shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Tip --}}
                    <div class="p-5 bg-indigo-700 rounded-3xl text-white shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="bg-white/20 p-2 rounded-xl">
                                <i class="fas fa-magic"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium leading-relaxed">
                                    Your audio is automatically transcribed using AI once saved. Ensure your mic is clear for best results.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Recording Console --}}
                <div class="lg:col-span-7">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                        
                        {{-- Status Header --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-8 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <div id="statusPill" class="flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-gray-200 text-gray-700">
                                <span id="statusDot" class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
                                <span id="statusText">Ready</span>
                            </div>
                            <div class="text-gray-400 text-xs font-bold uppercase tracking-widest" id="browserSupport">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i> System Active
                            </div>
                        </div>

                        <div class="p-8 lg:p-12 flex flex-col items-center justify-center min-h-[420px]">
                            
                            {{-- Visualizer & Timer Area --}}
                            <div class="relative w-full mb-12 flex flex-col items-center">
                                <div id="timerDisplay" class="text-6xl font-black font-mono tracking-tighter text-gray-900 dark:text-white mb-8 hidden">
                                    00:00:00
                                </div>

                                <div id="waveWrap" class="w-full h-24 hidden relative bg-gray-50 dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                                    <canvas id="visualizer" class="w-full h-full"></canvas>
                                </div>

                                {{-- Idle State Icon --}}
                                <div id="idleIcon" class="flex flex-col items-center transition-opacity duration-300">
                                    <div class="w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-4 border-2 border-red-100 dark:border-red-900/40">
                                        <i class="fas fa-microphone text-4xl text-red-600"></i>
                                    </div>
                                    <p class="text-gray-500 font-bold uppercase text-xs tracking-widest">Tap to start recording</p>
                                </div>
                            </div>

                            {{-- Control Actions --}}
                            <div class="flex flex-wrap items-center justify-center gap-6 w-full">
                                <button id="startBtn" class="group relative flex items-center justify-center w-20 h-20 bg-red-600 hover:bg-red-700 text-white rounded-full transition-all shadow-xl hover:scale-110 active:scale-95">
                                    <i class="fas fa-microphone text-2xl"></i>
                                </button>

                                <button id="pauseBtn" class="hidden items-center justify-center w-16 h-16 bg-amber-500 hover:bg-amber-600 text-white rounded-full transition-all shadow-lg hover:scale-105">
                                    <i class="fas fa-pause"></i>
                                </button>

                                <button id="resumeBtn" class="hidden items-center justify-center w-16 h-16 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full transition-all shadow-lg hover:scale-105">
                                    <i class="fas fa-play"></i>
                                </button>

                                <button id="stopBtn" class="hidden items-center justify-center w-16 h-16 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-full transition-all shadow-lg hover:scale-105">
                                    <i class="fas fa-check text-xl"></i>
                                </button>
                                
                                <button id="discardBtn" class="hidden items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full transition-all hover:bg-red-50 hover:text-red-600">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            {{-- Progress Overlay --}}
                            <div id="uploadSection" class="w-full mt-10 space-y-4 hidden">
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">Syncing with server...</span>
                                    <span class="text-xs text-gray-400" id="progressPercent">0%</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    <div id="uploadBar" class="bg-indigo-600 h-full transition-all duration-500 ease-out" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (() => {
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
        const idleIcon      = document.getElementById('idleIcon');

        let mediaRecorder   = null;
        let audioChunks      = [];
        let audioContext    = null;
        let analyser        = null;
        let animationId     = null;
        let timerInterval   = null;
        let startTime       = null;
        let pausedMs        = 0;
        let pauseStart      = null;
        let recordingStart  = null;

        function show(el)  { el.classList.remove('hidden'); if(el.id === 'startBtn') el.classList.add('flex'); }
        function hide(el)  { el.classList.add('hidden'); if(el.id === 'startBtn') el.classList.remove('flex'); }

        function setStatus(type, text) {
            statusText.textContent = text;
            const map = {
                idle:      ['bg-gray-400',  'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                recording: ['bg-red-500 animate-pulse', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
                paused:    ['bg-amber-400', 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
                uploading: ['bg-indigo-500 animate-bounce', 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'],
                done:      ['bg-emerald-500', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
            };
            const [dot, pill] = map[type] || map.idle;
            statusDot.className = 'w-2.5 h-2.5 rounded-full ' + dot;
            statusPill.className = 'flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest ' + pill;
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

        function stopTimer() { clearInterval(timerInterval); }

        function startVisualiser(stream) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser     = audioContext.createAnalyser();
            analyser.fftSize = 256;
            const src = audioContext.createMediaStreamSource(stream);
            src.connect(analyser);
            const bufLen = analyser.frequencyBinCount;
            const dataArr = new Uint8Array(bufLen);

            function draw() {
                animationId = requestAnimationFrame(draw);
                analyser.getByteFrequencyData(dataArr);
                canvas.width = canvas.offsetWidth;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                const barW = (canvas.width / bufLen) * 2.5;
                let x = 0;
                for (let i = 0; i < bufLen; i++) {
                    const barH = (dataArr[i] / 255) * canvas.height;
                    ctx.fillStyle = `rgba(99, 102, 241, ${dataArr[i]/255})`;
                    ctx.beginPath();
                    ctx.roundRect(x, (canvas.height - barH)/2, barW - 2, barH, 4);
                    ctx.fill();
                    x += barW;
                }
            }
            draw();
        }

        function stopVisualiser() {
            cancelAnimationFrame(animationId);
            if (audioContext) audioContext.close();
        }

        startBtn.addEventListener('click', async () => {
            const titleInput = document.getElementById('meetingTitle');
            const title = titleInput.value.trim();
            if (!title) {
                document.getElementById('titleError').classList.remove('hidden');
                titleInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                return;
            }
            document.getElementById('titleError').classList.add('hidden');
            titleInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
            
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus' : 'audio/webm';
                mediaRecorder = new MediaRecorder(stream, { mimeType, audioBitsPerSecond: 64000 });
                audioChunks = [];
                recordingStart = Date.now();
                mediaRecorder.ondataavailable = e => { if (e.data.size > 0) audioChunks.push(e.data); };
                mediaRecorder.onstop = handleStop;
                mediaRecorder.start(100);
                startTime = Date.now();
                pausedMs = 0;
                startTimer();
                startVisualiser(stream);
                
                setStatus('recording', 'Live');
                show(timerDisplay); show(waveWrap); hide(idleIcon);
                hide(startBtn); show(pauseBtn); show(stopBtn); show(discardBtn);
                detailsCard.style.opacity = '0.5';
                detailsCard.style.pointerEvents = 'none';
            } catch (err) {
                alert('Microphone access denied.');
            }
        });

        pauseBtn.addEventListener('click', () => {
            if (mediaRecorder?.state === 'recording') {
                mediaRecorder.pause();
                pauseStart = Date.now();
                stopTimer();
                stopVisualiser();
                setStatus('paused', 'On Hold');
                hide(pauseBtn); show(resumeBtn);
            }
        });

        resumeBtn.addEventListener('click', () => {
            if (mediaRecorder?.state === 'paused') {
                pausedMs += Date.now() - pauseStart;
                mediaRecorder.resume();
                startTimer();
                startVisualiser(mediaRecorder.stream);
                setStatus('recording', 'Live');
                hide(resumeBtn); show(pauseBtn);
            }
        });

        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                stopTimer(); stopVisualiser();
                mediaRecorder.stream.getTracks().forEach(t => t.stop());
                mediaRecorder.stop();
                setStatus('uploading', 'Processing');
                hide(pauseBtn); hide(resumeBtn); hide(stopBtn); hide(discardBtn);
            }
        });

        discardBtn.addEventListener('click', () => {
            if (!confirm('Discard recording?')) return;
            stopTimer(); stopVisualiser();
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stream.getTracks().forEach(t => t.stop());
                mediaRecorder.stop();
            }
            resetUI();
        });

        function resetUI() {
            audioChunks = [];
            setStatus('idle', 'Ready');
            hide(timerDisplay); hide(waveWrap); hide(uploadSection); show(idleIcon);
            timerDisplay.textContent = '00:00:00';
            show(startBtn);
            hide(pauseBtn); hide(resumeBtn); hide(stopBtn); hide(discardBtn);
            detailsCard.style.opacity = '1';
            detailsCard.style.pointerEvents = 'auto';
        }

        async function handleStop() {
            if (audioChunks.length === 0) { resetUI(); return; }
            const durationSec = Math.round((Date.now() - recordingStart - pausedMs) / 1000);
            const blob = new Blob(audioChunks, { type: mediaRecorder.mimeType });
            const file = new File([blob], `rec-${Date.now()}.webm`, { type: mediaRecorder.mimeType });

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('title', document.getElementById('meetingTitle').value.trim());
            fd.append('description', document.getElementById('meetingDescription').value.trim());
            fd.append('start_time', new Date().toISOString());
            fd.append('duration', durationSec);
            fd.append('audio', file);

            show(uploadSection);
            animateBar(40);

            try {
                const res = await fetch('{{ route("meetings.record.save") }}', {
                    method: 'POST', body: fd, headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error();
                animateBar(100);
                setStatus('done', 'Success');
                setTimeout(() => { window.location.href = data.redirect; }, 800);
            } catch (err) {
                setStatus('idle', 'Error');
                alert('Upload failed');
                resetUI();
            }
        }

        function animateBar(target) {
            uploadBar.style.width = target + '%';
            document.getElementById('progressPercent').textContent = target + '%';
        }
    })();
    </script>
    @endpush
</x-app-layout>