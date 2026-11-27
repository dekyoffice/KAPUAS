<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Informasi KAPUAS</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/favicon.png')); ?>">
    <meta name="description" content="Login ke Sistem Informasi KAPUAS — Platform Pengarsipan Dokumen Digital Resmi Instansi">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-navy:          #003CAA;
            --color-navy-dark:     #0046B4;
            --color-primary:       #0050C8;
            --color-primary-light: #1478F0;
            --color-accent:        #3296FA;
            --color-accent-light:  #46AAFA;
            --color-ice:           #D2E6FA;
            --color-ink:           #1E293B;
            --color-ink-muted:     #64748B;
            --color-line:          #E2E8F0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Left panel — deep royal navy: gelap agar logo biru menonjol,
           tapi masih satu family dengan sidebar biru cerah di dashboard */
        .login-panel-left {
            background: linear-gradient(160deg, #051642 0%, #0a2d6e 50%, #0d3580 100%);
        }

        /* Floating mountain shapes */
        .mountain-shape {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        /* Input focus ring */
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(50, 150, 250, 0.2);
            border-color: #3296FA;
        }

        /* Submit button gradient */
        .btn-primary {
            background: linear-gradient(135deg, #003CAA 0%, #0050C8 60%, #1478F0 100%);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0046B4 0%, #1478F0 100%);
            box-shadow: 0 8px 24px rgba(0, 80, 200, 0.35);
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 flex">

    <!-- ===== LEFT PANEL — Branding ===== -->
    <div class="login-panel-left hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden flex-col items-center justify-center p-12 text-white">

        <!-- Background glow orbs — biru terang di atas dark navy untuk depth -->
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(50,150,250,0.25) 0%, transparent 70%)"></div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(0,80,200,0.20) 0%, transparent 70%)"></div>
        <div class="absolute -bottom-16 right-1/3 w-96 h-96 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(70,170,250,0.15) 0%, transparent 70%)"></div>
        <!-- Bintik kecil dekoratif -->
        <div class="absolute top-16 left-16 w-2 h-2 rounded-full bg-accent/40 pointer-events-none"></div>
        <div class="absolute top-1/3 right-24 w-1.5 h-1.5 rounded-full bg-accent-light/50 pointer-events-none"></div>
        <div class="absolute bottom-32 left-32 w-2 h-2 rounded-full bg-primary/50 pointer-events-none"></div>

        <!-- Wave decoration — warna accent biru agar menyatu dengan dark bg -->
        <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 320" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#1478F0" fill-opacity="0.12" d="M0,224L60,202.7C120,181,240,139,360,138.7C480,139,600,181,720,208C840,235,960,245,1080,229.3C1200,213,1320,171,1380,149.3L1440,128L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
        </svg>
        <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 320" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#3296FA" fill-opacity="0.07" d="M0,288L60,266.7C120,245,240,203,360,186.7C480,171,600,181,720,197.3C840,213,960,235,1080,229.3C1200,224,1320,192,1380,176L1440,160L1440,320L0,320Z"></path>
        </svg>

        <!-- Content -->
        <div class="relative z-10 text-center max-w-md">
            <!-- Logo — border lebih terang & glow agar kontras di atas dark bg -->
            <div class="flex items-center justify-center mb-8">
                <div class="w-24 h-24 rounded-2xl border-2 border-accent/50 flex items-center justify-center shadow-2xl relative" style="background: rgba(50,150,250,0.12); box-shadow: 0 0 40px rgba(50,150,250,0.25), 0 20px 60px rgba(0,0,0,0.4)">
                    <img src="<?php echo e(asset('images/LOGO_KAPUAS.png')); ?>" alt="Logo KAPUAS" class="w-16 h-16 object-contain" onerror="this.parentElement.innerHTML='<span class=\'text-5xl font-extrabold text-white\'>K</span>'">
                </div>
            </div>

            <h1 class="text-4xl xl:text-5xl font-extrabold tracking-tight mb-4">
                KAPUAS
            </h1>
            <p class="text-xl font-semibold text-white/80 mb-2">
                Sistem Informasi Pengarsipan Digital
            </p>
            <p class="text-sm text-white/60 leading-relaxed max-w-sm mx-auto">
                Platform resmi pengelolaan, pengarsipan, dan pencarian dokumen digital instansi yang terpusat, aman, dan terstruktur.
            </p>

            <!-- Feature cards — glassmorphism di atas dark bg -->
            <div class="mt-10 grid grid-cols-3 gap-4 text-center">
                <div class="rounded-xl p-4 border border-accent/20" style="background: rgba(50,150,250,0.10)">
                    <div class="text-2xl font-extrabold text-white">3</div>
                    <div class="text-xs text-white/50 mt-1 font-medium">Level Akses</div>
                </div>
                <div class="rounded-xl p-4 border border-accent/20" style="background: rgba(50,150,250,0.10)">
                    <div class="text-2xl font-extrabold text-white">100%</div>
                    <div class="text-xs text-white/50 mt-1 font-medium">Digital</div>
                </div>
                <div class="rounded-xl p-4 border border-accent/20" style="background: rgba(50,150,250,0.10)">
                    <div class="text-2xl font-extrabold text-white">&infin;</div>
                    <div class="text-xs text-white/50 mt-1 font-medium">Kapasitas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT PANEL — Form ===== -->
    <div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col items-center justify-center px-6 sm:px-10 lg:px-12 py-12 bg-white min-h-screen">

        <!-- Mobile logo (only visible on small screens) -->
        <div class="lg:hidden text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-3" style="background: linear-gradient(135deg, #003CAA, #3296FA)">
                <img src="<?php echo e(asset('images/LOGO_KAPUAS.png')); ?>" alt="Logo KAPUAS" class="w-10 h-10 object-contain" onerror="this.parentElement.innerHTML='<span class=\'text-2xl font-extrabold text-white\'>K</span>'">
            </div>
            <h2 class="text-2xl font-extrabold text-ink">KAPUAS</h2>
            <p class="text-xs text-ink-muted mt-1">Sistem Pengarsipan Digital Instansi</p>
        </div>

        <div class="w-full max-w-sm">
            <!-- Heading -->
            <div class="mb-8">
                <h2 class="text-2xl font-extrabold text-ink tracking-tight">Selamat Datang</h2>
                <p class="text-sm text-ink-muted mt-1.5">Masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            <!-- Success Flash -->
            <?php if(session('success')): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                <span><?php echo e(session('success')); ?></span>
            </div>
            <?php endif; ?>

            <!-- Error Flash -->
            <?php if(session('error')): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?php echo e(session('error')); ?></span>
            </div>
            <?php endif; ?>

            <!-- Login Error -->
            <?php if($errors->any()): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                <div class="flex items-center gap-2 font-bold mb-1 text-rose-800">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Gagal Masuk</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?php echo e(route('login')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-ink-muted mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="<?php echo e(old('email')); ?>"
                            placeholder="nama@kapuas.go.id"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-line text-ink rounded-xl placeholder-ink-muted/60 text-sm transition-all hover:border-accent/50">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-ink-muted mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" name="password" type="password" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-line text-ink rounded-xl placeholder-ink-muted/60 text-sm transition-all hover:border-accent/50">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-ink-muted hover:text-primary transition-colors">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs text-ink-muted cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-line accent-primary">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-login" class="btn-primary w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-center text-[11px] text-ink-muted/60">
            Sistem Informasi KAPUAS &bull; Hak Cipta &copy; <?php echo e(date('Y')); ?>

        </p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            if (passInput.type === 'password') {
                passInput.type = 'text';
            } else {
                passInput.type = 'password';
            }
        }
    </script>
</body>
</html>
<?php /**PATH D:\KAPUASS\resources\views/auth/login.blade.php ENDPATH**/ ?>