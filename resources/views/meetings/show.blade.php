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

    {{-- ── PHP variables passed to JS safely ─────────────────────────────── --}}
    @php
        $summaryText    = $meeting->summary?->summary_text    ?? '';
        $briefSummary   = $meeting->summary?->brief_summary   ?? '';
        $actionPoints   = $meeting->summary?->action_points   ?? [];
        $keyDecisions   = $meeting->summary?->key_decisions   ?? [];
        $audioUrl       = $meeting->audio_url                 ?? '';
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ── Processing banner ─────────────────────────────────────── --}}
            @if(!in_array($meeting->processing_status, ['completed', 'failed']))
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-spinner fa-spin text-yellow-600 dark:text-yellow-400 mr-3"></i>
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-300">Processing in Progress</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                Status: <strong>{{ ucfirst($meeting->processing_status) }}</strong>.
                                @if($meeting->processing_status === 'transcribing')
                                    We're transcribing your audio. This usually takes 2–5 minutes.
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
                            <p class="text-sm text-red-700 dark:text-red-400">{{ $meeting->error_message ?? 'An unknown error occurred.' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Success flash ─────────────────────────────────────────── --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3"></i>
                        <p class="text-green-800 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- ── Main grid ─────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- ── LEFT: Audio player + Transcript/Summary tabs ───────── --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Audio Player --}}
                    @if($meeting->audio_file_path)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-play-circle text-blue-500 mr-3"></i>Audio Player
                            </h3>

                            {{-- Loading state --}}
                            <div id="loadingState" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p class="text-sm">Loading audio…</p>
                            </div>

                            <div id="waveform" class="mb-4 hidden"></div>

                            <div id="basicPlayerWrap" class="mb-4 hidden">
                                <p class="text-xs text-amber-600 dark:text-amber-400 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Waveform unavailable — using basic player.
                                </p>
                                <audio id="basicPlayer" class="w-full" controls preload="metadata" src="{{ $audioUrl }}"></audio>
                            </div>

                            <div id="audioControls" class="hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-4">
                                        <button id="playPauseBtn"
                                                class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition transform hover:scale-105 active:scale-95 flex items-center justify-center">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <span id="currentTime" class="text-sm font-mono text-gray-600 dark:text-gray-400">0:00</span>
                                        <span class="text-sm text-gray-400">/</span>
                                        <span id="duration" class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $meeting->formatted_duration }}</span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <label for="playbackRate" class="text-sm text-gray-600 dark:text-gray-400">Speed:</label>
                                        <select id="playbackRate"
                                                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
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
                                    <i class="fas fa-volume-down text-gray-500 dark:text-gray-400"></i>
                                    <input type="range" id="volumeControl" min="0" max="100" value="100"
                                           class="flex-1 accent-blue-600">
                                    <i class="fas fa-volume-up text-gray-500 dark:text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Transcript / Summary tabs --}}
                    @if($meeting->transcript || $meeting->summary)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">

                            {{-- Tab header --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                                        <i class="fas fa-file-alt text-blue-500 mr-3"></i>Content
                                    </h3>
                                    <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-900 p-1">
                                        <button id="transcriptTab"
                                                class="px-4 py-2 rounded-lg font-medium transition-all duration-200
                                                       bg-white dark:bg-gray-800 text-blue-600 shadow-sm
                                                       border border-gray-200 dark:border-gray-700">
                                            Transcript
                                        </button>
                                        <button id="summaryTab"
                                                class="px-4 py-2 rounded-lg font-medium transition-all duration-200
                                                       text-gray-600 dark:text-gray-300 ml-1">
                                            Summary
                                        </button>
                                    </div>
                                </div>

                                <div class="flex gap-2 items-center">
                                    <div class="relative" id="searchContainer">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="text"
                                               id="searchTranscript"
                                               placeholder="Search transcript…"
                                               class="pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @if($meeting->transcript)
                                        <a href="{{ route('transcripts.download', $meeting) }}"
                                           id="downloadTranscriptBtn"
                                           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                                            <i class="fas fa-download mr-1"></i>Download
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Transcript panel --}}
                            <div id="transcriptPanel" class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                @if($meeting->transcript && !empty($meeting->transcript->segments))
                                    @foreach($meeting->transcript->segments as $segment)
                                        <div class="transcript-segment p-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700
                                                    cursor-pointer transition-all duration-200
                                                    border border-transparent hover:border-blue-200 dark:hover:border-blue-800"
                                             data-start="{{ $segment['start'] ?? 0 }}"
                                             data-end="{{ $segment['end'] ?? 0 }}"
                                             data-segment-id="{{ $segment['id'] ?? 0 }}">
                                            <div class="flex items-start gap-3">
                                                <span class="text-xs text-blue-600 dark:text-blue-400 font-mono flex-shrink-0
                                                             bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">
                                                    {{ gmdate('i:s', (int)($segment['start'] ?? 0)) }}
                                                </span>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 flex-1 leading-relaxed">
                                                    {{ $segment['text'] ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($meeting->transcript && $meeting->transcript->full_text)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                                        {{ $meeting->transcript->full_text }}
                                    </p>
                                @else
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-file-alt text-3xl mb-3 opacity-30"></i>
                                        <p class="text-sm">Transcript is not available yet.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Summary panel --}}
                            <div id="summaryPanel" class="hidden max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                @if($meeting->summary)
                                    @if($meeting->summary->brief_summary)
                                        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-4">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                {{ $meeting->summary->brief_summary }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($meeting->summary->summary_text)
                                        <div class="prose prose-sm dark:prose-invert max-w-none mb-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                                {{ $meeting->summary->summary_text }}
                                            </p>
                                        </div>
                                    @endif

                                    @if(!empty($meeting->summary->action_points))
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-gray-800 dark:text-white mb-2 flex items-center">
                                                <i class="fas fa-tasks text-green-500 mr-2"></i>Action Points
                                            </h4>
                                            <ul class="space-y-2">
                                                @foreach($meeting->summary->action_points as $point)
                                                    <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                                        {{ $point }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(!empty($meeting->summary->key_decisions))
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-gray-800 dark:text-white mb-2 flex items-center">
                                                <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>Key Decisions
                                            </h4>
                                            <ul class="space-y-2">
                                                @foreach($meeting->summary->key_decisions as $decision)
                                                    <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                                        <i class="fas fa-arrow-right text-blue-500 mr-2 mt-1 flex-shrink-0"></i>
                                                        {{ $decision }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-lightbulb text-3xl mb-3 opacity-30"></i>
                                        <p class="text-sm">Summary will appear here once processing is complete.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── RIGHT: Stats + Export + Sentiment + Danger ─────────── --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- Quick stats row 1 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all transform hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-audio text-blue-600 dark:text-blue-400 text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-1 rounded-full">Size</span>
                            </div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $meeting->formatted_size }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Audio file size</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all transform hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 px-2 py-1 rounded-full">Duration</span>
                            </div>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $meeting->formatted_duration }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Meeting length</p>
                        </div>
                    </div>

                    {{-- Quick stats row 2 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-language text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Language</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                        {{ strtoupper($meeting->transcript?->language ?? 'N/A') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-500/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-align-left text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Words</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                        {{ number_format($meeting->transcript?->word_count ?? 0) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Export --}}
                    @if($meeting->processing_status === 'completed')
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all border-t-4 border-blue-500">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                    <i class="fas fa-file-export text-blue-500 mr-2"></i>Export
                                </h3>
                                <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full">Ready</span>
                            </div>
                            <form action="{{ route('meetings.export', $meeting) }}" method="POST" class="grid grid-cols-3 gap-2">
                                @csrf
                                <button type="submit" name="format" value="pdf"
                                        class="flex flex-col items-center justify-center p-3 bg-red-50 dark:bg-red-900/20
                                               hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition group">
                                    <i class="fas fa-file-pdf text-red-600 dark:text-red-400 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium text-red-700 dark:text-red-300">PDF</span>
                                </button>
                                <button type="submit" name="format" value="docx"
                                        class="flex flex-col items-center justify-center p-3 bg-blue-50 dark:bg-blue-900/20
                                               hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg transition group">
                                    <i class="fas fa-file-word text-blue-600 dark:text-blue-400 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">DOCX</span>
                                </button>
                                <button type="submit" name="format" value="txt"
                                        class="flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-gray-700
                                               hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition group">
                                    <i class="fas fa-file-alt text-gray-600 dark:text-gray-400 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">TXT</span>
                                </button>
                            </form>

                            @if($meeting->exports->isNotEmpty())
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
                                    @foreach($meeting->exports->sortByDesc('created_at') as $export)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                <i class="fas
                                                    @if($export->format === 'pdf') fa-file-pdf text-red-500
                                                    @elseif($export->format === 'docx') fa-file-word text-blue-500
                                                    @else fa-file-alt text-gray-500
                                                    @endif mr-2"></i>
                                                {{ strtoupper($export->format) }}
                                                <span class="text-xs text-gray-400 ml-1">{{ $export->created_at->diffForHumans() }}</span>
                                            </span>

                                            @if($export->status === 'completed')
                                                <a href="{{ route('exports.download', $export) }}"
                                                   class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full
                                                    @if($export->status === 'failed') bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
                                                    @else bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400
                                                    @endif">
                                                    {{ ucfirst($export->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Summary sidebar card --}}
                    @if($meeting->summary)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all">
                            <div class="flex items-center justify-between mb-3 cursor-pointer" onclick="toggleSummaryDetails()">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Summary
                                </h3>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="summaryChevron"></i>
                            </div>

                            <div id="summaryContent">
                                @if($meeting->summary->brief_summary)
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg mb-3">
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-100">
                                            {{ $meeting->summary->brief_summary }}
                                        </p>
                                    </div>
                                @endif

                                @if($meeting->summary->summary_text)
                                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed" id="summaryPreview">
                                        {{ Str::limit($meeting->summary->summary_text, 150) }}
                                    </p>
                                    @if(strlen($meeting->summary->summary_text) > 150)
                                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed hidden" id="summaryFull">
                                            {{ $meeting->summary->summary_text }}
                                        </p>
                                        <button onclick="toggleSummaryText(event)"
                                                id="summaryToggleBtn"
                                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2">
                                            Read more
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Action Points & Key Decisions --}}
                        <div class="grid grid-cols-2 gap-4">
                            @if(!empty($actionPoints))
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border-l-4 border-green-500">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-gray-800 dark:text-white flex items-center text-sm">
                                            <i class="fas fa-tasks text-green-500 mr-2"></i>Actions
                                        </h4>
                                        <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-1 rounded-full">
                                            {{ count($actionPoints) }}
                                        </span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach(array_slice($actionPoints, 0, 2) as $point)
                                            <li class="flex items-start text-xs text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span class="line-clamp-2">{{ $point }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if(count($actionPoints) > 2)
                                        <button onclick="showAllItems('actions')"
                                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2">
                                            +{{ count($actionPoints) - 2 }} more
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($keyDecisions))
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border-l-4 border-blue-500">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-gray-800 dark:text-white flex items-center text-sm">
                                            <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>Decisions
                                        </h4>
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full">
                                            {{ count($keyDecisions) }}
                                        </span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach(array_slice($keyDecisions, 0, 2) as $decision)
                                            <li class="flex items-start text-xs text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-arrow-right text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span class="line-clamp-2">{{ $decision }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if(count($keyDecisions) > 2)
                                        <button onclick="showAllItems('decisions')"
                                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2">
                                            +{{ count($keyDecisions) - 2 }} more
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Sentiment --}}
                    @if($meeting->sentimentAnalysis)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                    <i class="fas fa-smile text-blue-500 mr-2"></i>Sentiment
                                </h3>
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($meeting->sentimentAnalysis->overall_sentiment === 'positive') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($meeting->sentimentAnalysis->overall_sentiment === 'negative') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst($meeting->sentimentAnalysis->overall_sentiment) }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                @foreach([
                                    ['label' => 'Positive', 'score' => $meeting->sentimentAnalysis->positive_score, 'color' => 'bg-green-500', 'text' => 'text-green-600 dark:text-green-400'],
                                    ['label' => 'Neutral',  'score' => $meeting->sentimentAnalysis->neutral_score,  'color' => 'bg-gray-500',  'text' => 'text-gray-600 dark:text-gray-400'],
                                    ['label' => 'Negative', 'score' => $meeting->sentimentAnalysis->negative_score, 'color' => 'bg-red-500',   'text' => 'text-red-600 dark:text-red-400'],
                                ] as $item)
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs {{ $item['text'] }}">{{ $item['label'] }}</span>
                                            <span class="text-xs font-bold {{ $item['text'] }}">{{ $item['score'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="{{ $item['color'] }} h-2 rounded-full transition-all duration-500"
                                                 style="width: {{ min(100, max(0, $item['score'])) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Danger Zone --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all border border-red-200 dark:border-red-800/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-sm"></i>
                            </div>
                            <h3 class="text-lg font-bold text-red-600 dark:text-red-400">Danger Zone</h3>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">
                            Permanently delete this meeting and all its data.
                        </p>
                        <form action="{{ route('meetings.destroy', $meeting) }}" method="POST"
                              onsubmit="return confirm('Are you sure? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg
                                           transition transform hover:scale-[1.02] active:scale-[0.98]
                                           flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>Delete Meeting
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark .custom-scrollbar::-webkit-scrollbar-track { background: #2d3748; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #4a5568; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #718096; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/wavesurfer.js@7"></script>
    <script>
    (() => {
        // ── Data from PHP (safe) ────────────────────────────────────────────
        const AUDIO_URL     = @json($audioUrl);
        const ACTION_POINTS = @json($actionPoints);
        const KEY_DECISIONS = @json($keyDecisions);
        const SUMMARY_TEXT  = @json($summaryText);

        // ── WaveSurfer (falls back to a native <audio> player on failure) ──
        @if($meeting->audio_file_path && $audioUrl)
        const loadingState   = document.getElementById('loadingState');
        const waveformEl     = document.getElementById('waveform');
        const audioControls  = document.getElementById('audioControls');
        const basicPlayerWrap= document.getElementById('basicPlayerWrap');
        const basicPlayer    = document.getElementById('basicPlayer');
        const playBtn        = document.getElementById('playPauseBtn');
        const currentTimeEl  = document.getElementById('currentTime');
        const durationEl     = document.getElementById('duration');

        let player = null; // unified interface: { playPause, getDuration, getCurrentTime, seekTo, setTime, setPlaybackRate, setVolume, on }
        let usingFallback = false;

        function useFallbackPlayer() {
            if (usingFallback) return;
            usingFallback = true;
            loadingState?.classList.add('hidden');
            waveformEl?.classList.add('hidden');
            basicPlayerWrap?.classList.remove('hidden');
            audioControls?.classList.remove('hidden');

            basicPlayer.addEventListener('loadedmetadata', () => {
                if (durationEl) durationEl.textContent = fmt(basicPlayer.duration);
            });
            basicPlayer.addEventListener('timeupdate', () => {
                if (currentTimeEl) currentTimeEl.textContent = fmt(basicPlayer.currentTime);
                highlightSegment(basicPlayer.currentTime);
            });
            basicPlayer.addEventListener('play',  () => { if (playBtn) playBtn.innerHTML = '<i class="fas fa-pause"></i>'; });
            basicPlayer.addEventListener('pause', () => { if (playBtn) playBtn.innerHTML = '<i class="fas fa-play"></i>'; });

            player = {
                playPause: () => basicPlayer.paused ? basicPlayer.play() : basicPlayer.pause(),
                play: () => basicPlayer.play(),
                getDuration: () => basicPlayer.duration || 0,
                getCurrentTime: () => basicPlayer.currentTime || 0,
                seekTo: ratio => { basicPlayer.currentTime = ratio * (basicPlayer.duration || 0); },
                setTime: t => { basicPlayer.currentTime = t; },
                setPlaybackRate: r => { basicPlayer.playbackRate = r; },
                setVolume: v => { basicPlayer.volume = v; },
            };
        }

        const wavesurfer = WaveSurfer.create({
            container:     '#waveform',
            waveColor:     '#3B82F6',
            progressColor: '#1E40AF',
            cursorColor:   '#1E40AF',
            barWidth:      2,
            barRadius:     3,
            cursorWidth:   1,
            height:        80,
            barGap:        2,
            responsive:    true,
        });

        wavesurfer.load(AUDIO_URL);

        wavesurfer.on('loading', pct => {
            if (loadingState) loadingState.innerHTML =
                `<i class="fas fa-spinner fa-spin text-2xl mb-2"></i><p class="text-sm">Loading audio… ${pct}%</p>`;
        });

        wavesurfer.on('ready', () => {
            if (usingFallback) return;
            loadingState?.classList.add('hidden');
            waveformEl?.classList.remove('hidden');
            audioControls?.classList.remove('hidden');
            if (durationEl) durationEl.textContent = fmt(wavesurfer.getDuration());

            player = {
                playPause: () => wavesurfer.playPause(),
                play: () => wavesurfer.play(),
                getDuration: () => wavesurfer.getDuration(),
                getCurrentTime: () => wavesurfer.getCurrentTime(),
                seekTo: ratio => wavesurfer.seekTo(ratio),
                setTime: t => wavesurfer.setTime(t),
                setPlaybackRate: r => wavesurfer.setPlaybackRate(r),
                setVolume: v => wavesurfer.setVolume(v),
            };

            wavesurfer.on('play',  () => { if (playBtn) playBtn.innerHTML = '<i class="fas fa-pause"></i>'; });
            wavesurfer.on('pause', () => { if (playBtn) playBtn.innerHTML = '<i class="fas fa-play"></i>'; });
            wavesurfer.on('timeupdate', t => {
                if (currentTimeEl) currentTimeEl.textContent = fmt(t);
                highlightSegment(t);
            });
        });

        wavesurfer.on('error', err => {
            console.error('WaveSurfer error, falling back to basic player:', err);
            useFallbackPlayer();
        });

        playBtn?.addEventListener('click', () => player?.playPause());

        document.getElementById('playbackRate')?.addEventListener('change', e =>
            player?.setPlaybackRate(parseFloat(e.target.value))
        );
        document.getElementById('volumeControl')?.addEventListener('input', e =>
            player?.setVolume(e.target.value / 100)
        );

        // Click transcript → seek
        document.querySelectorAll('.transcript-segment').forEach(seg => {
            seg.addEventListener('click', () => {
                if (!player) return;
                const t = parseFloat(seg.dataset.start);
                const d = player.getDuration();
                if (d > 0) { player.seekTo(t / d); player.play(); }
            });
        });

        function highlightSegment(t) {
            document.querySelectorAll('.transcript-segment').forEach(seg => {
                const s = parseFloat(seg.dataset.start);
                const e = parseFloat(seg.dataset.end);
                const active = t >= s && t <= e;
                seg.classList.toggle('bg-blue-100',   active);
                seg.classList.toggle('border-blue-500', active);
                seg.classList.toggle('border-transparent', !active);
                if (active) seg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', e => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (!player) return;
            if (e.code === 'Space')       { e.preventDefault(); player.playPause(); }
            if (e.code === 'ArrowLeft')   { e.preventDefault(); player.setTime(Math.max(0, player.getCurrentTime() - 5)); }
            if (e.code === 'ArrowRight')  { e.preventDefault(); player.setTime(Math.min(player.getDuration(), player.getCurrentTime() + 5)); }
        });
        @endif

        // ── Tabs ────────────────────────────────────────────────────────────
        const tTab = document.getElementById('transcriptTab');
        const sTab = document.getElementById('summaryTab');
        const tPanel = document.getElementById('transcriptPanel');
        const sPanel = document.getElementById('summaryPanel');
        const searchBox = document.getElementById('searchContainer');

        function setTab(tab) {
            const isTrans = tab === 'transcript';
            tPanel?.classList.toggle('hidden', !isTrans);
            sPanel?.classList.toggle('hidden',  isTrans);
            searchBox?.classList.toggle('hidden', !isTrans);

            [tTab, sTab].forEach((btn, i) => {
                const active = (i === 0) === isTrans;
                btn?.classList.toggle('bg-white',          active);
                btn?.classList.toggle('dark:bg-gray-800',  active);
                btn?.classList.toggle('text-blue-600',     active);
                btn?.classList.toggle('shadow-sm',         active);
                btn?.classList.toggle('border',            active);
                btn?.classList.toggle('border-gray-200',   active);
                btn?.classList.toggle('dark:border-gray-700', active);
                btn?.classList.toggle('text-gray-600',    !active);
                btn?.classList.toggle('dark:text-gray-300',!active);
            });
        }

        tTab?.addEventListener('click', () => setTab('transcript'));
        sTab?.addEventListener('click', () => setTab('summary'));
        setTab('transcript');

        // ── Transcript search ───────────────────────────────────────────────
        let searchTO;
        document.getElementById('searchTranscript')?.addEventListener('input', e => {
            clearTimeout(searchTO);
            searchTO = setTimeout(() => {
                const q = e.target.value.toLowerCase().trim();
                document.querySelectorAll('.transcript-segment').forEach(seg => {
                    const p = seg.querySelector('p');
                    const orig = p.getAttribute('data-orig') || p.textContent;
                    if (!p.getAttribute('data-orig')) p.setAttribute('data-orig', p.textContent);

                    if (!q) {
                        seg.style.display = '';
                        p.textContent = orig;
                        return;
                    }
                    if (orig.toLowerCase().includes(q)) {
                        seg.style.display = '';
                        p.innerHTML = orig.replace(new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi'),
                            '<mark class="bg-yellow-300 dark:bg-yellow-600 px-0.5 rounded">$1</mark>');
                    } else {
                        seg.style.display = 'none';
                    }
                });
            }, 250);
        });

        // ── Summary toggle (read more / less) ──────────────────────────────
        window.toggleSummaryText = function(e) {
            e.stopPropagation();
            const preview = document.getElementById('summaryPreview');
            const full    = document.getElementById('summaryFull');
            const btn     = document.getElementById('summaryToggleBtn');
            if (!preview || !full || !btn) return;

            const showing = !full.classList.contains('hidden');
            full.classList.toggle('hidden', showing);
            preview.classList.toggle('hidden', !showing);
            btn.textContent = showing ? 'Read more' : 'Read less';
        };

        window.toggleSummaryDetails = function() {
            // no-op – kept for backward compat if called elsewhere
        };

        // ── Show all items ──────────────────────────────────────────────────
        window.showAllItems = function(type) {
            const items = type === 'actions' ? ACTION_POINTS : KEY_DECISIONS;
            const title = type === 'actions' ? 'Action Points' : 'Key Decisions';
            alert(title + ':\n\n' + items.map(i => '• ' + i).join('\n'));
        };

        // ── Helpers ─────────────────────────────────────────────────────────
        function fmt(s) {
            if (!s || isNaN(s)) return '0:00';
            const m = Math.floor(s / 60), sec = Math.floor(s % 60);
            return m + ':' + String(sec).padStart(2, '0');
        }
    })();
    </script>
    @endpush
</x-app-layout>