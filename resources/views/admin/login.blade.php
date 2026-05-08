<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body {
        font-family: "Poppins", sans-serif;
      }
    </style>
  </head>
  <body>
    <div class="min-h-screen bg-[#B7C9B9] px-4 py-10 sm:px-6">
      <div class="mx-auto flex min-h-[calc(100vh-5rem)] w-full max-w-[557px] items-center">
        <div class="w-full rounded-[20px] bg-white px-6 py-10 sm:px-10 sm:py-12">
          <img class="mx-auto h-24 w-72 object-contain sm:h-28 sm:w-80" src="{{ asset('images/logoCila.png') }}" alt="Logo CilaAir" />

          <h1 class="mt-8 text-neutral-700 text-3xl font-bold">Selamat datang!</h1>
          <p class="mt-2 text-neutral-700 text-base font-normal">Masuk untuk melanjutkan.</p>

          @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
              <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('admin.login.store') }}" method="POST" class="mt-8 space-y-6">
            @csrf
            <div>
              <label for="login" class="mb-2 block text-stone-400 text-lg sm:text-xl font-medium">Email/Username</label>
              <input
                id="login"
                name="login"
                type="text"
                value="{{ old('login') }}"
                class="h-14 w-full rounded-[5px] border border-stone-400 px-4 text-base sm:text-lg text-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-400"
              />
            </div>

            <div>
              <label for="password" class="mb-2 block text-stone-400 text-lg sm:text-xl font-medium">Password</label>
              <input
                id="password"
                name="password"
                type="password"
                class="h-14 w-full rounded-[5px] border border-stone-400 px-4 text-base sm:text-lg text-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-400"
              />
            </div>

            <button type="submit" class="h-14 w-40 rounded-xl bg-[#628C63] text-white text-lg sm:text-xl font-semibold hover:bg-[#0D2D1E]">
              LOGIN
            </button>

            <div class="flex items-center gap-3">
              <input id="remember" name="remember" type="checkbox" class="h-6 w-6 rounded-[3px] border-[3px] border-neutral-700 accent-neutral-700" />
              <label for="remember" class="text-stone-500 text-base font-medium">Ingat saya</label>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
