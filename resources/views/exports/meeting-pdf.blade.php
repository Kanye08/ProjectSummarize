<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $meeting->title }} - Meeting Summary</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #1E40AF;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .meta {
            color: #666;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #1E40AF;
            font-size: 20px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #E5E7EB;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        ul li {
            padding-left: 25px;
            position: relative;
            margin-bottom: 10px;
        }
        ul li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10B981;
            font-weight: bold;
        }
        .transcript {
            background-color: #F9FAFB;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .timestamp {
            color: #3B82F6;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin-right: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $meeting->title }}</h1>
        <div class="meta">
            <p><strong>Date:</strong> {{ $meeting->start_time->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Duration:</strong> {{ $meeting->formatted_duration }}</p>
            @if($meeting->location)
                <p><strong>Location:</strong> {{ $meeting->location }}</p>
            @endif
        </div>
    </div>

    @if($meeting->summary)
        <div class="section">
            <h2 class="section-title">Summary</h2>
            <p>{{ $meeting->summary->summary_text }}</p>
        </div>

        @if($meeting->summary->action_points && count($meeting->summary->action_points) > 0)
            <div class="section">
                <h2 class="section-title">Action Points</h2>
                <ul>
                    @foreach($meeting->summary->action_points as $point)
                        <li>{{ $point }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($meeting->summary->key_decisions && count($meeting->summary->key_decisions) > 0)
            <div class="section">
                <h2 class="section-title">Key Decisions</h2>
                <ul>
                    @foreach($meeting->summary->key_decisions as $decision)
                        <li>{{ $decision }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($meeting->summary->key_topics && count($meeting->summary->key_topics) > 0)
            <div class="section">
                <h2 class="section-title">Key Topics</h2>
                <ul>
                    @foreach($meeting->summary->key_topics as $topic)
                        <li>{{ $topic }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if($meeting->sentimentAnalysis)
        <div class="section">
            <h2 class="section-title">Sentiment Analysis</h2>
            <p><strong>Overall Sentiment:</strong> {{ ucfirst($meeting->sentimentAnalysis->overall_sentiment) }}</p>
            <p>
                <strong>Positive:</strong> {{ $meeting->sentimentAnalysis->positive_score }}% | 
                <strong>Neutral:</strong> {{ $meeting->sentimentAnalysis->neutral_score }}% | 
                <strong>Negative:</strong> {{ $meeting->sentimentAnalysis->negative_score }}%
            </p>
        </div>
    @endif

    @if($meeting->transcript)
        <div class="section">
            <h2 class="section-title">Full Transcript</h2>
            <div class="transcript">
                @if($meeting->transcript->segments && count($meeting->transcript->segments) > 0)
                    @foreach($meeting->transcript->segments as $segment)
                        <p>
                            <span class="timestamp">[{{ gmdate('i:s', (int)$segment['start']) }}]</span>
                            {{ $segment['text'] }}
                        </p>
                    @endforeach
                @else
                    <p>{{ $meeting->transcript->full_text }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Project Summarize - Meeting Transcription & Analysis</p>
    </div>
</body>
</html>