@php
    use App\Models\UserProfile;

    $__authUser = auth()->user();
    $__authProfile = $__authUser
        ? UserProfile::where('user_id', $__authUser->id)->first()
        : null;

    $__authAvatarUrl = $__authProfile?->avatar
        ? \Illuminate\Support\Facades\Storage::url($__authProfile->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($__authUser->name ?? '?') . '&background=D4A537&color=123524';
@endphp

<!-- ========== FACEBOOK-STYLE HEADER ========== -->
<header class="w-full bg-white dark:bg-[#1a1a1a] border-b border-black/10 dark:border-white/10 shadow-sm sticky top-0 z-50 select-none">
  <nav class="max-w-[1100px] mx-auto flex items-center justify-between px-4 py-2 gap-4">

    <!-- Logo + Search -->
    <div class="flex items-center gap-3 min-w-0">
      <a href="{{ route('alumni.dashboard') }}" class="shrink-0">
        <img src="https://tse2.mm.bing.net/th/id/OIP.D0DJ0ePPxNcvYOeq6q9esQAAAA?pid=Api&P=0&h=180"
             alt="School Logo"
             class="w-10 h-10 rounded-full ring-1 ring-[#D4A537]/50 object-cover">
      </a>
      <div class="hidden md:flex items-center bg-[#F0F2F5] dark:bg-[#3a3b3c] rounded-full px-3 py-2 w-64 focus-within:ring-2 focus-within:ring-[#1877F2]/40">
        <svg class="w-4 h-4 text-black/50 dark:text-white/60 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3"/></svg>
        <input type="text" placeholder="Search alumni wall" class="ml-2 bg-transparent border-0 text-sm text-black dark:text-white placeholder:text-black/40 dark:placeholder:text-white/50 focus:outline-none w-full">
      </div>
    </div>

    <!-- Center Navigation (Desktop) -->
    <ul class="hidden lg:flex flex-row items-center justify-center flex-1 gap-1 text-black/60 dark:text-white/60">

        <li>
            <a href="{{ route('alumni.dashboard') }}"
               class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 dark:hover:bg-[#3a3b3c] transition relative group
               {{ request()->routeIs('alumni.dashboard') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </a>
        </li>
        <li>
            <a href="{{ route('alumni.message') }}"
               class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 dark:hover:bg-[#3a3b3c] transition relative group
               {{ request()->routeIs('alumni.message') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
            </a>
        </li>
        <li>
            <a href="{{ route('alumni.settings') }}"
               class="flex items-center justify-center w-28 h-12 rounded-lg hover:bg-black/5 dark:hover:bg-[#3a3b3c] transition
               {{ request()->routeIs('alumni.settings') ? 'text-[#1877F2] border-b-4 border-[#1877F2] rounded-none' : '' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </a>
        </li>
    </ul>

    <!-- Right: Avatar + Actions (desktop only — mobile users get these in the dropdown menu) -->
    <div class="hidden lg:flex items-center gap-2">
        <a href="{{ route('alumni.profile') }}" class="w-10 h-10 rounded-full overflow-hidden ring-1 ring-black/10 dark:ring-white/10 hover:opacity-90 transition shrink-0" title="My Profile">
            <img src="{{ $__authAvatarUrl }}" alt="{{ $__authUser->name ?? 'Profile' }}" class="w-full h-full object-cover">
        </a>
        <livewire:auth::logout />
    </div>

    <!-- Mobile: compact avatar (identity at a glance) + Menu Toggle -->
    <div class="flex lg:hidden items-center gap-2">
        <a href="{{ route('alumni.profile') }}" class="w-9 h-9 rounded-full overflow-hidden ring-1 ring-black/10 dark:ring-white/10 shrink-0" title="My Profile">
            <img src="{{ $__authAvatarUrl }}" alt="{{ $__authUser->name ?? 'Profile' }}" class="w-full h-full object-cover">
        </a>
        <button class="p-2 rounded-full bg-[#F0F2F5] dark:bg-[#3a3b3c] hover:bg-[#E4E6EB] dark:hover:bg-[#4e4f50] transition" id="menu-toggle">
          <svg class="w-5 h-5 text-black dark:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="4" x2="20" y1="6" y2="6"/>
            <line x1="4" x2="20" y1="12" y2="12"/>
            <line x1="4" x2="20" y1="18" y2="18"/>
          </svg>
        </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden flex-col gap-y-1 px-4 pb-3 lg:hidden bg-white dark:bg-[#1a1a1a] border-t border-black/10 dark:border-white/5">
      <a href="{{ route('alumni.dashboard') }}" class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
          <span class="font-medium">Home</span>
      </a>
      <a href="{{ route('alumni.message') }}" class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
          <span class="font-medium">Messages</span>
      </a>
      <a href="{{ route('alumni.settings') }}" class="flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          <span class="font-medium">Settings</span>
      </a>

      <div class="my-1 border-t border-black/10 dark:border-white/10"></div>
      <button type="button" class="w-full flex items-center gap-x-3 px-3 py-3 rounded-lg text-black/80 dark:text-white/80 hover:bg-[#F0F2F5] dark:hover:bg-[#3a3b3c] transition-colors" title="Notifications">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
          <span class="font-medium">Notifications</span>
      </button>

      <div class="my-1 border-t border-black/10 dark:border-white/10"></div>

      <div class="[&>*]:w-full [&_button]:flex [&_button]:items-center [&_button]:gap-x-3 [&_button]:px-3 [&_button]:py-3 [&_button]:rounded-lg [&_button]:text-red-600 dark:[&_button]:text-red-400 [&_button]:hover:bg-red-50 dark:[&_button]:hover:bg-red-500/10 [&_button]:transition-colors [&_button]:font-medium">
          <livewire:auth::logout />
      </div>
  </div>

</header>
<!-- ========== END HEADER ========== -->

<script>
  document.getElementById('menu-toggle')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
  });
</script>
