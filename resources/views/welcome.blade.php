<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SummarAIze - Smart Meeting Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
        }
        
        .gradient-bg-dark {
            background: linear-gradient(135deg, #0369a1 0%, #1d4ed8 100%);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);
        }
        
        .upload-progress-bar {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .upload-progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .playback-highlight {
            background-color: rgba(14, 165, 233, 0.15);
            border-left: 3px solid #0ea5e9;
            padding-left: 8px;
            transition: background-color 0.3s;
        }
        
        .auth-link {
            position: relative;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.3s;
        }
        
        .auth-link:hover {
            background-color: rgba(14, 165, 233, 0.1);
        }
        
        .auth-link.active {
            background-color: rgba(14, 165, 233, 0.15);
            font-weight: 500;
        }
        
        .text-primary {
            color: #0ea5e9;
        }
        
        .bg-primary {
            background-color: #0ea5e9;
        }
        
        .hover\:bg-primary:hover {
            background-color: #0284c7;
        }
        
        .border-primary {
            border-color: #0ea5e9;
        }
        
        .text-secondary {
            color: #3b82f6;
        }
        
        .bg-secondary {
            background-color: #3b82f6;
        }
        
        .text-accent {
            color: #06b6d4;
        }
        
        .bg-accent {
            background-color: #06b6d4;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-10 py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="flex items-center">
                    <i class="fas fa-video text-primary text-2xl mr-2"></i>
                    <span class="text-xl font-bold text-gray-800">SummarAIze</span>
                </div>
                <div class="hidden md:flex ml-12 space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-primary font-medium">Features</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-primary font-medium">How It Works</a>
                    <a href="#testimonials" class="text-gray-600 hover:text-primary font-medium">Testimonials</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <!-- Dashboard link for logged-in users -->
                    <a href="{{ route('dashboard') }}" class="auth-link text-gray-600 hover:text-primary font-medium hidden md:block">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <!-- Logout form -->
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="auth-link text-gray-600 hover:text-primary font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Log Out
                        </button>
                    </form>
                    <!-- Mobile menu for logged-in users -->
                    <div class="md:hidden relative">
                        <button id="mobile-menu-button" class="text-gray-600 hover:text-primary">
                            <i class="fas fa-user-circle text-2xl"></i>
                        </button>
                        <div id="mobile-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Authentication links for guests -->
                    <a href="{{ route('login') }}" class="auth-link text-gray-600 hover:text-primary font-medium hidden md:block {{ request()->routeIs('login') ? 'active' : '' }}">
                        <i class="fas fa-sign-in-alt mr-2"></i>Log In
                    </a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-primary hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-medium transition duration-300 hidden md:block">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    @endif
                    
                    <!-- Mobile menu for guests -->
                    <div class="md:hidden relative">
                        <button id="mobile-menu-button" class="text-gray-600 hover:text-primary">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        <div id="mobile-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('login') ? 'bg-blue-50 text-primary' : '' }}">
                                <i class="fas fa-sign-in-alt mr-2"></i>Log In
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('register') ? 'bg-blue-50 text-primary' : '' }}">
                                    <i class="fas fa-user-plus mr-2"></i>Register
                                </a>
                            @endif
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-28 pb-16 gradient-bg text-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Transform Meetings into Actionable Insights</h1>
                    <p class="text-xl mb-8 text-blue-100">Record, transcribe, and analyze conversations with AI-powered precision. All in one intelligent platform.</p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-white text-primary hover:bg-gray-100 font-bold py-3 px-8 rounded-lg text-center transition duration-300">
                                <i class="fas fa-rocket mr-2"></i>Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="bg-white text-primary hover:bg-gray-100 font-bold py-3 px-8 rounded-lg text-center transition duration-300">Start Free Trial</a>
                            <a href="#features" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-bold py-3 px-8 rounded-lg text-center transition duration-300">Explore Features</a>
                        @endauth
                    </div>
                    <p class="mt-6 text-blue-200">No credit card required • 14-day free trial</p>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-lg w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Upload & Transcribe</h3>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">Live Demo</span>
                        </div>
                        
                        <!-- Upload Demo -->
                        <div class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition duration-300">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 font-medium">Drag & drop your meeting recording here</p>
                            <p class="text-gray-500 text-sm mt-2">Supports MP3, MP4, WAV, and more</p>
                            
                            @auth
                                <button class="mt-4 bg-primary hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-medium">
                                    <i class="fas fa-upload mr-2"></i>Upload File
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="mt-4 inline-block bg-primary hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-medium">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Log In to Upload
                                </a>
                            @endauth
                        </div>
                        
                        <!-- Upload Progress Demo -->
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700 font-medium">sales-meeting.mp4</span>
                                <span class="text-gray-500 text-sm">65%</span>
                            </div>
                            <div class="upload-progress-bar bg-gray-200">
                                <div class="upload-progress-fill bg-green-500" style="width: 65%"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-gray-500 text-sm">Uploading to your dashboard...</span>
                                <button class="text-red-500 hover:text-red-700 text-sm font-medium">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                        
                        <!-- Transcription Preview -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold text-gray-800 mb-3">Transcription Preview</h4>
                            <div class="h-40 overflow-y-auto text-sm">
                                <p class="mb-2"><span class="font-medium text-primary">[00:01:23] John:</span> So about the Q3 projections...</p>
                                <p class="mb-2 play-back-highlight"><span class="font-medium text-primary">[00:01:45] Sarah:</span> I think we can aim for 15% growth.</p>
                                <p class="mb-2"><span class="font-medium text-primary">[00:02:10] Mike:</span> That's ambitious but achievable with the new campaign.</p>
                                <p class="mb-2"><span class="font-medium text-primary">[00:02:45] John:</span> Let's sync text with audio playback to review...</p>
                                <p class="mb-2"><span class="font-medium text-primary">[00:03:15] Sarah:</span> We can export the transcript as PDF or Excel.</p>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <button class="text-primary mr-4">
                                        <i class="fas fa-play-circle text-xl"></i>
                                    </button>
                                    <span class="text-gray-600 text-sm">00:02:15 / 00:12:45</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-gray-600 hover:text-primary text-sm">
                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                    </button>
                                    <button class="text-gray-600 hover:text-primary text-sm">
                                        <i class="fas fa-file-excel mr-1"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">AI-Powered Meeting Intelligence</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">Everything you need to capture, analyze, and act on your meeting discussions</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="text-primary mb-4">
                        <i class="fas fa-video text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Start & Record Meetings</h3>
                    <p class="text-gray-600 mb-4">Start meetings directly from the platform or upload existing recordings. Supports all major formats.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>One-click meeting start</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Cloud recording with auto-save</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Upload existing recordings</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="text-primary mb-4">
                        <i class="fas fa-file-audio text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">AI Audio Transcription</h3>
                    <p class="text-gray-600 mb-4">Automatic, accurate transcription with speaker identification. Edit and correct transcripts easily.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Real-time transcription</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Speaker identification</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Edit transcripts manually</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="text-primary mb-4">
                        <i class="fas fa-sync-alt text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Audio-Text Sync Playback</h3>
                    <p class="text-gray-600 mb-4">Play audio with synchronized text highlighting, just like Otter.ai. Click text to jump to that moment.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Text highlights as audio plays</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Click text to jump to audio</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Adjustable playback speed</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="text-primary mb-4">
                        <i class="fas fa-tasks text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Upload Progress & Control</h3>
                    <p class="text-gray-600 mb-4">Track upload progress in real-time. Pause, resume, or cancel uploads as needed.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Real-time progress tracking</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Pause & resume uploads</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Cancel uploads anytime</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="text-primary mb-4">
                        <i class="fas fa-file-contract text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">AI Summary Generation</h3>
                    <p class="text-gray-600 mb-4">Automatically generate concise meeting summaries, action items, and key decisions.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Key points extraction</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Action item identification</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Decision tracking</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="text-primary mb-4">
                        <i class="fas fa-download text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Export & Share</h3>
                    <p class="text-gray-600 mb-4">Export transcripts, summaries, and analytics in multiple formats for sharing and archiving.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>PDF, Excel, Word exports</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Shareable links</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Integration with Slack, Teams</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Call to Action in Features Section -->
            <div class="mt-16 text-center">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Ready to experience these features?</h3>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-primary hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                            <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="bg-primary hover:bg-blue-500 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="bg-white border-2 border-primary text-primary hover:bg-blue-50 font-bold py-3 px-8 rounded-lg transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Log In to Existing Account
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">How SummarAIze Works</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">Transform your meetings in just four simple steps</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-md">
                        <span class="gradient-bg text-white font-bold text-2xl rounded-full w-12 h-12 flex items-center justify-center">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Start or Upload</h3>
                    <p class="text-gray-600">Start a new meeting or upload existing recordings from any device.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-md">
                        <span class="gradient-bg text-white font-bold text-2xl rounded-full w-12 h-12 flex items-center justify-center">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">AI Processing</h3>
                    <p class="text-gray-600">Our AI transcribes audio, identifies speakers, and syncs text with audio.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-md">
                        <span class="gradient-bg text-white font-bold text-2xl rounded-full w-12 h-12 flex items-center justify-center">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Review & Edit</h3>
                    <p class="text-gray-600">Playback with synchronized text, edit transcripts, and generate summaries.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-md">
                        <span class="gradient-bg text-white font-bold text-2xl rounded-full w-12 h-12 flex items-center justify-center">4</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Export & Share</h3>
                    <p class="text-gray-600">Export in multiple formats, share with your team, and track action items.</p>
                </div>
            </div>
            
            <div class="mt-12 bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Export Options</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-primary transition duration-300">
                        <i class="fas fa-file-pdf text-3xl text-red-500 mb-3"></i>
                        <h4 class="font-bold text-gray-800">PDF</h4>
                        <p class="text-gray-600 text-sm">Formatted transcript with timestamps</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-primary transition duration-300">
                        <i class="fas fa-file-excel text-3xl text-green-500 mb-3"></i>
                        <h4 class="font-bold text-gray-800">Excel</h4>
                        <p class="text-gray-600 text-sm">Structured data for analysis</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-primary transition duration-300">
                        <i class="fas fa-file-word text-3xl text-blue-500 mb-3"></i>
                        <h4 class="font-bold text-gray-800">Word</h4>
                        <p class="text-gray-600 text-sm">Editable document format</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-primary transition duration-300">
                        <i class="fas fa-file-csv text-3xl text-yellow-500 mb-3"></i>
                        <h4 class="font-bold text-gray-800">CSV</h4>
                        <p class="text-gray-600 text-sm">Raw data for custom processing</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Trusted by Teams Worldwide</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">See what professionals are saying about SummarAIze</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-bold text-lg mr-4">
                            SJ
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Sarah Johnson</h4>
                            <p class="text-gray-600 text-sm">Product Manager, TechCorp</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"SummarAIze has transformed our team meetings. The AI summaries save us hours every week and the transcription accuracy is incredible."</p>
                    <div class="flex text-yellow-400 mt-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-accent flex items-center justify-center text-white font-bold text-lg mr-4">
                            MR
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Michael Rodriguez</h4>
                            <p class="text-gray-600 text-sm">Consultant, BusinessWorks</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"The audio-text sync feature is exactly what I needed. Being able to click on text and jump to that moment in the audio is a game-changer."</p>
                    <div class="flex text-yellow-400 mt-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-white font-bold text-lg mr-4">
                            EC
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Elena Chen</h4>
                            <p class="text-gray-600 text-sm">Research Director, Global Insights</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"Exporting transcripts to multiple formats has streamlined our reporting process. The Excel exports are perfect for our data analysis."</p>
                    <div class="flex text-yellow-400 mt-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 gradient-bg-dark">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Transform Your Meetings?</h2>
            <p class="text-xl text-blue-100 mb-10 max-w-3xl mx-auto">Join thousands of teams who use SummarAIze to capture, understand, and act on their meeting discussions.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-primary hover:bg-gray-100 font-bold py-4 px-10 rounded-lg text-lg transition duration-300">
                        <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                    </a>
                    <a href="#" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-bold py-4 px-10 rounded-lg text-lg transition duration-300">
                        <i class="fas fa-video mr-2"></i>Start a Meeting
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-white text-primary hover:bg-gray-100 font-bold py-4 px-10 rounded-lg text-lg transition duration-300">Start Your Free 14-Day Trial</a>
                    <a href="{{ route('login') }}" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-bold py-4 px-10 rounded-lg text-lg transition duration-300">Log In to Your Account</a>
                @endauth
            </div>
            <p class="text-blue-200 mt-8">No credit card required • Cancel anytime</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-8 md:mb-0">
                    <div class="flex items-center">
                        <i class="fas fa-video text-primary text-2xl mr-2"></i>
                        <span class="text-xl font-bold">SummarAIze</span>
                    </div>
                    <p class="text-gray-400 mt-4 max-w-md">The intelligent meeting platform that records, transcribes, and analyzes your conversations so you can focus on what matters.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="font-bold text-lg mb-4">Product</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#features" class="hover:text-white">Features</a></li>
                            <li><a href="#how-it-works" class="hover:text-white">How It Works</a></li>
                            <li><a href="#testimonials" class="hover:text-white">Testimonials</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4">Account</h4>
                        <ul class="space-y-2 text-gray-400">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="hover:text-white text-left">Log Out</button>
                                    </form>
                                </li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white">Log In</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-white">Register</a></li>
                            @endauth
                            <li><a href="#" class="hover:text-white">Support</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4">Legal</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Privacy</a></li>
                            <li><a href="#" class="hover:text-white">Terms</a></li>
                            <li><a href="#" class="hover:text-white">Security</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2026 SummarAIze. All rights reserved.</p>
                <p class="mt-2">Powered with <i class="fas fa-heart text-red-500"></i> by Tritek Academy for productive teams everywhere</p>
            </div>
        </div>
    </footer>

    <script>
        // Simple script to demonstrate interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Upload progress animation for demo
            const progressFill = document.querySelector('.upload-progress-fill');
            if (progressFill) {
                let width = 65;
                
                const progressInterval = setInterval(() => {
                    if (width < 100) {
                        width += 1;
                        progressFill.style.width = width + '%';
                    } else {
                        clearInterval(progressInterval);
                    }
                }, 500);
            }
            
            // Toggle playback highlight for demo
            const playbackText = document.querySelector('.play-back-highlight');
            if (playbackText) {
                setInterval(() => {
                    playbackText.classList.toggle('playback-highlight');
                }, 2000);
            }
            
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                    }
                });
            }
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>