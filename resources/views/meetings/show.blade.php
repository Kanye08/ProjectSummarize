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
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6 animate-pulse">
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
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column - Audio Player & Transcript (8 cols) -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Audio Player with Sync -->
                    @if($meeting->audio_file_path)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-play-circle text-blue-500 mr-3"></i>Audio Player
                            </h3>
                            
                            <div id="waveform" class="mb-4"></div>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <button id="playPauseBtn" class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition transform hover:scale-105 active:scale-95">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <span id="currentTime" class="text-sm text-gray-600 dark:text-gray-400">0:00</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">/</span>
                                    <span id="duration" class="text-sm text-gray-600 dark:text-gray-400">{{ $meeting->formatted_duration }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <label for="playbackRate" class="text-sm text-gray-600 dark:text-gray-400">Speed:</label>
                                    <select id="playbackRate" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                <input type="range" id="volumeControl" min="0" max="100" value="100" class="flex-1 accent-blue-600">
                            </div>
                        </div>
                    @endif

                    <!-- Transcript / Summary Tabs -->
                    @if($meeting->transcript || $meeting->summary)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                                        <i class="fas fa-file-alt text-blue-500 mr-3"></i>Content
                                    </h3>
                                    <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-900 p-1">
                                        <button
                                            id="transcriptTab"
                                            type="button"
                                            class="px-4 py-2 rounded-lg font-medium transition-all duration-200
                                                   bg-white dark:bg-gray-800 text-blue-600 shadow-sm
                                                   border border-gray-200 dark:border-gray-700">
                                            Transcript
                                        </button>
                                        <button
                                            id="summaryTab"
                                            type="button"
                                            class="px-4 py-2 rounded-lg font-medium transition-all duration-200
                                                   text-gray-600 dark:text-gray-300 ml-1">
                                            Summary
                                        </button>
                                    </div>
                                </div>

                                <div class="flex gap-2 items-center">
                                    <div class="relative hidden" id="searchContainer">
                                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="text" 
                                               id="searchTranscript" 
                                               placeholder="Search transcript..."
                                               class="pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <a href="{{ route('transcripts.download', $meeting) }}" 
                                       id="downloadTranscriptBtn"
                                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition transform hover:scale-105 active:scale-95 hidden">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>

                            <!-- Transcript Panel -->
                            <div id="transcriptPanel" class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                @if($meeting->transcript && $meeting->transcript->segments)
                                    @foreach($meeting->transcript->segments as $segment)
                                        <div class="transcript-segment p-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200 border border-transparent hover:border-blue-200 dark:hover:border-blue-800"
                                             data-start="{{ $segment['start'] }}"
                                             data-end="{{ $segment['end'] }}"
                                             data-segment-id="{{ $segment['id'] }}">
                                            <div class="flex items-start gap-3">
                                                <span class="text-xs text-blue-600 dark:text-blue-400 font-mono flex-shrink-0 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">
                                                    {{ gmdate('i:s', (int)$segment['start']) }}
                                                </span>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 flex-1 leading-relaxed">
                                                    {{ $segment['text'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($meeting->transcript)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                                        {{ $meeting->transcript->full_text }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                        Transcript is not available yet.
                                    </p>
                                @endif
                            </div>

                            <!-- Summary Panel -->
                            <div id="summaryPanel" class="hidden max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                @if($meeting->summary)
                                    @if($meeting->summary->brief_summary)
                                        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-4">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                {{ $meeting->summary->brief_summary }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="prose prose-sm dark:prose-invert max-w-none">
                                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                            {{ $meeting->summary->summary_text }}
                                        </p>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                        Summary will appear here once processing is complete.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Modern Grid Layout (4 cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Quick Stats Cards - Row 1 -->
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

                    <!-- Language & Word Count Cards - Row 2 -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-language text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Language</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ strtoupper($meeting->transcript->language ?? 'N/A') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($meeting->transcript)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-500/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-align-left text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Words</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ number_format($meeting->transcript->word_count) }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Export Options - Modern Card -->
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
                                        class="flex flex-col items-center justify-center p-3 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition group">
                                    <i class="fas fa-file-pdf text-red-600 dark:text-red-400 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium text-red-700 dark:text-red-300">PDF</span>
                                </button>
                                <button type="submit" name="format" value="docx" 
                                        class="flex flex-col items-center justify-center p-3 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg transition group">
                                    <i class="fas fa-file-word text-blue-600 dark:text-blue-400 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">DOCX</span>
                                </button>
                                <button type="submit" name="format" value="txt" 
                                        class="flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition group">
                                    <i class="fas fa-file-alt text-gray-600 dark:text-gray-400 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">TXT</span>
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Summary & Insights - Collapsible Card -->
                    @if($meeting->summary)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all">
                            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSummaryDetails()">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Summary
                                </h3>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="summaryChevron"></i>
                            </div>

                            <div id="summaryContent" class="mt-4">
                                @if($meeting->summary->brief_summary)
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mb-4">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                            {{ $meeting->summary->brief_summary }}
                                        </p>
                                    </div>
                                @endif

                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                        {{ Str::limit($meeting->summary->summary_text, 150) }}
                                    </p>
                                </div>
                                
                                @if(strlen($meeting->summary->summary_text) > 150)
                                    <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2 focus:outline-none" onclick="expandSummary(event)">
                                        Read more
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Action Points & Key Decisions Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            @if($meeting->summary->action_points && count($meeting->summary->action_points) > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border-l-4 border-green-500">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-gray-800 dark:text-white flex items-center">
                                            <i class="fas fa-tasks text-green-500 mr-2"></i>Actions
                                        </h4>
                                        <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-1 rounded-full">
                                            {{ count($meeting->summary->action_points) }}
                                        </span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach(array_slice($meeting->summary->action_points, 0, 2) as $point)
                                            <li class="flex items-start text-xs text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span class="line-clamp-2">{{ $point }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if(count($meeting->summary->action_points) > 2)
                                        <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2" onclick="showAllItems('actions')">
                                            +{{ count($meeting->summary->action_points) - 2 }} more
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if($meeting->summary->key_decisions && count($meeting->summary->key_decisions) > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 hover:shadow-xl transition-all border-l-4 border-blue-500">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-gray-800 dark:text-white flex items-center">
                                            <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>Decisions
                                        </h4>
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full">
                                            {{ count($meeting->summary->key_decisions) }}
                                        </span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach(array_slice($meeting->summary->key_decisions, 0, 2) as $decision)
                                            <li class="flex items-start text-xs text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-arrow-right text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span class="line-clamp-2">{{ $decision }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if(count($meeting->summary->key_decisions) > 2)
                                        <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2" onclick="showAllItems('decisions')">
                                            +{{ count($meeting->summary->key_decisions) - 2 }} more
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Sentiment Analysis - Modern Card -->
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
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-green-600 dark:text-green-400">Positive</span>
                                        <span class="text-xs font-bold text-green-600">{{ $meeting->sentimentAnalysis->positive_score }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $meeting->sentimentAnalysis->positive_score }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">Neutral</span>
                                        <span class="text-xs font-bold text-gray-600">{{ $meeting->sentimentAnalysis->neutral_score }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-gray-500 h-2 rounded-full transition-all duration-500" style="width: {{ $meeting->sentimentAnalysis->neutral_score }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-red-600 dark:text-red-400">Negative</span>
                                        <span class="text-xs font-bold text-red-600">{{ $meeting->sentimentAnalysis->negative_score }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: {{ $meeting->sentimentAnalysis->negative_score }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Delete Meeting - Danger Zone -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all border border-red-200 dark:border-red-800/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-sm"></i>
                            </div>
                            <h3 class="text-lg font-bold text-red-600 dark:text-red-400">Danger Zone</h3>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">
                            Permanently delete this meeting and all associated data.
                        </p>
                        <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>
                                Delete Meeting
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #2d3748;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4a5568;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #718096;
    }
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
                segment.classList.add('bg-blue-100', 'dark:bg-blue-900/30', 'border-blue-500');
                segment.classList.remove('border-transparent');
                
                // Auto-scroll with smooth behavior
                const container = document.getElementById('transcriptPanel');
                const segmentTop = segment.offsetTop;
                const containerScrollTop = container.scrollTop;
                const containerHeight = container.clientHeight;
                
                if (segmentTop < containerScrollTop || segmentTop > containerScrollTop + containerHeight - 100) {
                    segment.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                segment.classList.remove('bg-blue-100', 'dark:bg-blue-900/30', 'border-blue-500');
                segment.classList.add('border-transparent');
            }
        });
    }

    // Tabs: Transcript / Summary
    const transcriptTab = document.getElementById('transcriptTab');
    const summaryTab = document.getElementById('summaryTab');
    const transcriptPanel = document.getElementById('transcriptPanel');
    const summaryPanel = document.getElementById('summaryPanel');
    const searchContainer = document.getElementById('searchContainer');
    const searchInput = document.getElementById('searchTranscript');
    const downloadBtn = document.getElementById('downloadTranscriptBtn');

    function setActiveTab(tab) {
        const isTranscript = tab === 'transcript';

        if (transcriptTab && summaryTab) {
            if (isTranscript) {
                transcriptTab.classList.add('bg-white', 'dark:bg-gray-800', 'text-blue-600', 'shadow-sm', 'border', 'border-gray-200', 'dark:border-gray-700');
                transcriptTab.classList.remove('text-gray-600', 'dark:text-gray-300');
                summaryTab.classList.remove('bg-white', 'dark:bg-gray-800', 'text-blue-600', 'shadow-sm', 'border', 'border-gray-200', 'dark:border-gray-700');
                summaryTab.classList.add('text-gray-600', 'dark:text-gray-300');
            } else {
                summaryTab.classList.add('bg-white', 'dark:bg-gray-800', 'text-blue-600', 'shadow-sm', 'border', 'border-gray-200', 'dark:border-gray-700');
                summaryTab.classList.remove('text-gray-600', 'dark:text-gray-300');
                transcriptTab.classList.remove('bg-white', 'dark:bg-gray-800', 'text-blue-600', 'shadow-sm', 'border', 'border-gray-200', 'dark:border-gray-700');
                transcriptTab.classList.add('text-gray-600', 'dark:text-gray-300');
            }
        }

        if (transcriptPanel && summaryPanel) {
            if (isTranscript) {
                transcriptPanel.classList.remove('hidden');
                summaryPanel.classList.add('hidden');
            } else {
                summaryPanel.classList.remove('hidden');
                transcriptPanel.classList.add('hidden');
            }
        }

        if (searchContainer && downloadBtn) {
            if (isTranscript) {
                searchContainer.classList.remove('hidden');
                downloadBtn.classList.remove('hidden');
            } else {
                searchContainer.classList.add('hidden');
                downloadBtn.classList.add('hidden');
            }
        }
    }

    if (transcriptTab) {
        transcriptTab.addEventListener('click', () => setActiveTab('transcript'));
    }

    if (summaryTab) {
        summaryTab.addEventListener('click', () => setActiveTab('summary'));
    }

    // Initialize default tab
    setActiveTab('transcript');

    // Search transcript with debounce
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = e.target.value.toLowerCase();
                
                document.querySelectorAll('.transcript-segment').forEach(segment => {
                    const text = segment.textContent.toLowerCase();
                    const p = segment.querySelector('p');
                    const originalText = p.textContent;
                    
                    if (text.includes(query)) {
                        segment.style.display = 'block';
                        if (query) {
                            const regex = new RegExp(`(${query})`, 'gi');
                            p.innerHTML = originalText.replace(regex, '<mark class="bg-yellow-300 dark:bg-yellow-600 px-1 rounded">$1</mark>');
                        } else {
                            p.textContent = originalText;
                        }
                    } else {
                        segment.style.display = query ? 'none' : 'block';
                        if (!query) {
                            p.textContent = originalText;
                        }
                    }
                });
            }, 300);
        });
    }

    // Summary toggle functionality
    window.toggleSummaryDetails = function() {
        const summaryContent = document.getElementById('summaryContent');
        const chevron = document.getElementById('summaryChevron');
        const fullSummary = `{{ addslashes($meeting->summary->summary_text ?? '') }}`;
        
        if (summaryContent.dataset.expanded === 'true') {
            summaryContent.innerHTML = `
                @if($meeting->summary->brief_summary)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mb-4">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ addslashes($meeting->summary->brief_summary) }}
                        </p>
                    </div>
                @endif
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        ${fullSummary.substring(0, 150)}...
                    </p>
                </div>
                <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2" onclick="expandSummary(event)">
                    Read more
                </button>
            `;
            summaryContent.dataset.expanded = 'false';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            summaryContent.innerHTML = `
                @if($meeting->summary->brief_summary)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mb-4">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ addslashes($meeting->summary->brief_summary) }}
                        </p>
                    </div>
                @endif
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        ${fullSummary}
                    </p>
                </div>
                <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2" onclick="collapseSummary(event)">
                    Read less
                </button>
            `;
            summaryContent.dataset.expanded = 'true';
            chevron.style.transform = 'rotate(180deg)';
        }
    }

    window.expandSummary = function(event) {
        event.stopPropagation();
        const summaryContent = document.getElementById('summaryContent');
        const chevron = document.getElementById('summaryChevron');
        const fullSummary = `{{ addslashes($meeting->summary->summary_text ?? '') }}`;
        
        summaryContent.innerHTML = `
            @if($meeting->summary->brief_summary)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mb-4">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ addslashes($meeting->summary->brief_summary) }}
                    </p>
                </div>
            @endif
            <div class="prose prose-sm dark:prose-invert max-w-none">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                    ${fullSummary}
                </p>
            </div>
            <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2" onclick="collapseSummary(event)">
                Read less
            </button>
        `;
        summaryContent.dataset.expanded = 'true';
        chevron.style.transform = 'rotate(180deg)';
    }

    window.collapseSummary = function(event) {
        event.stopPropagation();
        const summaryContent = document.getElementById('summaryContent');
        const chevron = document.getElementById('summaryChevron');
        const fullSummary = `{{ addslashes($meeting->summary->summary_text ?? '') }}`;
        
        summaryContent.innerHTML = `
            @if($meeting->summary->brief_summary)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mb-4">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ addslashes($meeting->summary->brief_summary) }}
                    </p>
                </div>
            @endif
            <div class="prose prose-sm dark:prose-invert max-w-none">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                    ${fullSummary.substring(0, 150)}...
                </p>
            </div>
            <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2" onclick="expandSummary(event)">
                Read more
            </button>
        `;
        summaryContent.dataset.expanded = 'false';
        chevron.style.transform = 'rotate(0deg)';
    }

    // Show all items modal (simplified version - you can enhance with a modal)
    window.showAllItems = function(type) {
        @if($meeting->summary)
            const items = type === 'actions' 
                ? @json($meeting->summary->action_points ?? [])
                : @json($meeting->summary->key_decisions ?? []);
            
            const title = type === 'actions' ? 'Action Points' : 'Key Decisions';
            const itemList = items.map(item => `• ${item}`).join('\n');
            
            alert(`${title}:\n\n${itemList}`);
        @endif
    }

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }

    // Initialize duration display
    wavesurfer.on('ready', () => {
        document.getElementById('duration').textContent = formatTime(wavesurfer.getDuration());
    });
</script>
@endpush
</x-app-layout>