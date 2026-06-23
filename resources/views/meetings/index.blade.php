<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-video text-primary mr-3"></i>My Meetings
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                    Manage and review your meeting recordings
                </p>
            </div>
            <a href="{{ route('meetings.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-lg transition shadow-md">
                <i class="fas fa-plus mr-2"></i>Upload New Meeting
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3"></i>
                        <p class="text-green-800 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Meetings Grid -->
            @if($meetings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($meetings as $meeting)
                        <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-lg hover:shadow-xl transition-all border border-white/30 dark:border-gray-700/30 overflow-hidden">
                            <!-- Status Badge -->
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-4">
                                <div class="flex items-center justify-between text-white">
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full 
                                        @if($meeting->processing_status === 'completed') bg-green-500
                                        @elseif($meeting->processing_status === 'failed') bg-red-500
                                        @elseif(in_array($meeting->processing_status, ['processing', 'transcribing', 'summarizing'])) bg-yellow-500
                                        @else bg-gray-500
                                        @endif">
                                        {{ ucfirst($meeting->processing_status) }}
                                    </span>
                                    <span class="text-xs">
                                        <i class="fas fa-clock mr-1"></i>{{ $meeting->formatted_duration }}
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 truncate">
                                    {{ $meeting->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    <i class="fas fa-calendar mr-2"></i>{{ $meeting->start_time->format('M d, Y') }}
                                </p>

                                @if($meeting->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 line-clamp-2">
                                        {{ $meeting->description }}
                                    </p>
                                @endif

                                @if($meeting->processing_status === 'completed' && $meeting->summary)
                                    <p class="text-xs text-gray-700 dark:text-gray-200 italic mb-4 line-clamp-3">
                                        {{ $meeting->summary->brief_summary ?: Str::limit($meeting->summary->summary_text, 160) }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                        {{ ucfirst($meeting->processing_status) }}&nbsp;– transcription and summary will appear once processing is complete.
                                    </p>
                                @endif

                                <!-- Metadata -->
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    <span><i class="fas fa-file-audio mr-1"></i>{{ $meeting->formatted_size }}</span>
                                    <span><i class="fas fa-language mr-1"></i>{{ strtoupper($meeting->audio_format ?? 'N/A') }}</span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ route('meetings.show', $meeting) }}"
                                       class="flex-1 px-4 py-2 bg-primary hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition text-center">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                    @if($meeting->processing_status === 'completed')
                                        <a href="{{ route('transcripts.show', $meeting) }}" 
                                           class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition text-center">
                                            <i class="fas fa-file-alt mr-1"></i>Transcript
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $meetings->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="inline-block p-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 mb-6">
                        <i class="fas fa-video-slash text-6xl text-emerald-500 dark:text-emerald-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">No Meetings Yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Upload your first meeting recording to get started</p>
                    <a href="{{ route('meetings.create') }}"
                       class="inline-block px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-lg transition shadow-md">
                        <i class="fas fa-plus mr-2"></i>Upload Meeting
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>