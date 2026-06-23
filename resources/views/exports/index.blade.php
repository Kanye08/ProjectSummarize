<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-file-export text-primary mr-3"></i>Exports
                </h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">All your generated meeting exports</p>
            </div>
            <a href="{{ route('meetings.index') }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Meetings
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                @forelse($exports as $export)
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center
                                @if($export->format === 'pdf') bg-red-50 dark:bg-red-900/20
                                @elseif($export->format === 'docx') bg-blue-50 dark:bg-blue-900/20
                                @else bg-gray-50 dark:bg-gray-700
                                @endif">
                                <i class="text-lg
                                    @if($export->format === 'pdf') fas fa-file-pdf text-red-600 dark:text-red-400
                                    @elseif($export->format === 'docx') fas fa-file-word text-blue-600 dark:text-blue-400
                                    @else fas fa-file-alt text-gray-600 dark:text-gray-400
                                    @endif"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ $export->meeting->title ?? 'Deleted meeting' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ strtoupper($export->format) }}
                                    @if($export->file_size)
                                        · {{ round($export->file_size / 1024, 1) }} KB
                                    @endif
                                    · {{ $export->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium px-3 py-1 rounded-full
                                @if($export->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($export->status === 'failed') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 animate-pulse
                                @endif">
                                {{ ucfirst($export->status) }}
                            </span>

                            @if($export->status === 'completed')
                                <a href="{{ route('exports.download', $export) }}"
                                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-file-export text-4xl mb-4 opacity-30"></i>
                        <p>No exports yet.</p>
                        <p class="text-sm mt-1">Generate one from a meeting page.</p>
                    </div>
                @endforelse
            </div>

            @if($exports->hasPages())
                <div class="mt-6">
                    {{ $exports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
