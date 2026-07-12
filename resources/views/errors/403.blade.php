<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forbidden Access - Google Notes</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F8F9FA] text-[#202124]">
    <div class="min-h-screen flex flex-col items-center justify-center p-6 text-center">
        <!-- Icon -->
        <div class="w-24 h-24 bg-[#EA4335]/10 text-[#EA4335] rounded-3xl flex items-center justify-center mb-8 mx-auto animate-pulse">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="text-6xl font-extrabold text-[#EA4335] mb-3">403</h1>
        <h2 class="text-2xl font-bold text-[#202124] mb-4">Access Denied</h2>
        <p class="text-gray-500 text-sm max-w-md mx-auto mb-8 leading-relaxed">
            You do not have permission to view or access this resource. Please make sure you are logged into the correct account.
        </p>

        <!-- CTA Button -->
        <a href="/" class="inline-flex items-center gap-2 px-6 py-3 bg-[#4285F4] text-white text-sm font-semibold rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</body>
</html>
