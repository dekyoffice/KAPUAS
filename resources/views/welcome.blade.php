<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0; url={{ auth()->check() ? route('dashboard') : route('login') }}">
    <title>Sistem Informasi KAPUAS</title>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <p class="text-sm text-slate-500">Mengarahkan ke Sistem Informasi KAPUAS...</p>
</body>
</html>
