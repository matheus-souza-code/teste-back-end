<div id="{{ $id }}" class="fixed inset-0 overflow-y-auto hidden z-50" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md z-50">
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
