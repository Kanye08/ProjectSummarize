<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    {{ __('My Dashboard') }}
                </h2>
                <p class="mt-1 flex items-center">
                    <i class="fas fa-user-shield text-primary mr-2"></i>
                    <span class="text-gray-600 dark:text-gray-300">
                        Welcome back,
                    </span>
                    <span class="font-semibold ml-1 text-gray-600 dark:text-white">
                        {{ Auth::user()->name }}
                    </span>
                </p>

            </div>
            <div class="flex items-center space-x-4">
                <div class="px-4 py-2 bg-blue-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-500 dark:text-gray-300 flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>{{ now()->format('F j, Y') }}
                    </span>
                </div>
                <a href="{{ route('meetings.create') }}" 
                   class="px-4 py-2 bg-primary hover:bg-blue-600 text-white font-medium rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i> New Meeting
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg p-6 border border-blue-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wider">Meetings</p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['total_meetings'] ?? 0 }}</p>
                            <div class="flex items-center mt-3">
                                <i class="fas fa-video text-blue-500 dark:text-blue-400 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Total recorded</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300">
                            <i class="fas fa-video text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg p-6 border border-green-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 dark:text-green-300 uppercase tracking-wider">Transcriptions</p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['total_transcriptions'] ?? 0 }}</p>
                            <div class="flex items-center mt-3">
                                <i class="fas fa-file-alt text-green-500 dark:text-green-400 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Pages processed</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-300">
                            <i class="fas fa-file-alt text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg p-6 border border-purple-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 dark:text-purple-300 uppercase tracking-wider">Storage</p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['storage_used'] ?? '0' }}GB</p>
                            <div class="flex items-center mt-3">
                                <i class="fas fa-database text-purple-500 dark:text-purple-400 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Of 50GB used</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300">
                            <i class="fas fa-database text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-amber-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg p-6 border border-amber-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-600 dark:text-amber-300 uppercase tracking-wider">Export Credits</p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['export_credits'] ?? 10 }}</p>
                            <div class="flex items-center mt-3">
                                <i class="fas fa-file-export text-amber-500 dark:text-amber-400 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Remaining</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300">
                            <i class="fas fa-file-export text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-10">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center mb-6">
                    <i class="fas fa-bolt text-primary mr-3"></i>Quick Actions
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <a href="{{ route('meetings.create') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="text-center">
                            <div class="p-4 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white inline-block mb-4 group-hover:shadow-lg">
                                <i class="fas fa-video text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">Start Meeting</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Begin a new meeting session</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('meetings.create') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="text-center">
                            <div class="p-4 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white inline-block mb-4 group-hover:shadow-lg">
                                <i class="fas fa-cloud-upload-alt text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">Upload Recording</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Upload existing meeting files</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('meetings.index') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="text-center">
                            <div class="p-4 rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 text-white inline-block mb-4 group-hover:shadow-lg">
                                <i class="fas fa-file-alt text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">My Transcriptions</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">View & manage transcripts</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('exports.index') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="text-center">
                            <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-white inline-block mb-4 group-hover:shadow-lg">
                                <i class="fas fa-file-export text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">Export Files</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Download in PDF, Excel, etc.</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity & Upcoming Meetings -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Meetings -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                                <i class="fas fa-history text-primary mr-3"></i>Recent Meetings
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Your latest meeting sessions</p>
                        </div>
                        <a href="#" class="text-sm text-primary hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                            View All
                        </a>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($recent_meetings ?? [] as $meeting)
                            <div class="flex items-center p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white mr-4">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $meeting->title }}
                                        </p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $meeting->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-xs px-2 py-1 rounded-full 
                                            @if($meeting->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                            @elseif($meeting->status === 'processing') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                            @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                            @endif">
                                            {{ ucfirst($meeting->status) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-3">
                                            <i class="fas fa-clock mr-1"></i>{{ $meeting->duration ?? '0:00' }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('meetings.show', $meeting) }}" 
                                   class="ml-4 text-primary hover:text-blue-700 dark:hover:text-blue-300">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="p-4 rounded-full bg-blue-50 dark:bg-blue-900/30 inline-block mb-4">
                                    <i class="fas fa-video-slash text-3xl text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No meetings yet</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Start your first meeting to see it here</p>
                                <a href="3" 
                                   class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                                    Start Meeting
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Storage & Usage -->
                <div class="space-y-6">
                    <!-- Storage Progress -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center mb-6">
                            <i class="fas fa-hdd text-primary mr-3"></i>Storage Usage
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Used Storage</span>
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $stats['storage_used'] ?? 0 }}GB / 50GB</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full" 
                                         style="width: {{ min(($stats['storage_used'] ?? 0) / 50 * 100, 100) }}%"></div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['audio_files'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Audio Files</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['video_files'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Video Files</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['documents'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Documents</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-2xl shadow-xl p-6 text-white">
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-lightbulb mr-3"></i>Quick Tips
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle mt-1 mr-3 opacity-90"></i>
                                <p class="text-sm">Use AI summaries to get key points from long meetings</p>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle mt-1 mr-3 opacity-90"></i>
                                <p class="text-sm">Export transcripts in multiple formats for different needs</p>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle mt-1 mr-3 opacity-90"></i>
                                <p class="text-sm">Sync text with audio playback for easy review</p>
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-white/20">
                            <p class="text-sm opacity-90">Need help? <a href="#" class="font-semibold underline">View documentation</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>