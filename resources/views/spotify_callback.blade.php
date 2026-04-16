<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Connection</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-400 to-green-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 max-w-md mx-auto text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Spotify Connection</h1>
        <p class="text-lg text-gray-600 mb-6">{{ $message }}</p>
        
        @if(strpos($message, 'successfully') !== false)
            <button onclick="window.close()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                Close this window
            </button>
        @else
            <a href="/dashboard" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Return to Dashboard
            </a>
        @endif
    </div>

    <script>
        // Notify parent window of successful connection
        if ("{{ strpos($message, 'successfully') !== false ? 'true' : 'false' }}" === 'true') {
            if (window.opener && !window.opener.closed) {
                window.opener.spotifyConnected = true;
                window.opener.loadDashboardData();
            }
            setTimeout(() => {
                window.close();
            }, 2000);
        }
    </script>
</body>
</html>
