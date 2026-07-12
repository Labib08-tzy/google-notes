<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Expired - Google Notes</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F8F9FA] text-[#202124]">
    <div class="min-h-screen flex flex-col items-center justify-center p-6 text-center">
        <!-- Icon -->
        <div class="w-24 h-24 bg-[#FBBC05]/10 text-[#FBBC05] rounded-3xl flex items-center justify-center mb-8 mx-auto animate-pulse">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="text-6xl font-extrabold text-[#FBBC05] mb-3">419</h1>
        <h2 class="text-2xl font-bold text-[#202124] mb-4">Page Expired</h2>
        <p class="text-gray-500 text-sm max-w-md mx-auto mb-8 leading-relaxed">
            Your security token has expired due to inactivity. Please refresh the page and try again.
        </p>

        <!-- CTA Button -->
        <button onclick="window.location.reload();" class="inline-flex items-center gap-2 px-6 py-3 bg-[#4285F4] text-white text-sm font-semibold rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Refresh Page
        </button>
    </div>
</body>
</html>
