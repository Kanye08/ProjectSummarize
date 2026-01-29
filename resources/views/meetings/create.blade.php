<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-plus-circle text-primary mr-3"></i>Upload Meeting Recording
            </h2>
            <a href="{{ route('meetings.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Meetings
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Upload Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <form action="{{ route('meetings.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-heading mr-2"></i>Meeting Title *
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               required
                               value="{{ old('title') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., Weekly Team Standup - Jan 24, 2026">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-align-left mr-2"></i>Description (Optional)
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white"
                                  placeholder="Add any notes about this meeting...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meeting Date/Time -->
                    <div class="mb-6">
                        <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Meeting Date & Time (Optional)
                        </label>
                        <input type="datetime-local" 
                               name="start_time" 
                               id="start_time"
                               value="{{ old('start_time') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:text-white">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Audio Upload -->
                    <div class="mb-6">
                        <label for="audio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-microphone mr-2"></i>Audio Recording *
                        </label>
                        
                        <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-primary transition"
                             id="dropZone">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="audio" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-primary hover:text-blue-700 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="audio" 
                                               name="audio" 
                                               type="file" 
                                               class="sr-only"
                                               accept=".mp3,.wav,.m4a,.ogg,.webm,.mpeg"
                                               required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    MP3, WAV, M4A, OGG, WEBM up to 512MB
                                </p>
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div id="filePreview" class="mt-4 hidden">
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file-audio text-3xl text-blue-600 dark:text-blue-400 mr-4"></i>
                                    <div>
                                        <p id="fileName" class="text-sm font-medium text-gray-900 dark:text-white"></p>
                                        <p id="fileSize" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                    </div>
                                </div>
                                <button type="button" id="removeFile" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times-circle text-2xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div id="uploadProgress" class="mt-4 hidden">
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div id="progressBar" class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all" style="width: 0%"></div>
                            </div>
                            <p id="progressText" class="text-sm text-gray-600 dark:text-gray-400 mt-2 text-center">Uploading: 0%</p>
                        </div>

                        @error('audio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3 mt-1"></i>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <p class="font-semibold mb-2">What happens next?</p>
                                <ul class="space-y-1 ml-4 list-disc">
                                    <li>Your audio will be uploaded securely</li>
                                    <li>AI will transcribe the audio (usually takes 2-5 minutes)</li>
                                    <li>A summary and action items will be generated automatically</li>
                                    <li>Sentiment analysis will be performed</li>
                                    <li>You can export the results in PDF, DOCX, or TXT format</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg transition shadow-lg">
                            <i class="fas fa-upload mr-2"></i>Upload & Process
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const audioInput = document.getElementById('audio');
        const dropZone = document.getElementById('dropZone');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFile = document.getElementById('removeFile');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const submitBtn = document.getElementById('submitBtn');

        // File input change
        audioInput.addEventListener('change', handleFileSelect);

        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary', 'bg-blue-50', 'dark:bg-blue-900/20');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-primary', 'bg-blue-50', 'dark:bg-blue-900/20');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary', 'bg-blue-50', 'dark:bg-blue-900/20');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                audioInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            const file = audioInput.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.classList.remove('hidden');
                dropZone.classList.add('hidden');
            }
        }

        removeFile.addEventListener('click', () => {
            audioInput.value = '';
            filePreview.classList.add('hidden');
            dropZone.classList.remove('hidden');
        });

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Form submission with progress
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            if (!audioInput.files[0]) {
                e.preventDefault();
                alert('Please select an audio file');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        });
    </script>
    @endpush
</x-app-layout>