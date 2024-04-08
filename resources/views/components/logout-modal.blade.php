<div class="logoutModal fixed z-10 overflow-y-auto hidden"
  style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" aria-labelledby="modal-title"
  role="dialog" aria-modal="true">
  <div class="rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full sm:p-6">
    <div class="relative rounded-lg shadow bg-white">
      <button type="button"
        class="absolute top-3 end-2.5 bg-transparent rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center hover:bg-gray-200"
        onclick="closeModal('.logoutModal')">
        <svg class="w-3 h-3 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
        <span class="sr-only">Close modal</span>
      </button>
      <div class="p-4 md:p-5 text-center">
        <svg class="mx-auto mb-4 w-12 h-12 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 20 20">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <h3 class="mb-5 text-lg font-normal">Are you sure you want to log out?</h3>
        <button type="button"
          class="font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center bg-red-500 text-white hover:bg-red-700"
          onclick="event.preventDefault(); document.querySelector('.logout-form').submit();">
          Yes, I'm sure
        </button>
        <button type="button"
          class="py-2.5 px-5 ms-3 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-blue-700"
          onclick="closeModal('.logoutModal')">
          No, cancel
        </button>
      </div>
    </div>
  </div>
</div>

<form class="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
  @csrf
</form>

<script>
  function openModal(selector) {
    document.querySelector(selector).classList.remove('hidden');
  }

  function closeModal(selector) {
    document.querySelector(selector).classList.add('hidden');
  }
</script>
