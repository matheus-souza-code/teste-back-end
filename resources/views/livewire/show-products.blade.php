<div>
    <div class="mb-4 flex justify-between">
        <x-primary-button type="button" class="open-modal">Add product</x-primary-button>
        <div class="flex gap-2">
            <div class="flex gap-2 justify-center items-center">
                <label for="allowEmptyImages">Allow empty images</label>
                <input wire:model.live="allowEmptyImages" id="allowEmptyImages" type="checkbox" class="rounded">
            </div>
            <select wire:model.live="category" class="w-[150px] block px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <option value="" default selected>All categories</option>
                @forelse ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @empty
                    <option value="">No categories available</option>
                @endforelse
            </select>
            <x-text-input
                type="text"
                wire:model.live="search"
                placeholder="Search products..."
                class="border px-3 py-2 rounded"
            />
        </div>
    </div>
    <div class="flex flex-wrap -m-4">
        @forelse ($products as $product)
            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-4">
                <div class="max-w-sm mx-auto bg-white shadow-md rounded-lg overflow-hidden flex flex-col h-full">
                    <img class="w-full h-48 object-cover" src="{{ $product->image_url }}" alt="product image">
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $product->name }}</h2>
                        <p class="text-gray-700 text-base mb-4 h-[120px]">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600 text-sm font-semibold">Category: <span class="text-gray-800">{{ $product->category}}</span></span>
                            <span class="text-indigo-500 font-bold text-lg">${{ $product->price }}</span>
                        </div>
                        <x-secondary-button class="w-full justify-center mt-auto open-modal" data-product="{{ $product }}">Edit Product</x-secondary-button>
                    </div>
                </div>
            </div>
        @empty
            <p>No products found.</p>
        @endforelse
    </div>
    <div class="mt-6">
        {{ $products->links() }}
    </div>
    <x-custom-modal id="custom-modal">
        <form id="product-form" method="POST">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Add a new product</h2>
            <div class="p-4">
                <div class="text-left mb-2">
                    <x-input-label value="Name"/>
                    <x-text-input type="text" id="input-name" name="name" class="mt-1 block w-full" required />
                    <span id="input-name-error" class="input-error mt-2 text-sm text-red-600 space-y-1" style="display:none;"></span>
                </div>
                <div class="text-left mb-4">
                    <x-input-label value="Price"/>
                    <x-text-input type="number" min="0.00" max="10000.00" step="0.01" id="input-price" name="price" class="mt-1 block w-full" required />
                    <span id="input-price-error" class="input-error mt-2 text-sm text-red-600 space-y-1" style="display:none;"></span>
                </div>
                <div class="text-left mb-4">
                    <x-input-label value="Description"/>
                    <x-textarea-input type="text" id="input-description" name="description" class="mt-1 block w-full" required />
                    <span id="input-description-error" class="input-error mt-2 text-sm text-red-600 space-y-1" style="display:none;"></span>
                </div>
                <div class="text-left mb-4">
                    <x-input-label value="Category"/>
                    <select id="category" name="category" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        @forelse ($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @empty
                            <option value="">No categories available</option>
                        @endforelse
                    </select>
                    <span id="input-category-error" class="input-error mt-2 text-sm text-red-600 space-y-1" style="display:none;"></span>
                </div>
                <div class="text-left mb-4">
                    <x-input-label value="Image url"/>
                    <x-text-input type="text" id="input-image_url" name="image_url" class="mt-1 block w-full" required />
                    <span id="input-image_url-error" class="input-error mt-2 text-sm text-red-600 space-y-1" style="display:none;"></span>
                </div>
            </div>
            <div class="flex justify-between mt-4 p-4" id="action-buttons">
                <x-danger-button id="delete-button" type="button">Delete</x-danger-button>
                <div class="flex gap-2">
                    <x-secondary-button type="button" class="close-modal">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Confirm</x-primary-button>
                </div>
            </div>
        </form>
    </x-custom-modal>
</div>


@script
    <script>
        $(document).ready(function() {
            let selectedProduct = null;

            $('.open-modal').on('click', function(e) {
                let product = this.getAttribute('data-product') ?? null;

                $('#custom-modal').removeClass('hidden');
                $('.input-error').hide();

                selectedProduct = JSON.parse(product) ?? null;

                organizeActionButtons();

                $('#input-name').val(selectedProduct?.name || '');
                $('#input-price').val(selectedProduct?.price || '');
                $('#input-description').val(selectedProduct?.description || '');
                $('#input-category').val(selectedProduct?.category || '');
                $('#input-image_url').val(selectedProduct?.image_url || '');
            });

            $('.close-modal').on('click', function() {
                closeModal();
            });

            $('#product-form').on('submit', function(e) {
                e.preventDefault();
                $('.input-error').hide();
                let actionUrl = selectedProduct 
                    ? `{{ route('products.update', '') }}/${selectedProduct.id}` 
                    : '{{ route('products.store') }}';
                
                $.ajax({
                    url: actionUrl,
                    type: selectedProduct ? 'PUT' : 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $wire.$refresh();
                        closeModal();
                        showFlashMessage(response.message);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors ?? null;
                        if (errors) {
                            Object.entries(errors).forEach(([key, messages]) => {
                                const errorMessage = messages.join(', ');
                                $(`#input-${key}-error`).text(errorMessage).show();
                            });
                        }
                    }
                });
            });

            $('#delete-button').on('click', function(e) {
                const csrf = $('#product-form').find('[name="_token"]').val();
                e.preventDefault();
                $.ajax({
                    url: `{{ route('products.destroy', '') }}/${selectedProduct.id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    },
                    success: function(response) {
                        $wire.$refresh();
                        closeModal();
                        showFlashMessage(response.message);
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message;
                        console.error(errorMessage);
                    }
                });
            })

            function closeModal() {
                $('#custom-modal').addClass('hidden');
            }

            function organizeActionButtons() {
                const actionButtons = $('#action-buttons');
    	        const deleteButton = $('#delete-button');

    	        deleteButton.toggleClass('hidden', !selectedProduct);

                if (selectedProduct) {
                    actionButtons.removeClass('justify-end').addClass('justify-between');
                } else {
                    actionButtons.removeClass('justify-between').addClass('justify-end');
                }
            }

            function showFlashMessage(message, type) {
                const flashMessage = $('<div></div>')
                    .addClass(`flash-message flash-success`)
                    .text(message);

                $('body').append(flashMessage);

                setTimeout(() => {
                    flashMessage.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    </script>
@endscript

@assets
    <style>
        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px;
            border-radius: 5px;
            color: #fff;
        }

        .flash-success {
            background-color: #4CAF50;
        }

        .flash-error {
            background-color: #F44336;
        }
    </style>
@endassets
