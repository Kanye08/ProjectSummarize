<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-file-alt text-primary mr-3"></i>Transcript
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                    {{ $meeting->title }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('transcripts.download', $meeting) }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>Download
                </a>
                <a href="{{ route('meetings.show', $meeting) }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Meeting
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Transcript Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $meeting->title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            <i class="fas fa-calendar mr-2"></i>{{ $meeting->start_time->format('F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>{{ number_format($meeting->transcript->word_count) }}</strong> words
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>{{ $meeting->formatted_duration }}</strong> duration
                        </p>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" 
                           id="searchTranscript" 
                           placeholder="Search in transcript..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                </div>
            </div>

            <!-- Full Transcript -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <div id="transcriptContent" class="prose dark:prose-invert max-w-none">
                    @if($meeting->transcript->segments && count($meeting->transcript->segments) > 0)
                        @foreach($meeting->transcript->segments as $segment)
                            <div class="transcript-segment mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0"
                                 data-segment-id="{{ $segment['id'] }}">
                                <div class="flex items-start gap-4">
                                    <span class="text-xs font-mono text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded flex-shrink-0">
                                        {{ gmdate('i:s', (int)$segment['start']) }}
                                    </span>
                                    <p class="text-gray-800 dark:text-gray-200 leading-relaxed flex-1">
                                        {{ $segment['text'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap">
                            {{ $meeting->transcript->full_text }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Export Options -->
            <div class="mt-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
                <h3 class="text-xl font-bold mb-4">Export This Transcript</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <form action="{{ route('meetings.export', $meeting) }}" method="POST">
                        @csrf
                        <button type="submit" name="format" value="pdf" 
                                class="w-full px-4 py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg transition text-left">
                            <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                        </button>
                    </form>
                    <form action="{{ route('meetings.export', $meeting) }}" method="POST">
                        @csrf
                        <button type="submit" name="format" value="docx" 
                                class="w-full px-4 py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg transition text-left">
                            <i class="fas fa-file-word mr-2"></i>Export as DOCX
                        </button>
                    </form>
                    <form action="{{ route('meetings.export', $meeting) }}" method="POST">
                        @csrf
                        <button type="submit" name="format" value="txt" 
                                class="w-full px-4 py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg transition text-left">
                            <i class="fas fa-file-alt mr-2"></i>Export as TXT
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Search functionality
        const searchInput = document.getElementById('searchTranscript');
        const segments = document.querySelectorAll('.transcript-segment');

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            
            segments.forEach(segment => {
                const text = segment.textContent.toLowerCase();
                const paragraph = segment.querySelector('p');
                const originalText = paragraph.getAttribute('data-original') || paragraph.textContent;
                
                if (!paragraph.getAttribute('data-original')) {
                    paragraph.setAttribute('data-original', paragraph.textContent);
                }
                
                if (text.includes(query)) {
                    segment.style.display = 'block';
                    
                    if (query) {
                        const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
                        paragraph.innerHTML = originalText.replace(regex, '<mark class="bg-yellow-300 dark:bg-yellow-600 px-1 rounded">$1</mark>');
                    } else {
                        paragraph.textContent = originalText;
                    }
                } else {
                    segment.style.display = query ? 'none' : 'block';
                }
            });
        });

        function escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    </script>
    @endpush
</x-app-layout>