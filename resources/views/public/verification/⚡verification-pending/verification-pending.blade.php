{{-- resources/views/livewire/verification-pending.blade.php --}}
<div class="min-h-screen flex items-center justify-center bg-[#F7F5EF] px-4">
  <div class="max-w-md w-full text-center bg-white p-10 rounded-3xl shadow-xl border border-[#123524]/5">
    <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#D4A537]/15 flex items-center justify-center">
      <svg class="w-8 h-8 text-[#D4A537]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
      </svg>
    </div>
    <h1 class="text-2xl font-bold text-[#123524]" style="font-family: 'Fraunces', serif;">Verification in Progress</h1>
    <p class="text-[#123524]/60 mt-3 leading-relaxed">
      Our AI system flagged your documents for manual review. An admin will verify your alumni status shortly —
      you'll be notified by email once your account is approved.
    </p>
    <a href="{{ route('login') }}" class="inline-block mt-6 text-sm font-semibold text-[#123524] hover:text-[#D4A537] transition">
      Back to Log In
    </a>
  </div>
</div>
