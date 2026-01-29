<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ $meeting->title }}
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                    <i class="fas fa-calendar mr-2"></i>{{ $meeting->start_time->format('F j, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('meetings.edit', $meeting) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('meetings.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Status Alert -->
            @if($meeting->processing_status !== 'completed')
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-spinner fa-spin text-yellow-600 dark:text-yellow-400 mr-3"></i>
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-300">Processing in Progress</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                Status: <strong>{{ ucfirst($meeting->processing_status) }}</strong>. 
                                @if($meeting->processing_status === 'transcribing')
                                    We're transcribing your audio. This usually takes 2-5 minutes.
                                @elseif($meeting->processing_status === 'summarizing')
                                    We're generating your summary and insights.
                                @else
                                    Your meeting is being processed.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($meeting->processing_status === 'failed')
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-3"></i>
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-300">Processing Failed</p>
                            <p class="text-sm text-red-700 dark:text-red-400">{{ $meeting->error_message }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Audio Player & Transcript -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Audio Player with Sync -->
                    @if($meeting->audio_file_path)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-play-circle text-primary mr-3"></i>Audio Player
                            </h3>
                            
                            <div id="waveform" class="mb-4"></div>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <button id="playPauseBtn" class="w-12 h-12 bg-primary hover:bg-blue-600 text-white rounded-full transition">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <span id="currentTime" class="text-sm text-gray-600 dark:text-gray-400">0:00</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">/</span>
                                    <span id="duration" class="text-sm text-gray-600 dark:text-gray-400">{{ $meeting->formatted_duration }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <label for="playbackRate" class="text-sm text-gray-600 dark:text-gray-400">Speed:</label>
                                    <select id="playbackRate" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                        <option value="0.5">0.5x</option>
                                        <option value="0.75">0.75x</option>
                                        <option value="1" selected>1x</option>
                                        <option value="1.25">1.25x</option>
                                        <option value="1.5">1.5x</option>
                                        <option value="2">2x</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="fas fa-volume-up text-gray-600 dark:text-gray-400"></i>
                                <input type="range" id="volumeControl" min="0" max="100" value="100" class="flex-1">
                            </div>
                        </div>
                    @endif

                    <!-- Transcript with Sync -->
                    @if($meeting->transcript)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <i class="fas fa-file-alt text-primary mr-3"></i>Transcript
                                </h3>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           id="searchTranscript" 
                                           placeholder="Search transcript..."
                                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm">
                                    <a href="{{ route('transcripts.download', $meeting) }}" 
                                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>

                            <div id="transcriptContainer" class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                                @foreach($meeting->transcript->segments as $segment)
                                    <div class="transcript-segment p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer transition"
                                         data-start="{{ $segment['start'] }}"
                                         data-end="{{ $segment['end'] }}"
                                         data-segment-id="{{ $segment['id'] }}">
                                        <div class="flex items-start gap-3">
                                            <span class="text-xs text-blue-600 dark:text-blue-400 font-mono flex-shrink-0">
                                                {{ gmdate('i:s', (int)$segment['start']) }}
                                            </span>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 flex-1">
                                                {{ $segment['text'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Summary & Actions -->
                <div class="space-y-6">
                    <!-- Meeting Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Meeting Details</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class="fas fa-file-audio w-6"></i>
                                <span>{{ $meeting->formatted_size }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class="fas fa-clock w-6"></i>
                                <span>{{ $meeting->formatted_duration }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class="fas fa-language w-6"></i>
                                <span>{{ strtoupper($meeting->transcript->language ?? 'N/A') }}</span>
                            </div>
                            @if($meeting->transcript)
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-align-left w-6"></i>
                                    <span>{{ number_format($meeting->transcript->word_count) }} words</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Export Options -->
                    @if($meeting->processing_status === 'completed')
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-file-export text-primary mr-2"></i>Export
                            </h3>
                            <form action="{{ route('meetings.export', $meeting) }}" method="POST" class="space-y-3">
                                @csrf
                                <button type="submit" name="format" value="pdf" 
                                        class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-left flex items-center justify-between">
                                    <span><i class="fas fa-file-pdf mr-2"></i>Export as PDF</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="submit" name="format" value="docx" 
                                        class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-left flex items-center justify-between">
                                    <span><i class="fas fa-file-word mr-2"></i>Export as DOCX</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="submit" name="format" value="txt" 
                                        class="w-full px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition text-left flex items-center justify-between">
                                    <span><i class="fas fa-file-alt mr-2"></i>Export as TXT</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Summary -->
                    @if($meeting->summary)
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
<h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
<i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Summary
</h3>
<p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
{{ $meeting->summary->summary_text }}
</p>
</div>
                    <!-- Action Points -->
                    @if($meeting->summary->action_points && count($meeting->summary->action_points) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-tasks text-green-500 mr-2"></i>Action Points
                            </h3>
                            <ul class="space-y-2">
                                @foreach($meeting->summary->action_points as $point)
                                    <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Key Decisions -->
                    @if($meeting->summary->key_decisions && count($meeting->summary->key_decisions) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>Key Decisions
                            </h3>
                            <ul class="space-y-2">
                                @foreach($meeting->summary->key_decisions as $decision)
                                    <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-arrow-right text-blue-500 mr-2 mt-1"></i>
                                        <span>{{ $decision }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                <!-- Sentiment Analysis -->
                @if($meeting->sentimentAnalysis)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-smile text-primary mr-2"></i>Sentiment
                        </h3>
                        
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall</span>
                                <span class="text-lg font-bold 
                                    @if($meeting->sentimentAnalysis->overall_sentiment === 'positive') text-green-600
                                    @elseif($meeting->sentimentAnalysis->overall_sentiment === 'negative') text-red-600
                                    @else text-gray-600
                                    @endif">
                                    {{ ucfirst($meeting->sentimentAnalysis->overall_sentiment) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-green-600 dark:text-green-400">Positive</span>
                                    <span class="text-xs font-bold text-green-600">{{ $meeting->sentimentAnalysis->positive_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $meeting->sentimentAnalysis->positive_score }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Neutral</span>
                                    <span class="text-xs font-bold text-gray-600">{{ $meeting->sentimentAnalysis->neutral_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gray-500 h-2 rounded-full" style="width: {{ $meeting->sentimentAnalysis->neutral_score }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-red-600 dark:text-red-400">Negative</span>
                                    <span class="text-xs font-bold text-red-600">{{ $meeting->sentimentAnalysis->negative_score }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $meeting->sentimentAnalysis->negative_score }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Delete Meeting -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-2">Danger Zone</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Permanently delete this meeting and all associated data.
                    </p>
                    <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            <i class="fas fa-trash mr-2"></i>Delete Meeting
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/wavesurfer.js@7"></script>
<script>
    // Initialize WaveSurfer
    const wavesurfer = WaveSurfer.create({
        container: '#waveform',
        waveColor: '#3B82F6',
        progressColor: '#1E40AF',
        cursorColor: '#1E40AF',
        barWidth: 2,
        barRadius: 3,
        cursorWidth: 1,
        height: 80,
        barGap: 2,
    });

    // Load audio
    wavesurfer.load('{{ $meeting->audio_url }}');

    const playPauseBtn = document.getElementById('playPauseBtn');
    const currentTimeEl = document.getElementById('currentTime');
    const playbackRateSelect = document.getElementById('playbackRate');
    const volumeControl = document.getElementById('volumeControl');

    // Play/Pause
    playPauseBtn.addEventListener('click', () => {
        wavesurfer.playPause();
    });

    wavesurfer.on('play', () => {
        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
    });

    wavesurfer.on('pause', () => {
        playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
    });

    // Update current time
    wavesurfer.on('timeupdate', (currentTime) => {
        currentTimeEl.textContent = formatTime(currentTime);
        highlightCurrentSegment(currentTime);
    });

    // Playback rate
    playbackRateSelect.addEventListener('change', (e) => {
        wavesurfer.setPlaybackRate(parseFloat(e.target.value));
    });

    // Volume control
    volumeControl.addEventListener('input', (e) => {
        wavesurfer.setVolume(e.target.value / 100);
    });

    // Click on transcript to jump to time
    document.querySelectorAll('.transcript-segment').forEach(segment => {
        segment.addEventListener('click', () => {
            const startTime = parseFloat(segment.dataset.start);
            wavesurfer.seekTo(startTime / wavesurfer.getDuration());
            wavesurfer.play();
        });
    });

    // Highlight current segment
    function highlightCurrentSegment(currentTime) {
        document.querySelectorAll('.transcript-segment').forEach(segment => {
            const start = parseFloat(segment.dataset.start);
            const end = parseFloat(segment.dataset.end);
            
            if (currentTime >= start && currentTime <= end) {
                segment.classList.add('bg-blue-100', 'dark:bg-blue-900/30', 'border-l-4', 'border-blue-500');
                segment.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                segment.classList.remove('bg-blue-100', 'dark:bg-blue-900/30', 'border-l-4', 'border-blue-500');
            }
        });
    }

    // Search transcript
    const searchInput = document.getElementById('searchTranscript');
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        
        document.querySelectorAll('.transcript-segment').forEach(segment => {
            const text = segment.textContent.toLowerCase();
            if (text.includes(query)) {
                segment.style.display = 'block';
                // Highlight search term
                const p = segment.querySelector('p');
                const originalText = p.textContent;
                if (query) {
                    const regex = new RegExp(`(${query})`, 'gi');
                    p.innerHTML = originalText.replace(regex, '<mark class="bg-yellow-300 dark:bg-yellow-600">$1</mark>');
                } else {
                    p.textContent = originalText;
                }
            } else {
                segment.style.display = query ? 'none' : 'block';
            }
        });
    });

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
</script>
@endpush
</x-app-layout>