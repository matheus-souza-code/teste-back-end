<div>
    <div class="mb-4 flex justify-between">
        <x-primary-button type="button" class="open-modal">Add category</x-primary-button>
        <x-text-input
            type="text"
            wire:model.live="search"
            placeholder="Search categories..."
            class="border px-3 py-2 rounded"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white border border-gray-200 rounded-lg">
            <thead>
                <tr>
                    <th class="w-[200px] py-2 px-4 border-b border-gray-200 bg-gray-100 text-center text-gray-600 font-semibold uppercase">Id</th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-center text-gray-600 font-semibold uppercase">Name</th>
                    <th class="w-[200px] py-2 px-4 border-b border-gray-200 bg-gray-100 text-center text-gray-600 font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b border-gray-200 text-center">{{ $category->id }}</td>
                        <td class="py-2 px-4 border-b border-gray-200 text-center">{{ $category->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">
                            <div class="flex justify-center gap-4">
                                <x-primary-button type="button" class="open-modal" data-category="{{ $category }}">Edit</x-primary-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
    <x-custom-modal id="custom-modal">
        <form id="category-form" method="POST">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Add a new category</h2>
            <div class="p-4">
                <div class="text-left">
                    <x-input-label value="Name"/>
                    <x-text-input 
                        type="text" 
                        id="input-name" 
                        name="name" 
                        class="mt-1 block w-full" 
                        required 
                    />
                    <span id="input-error" class="mt-2 text-sm text-red-600 space-y-1" style="display:none;"></span>
                </div>
            </div>
            <div class="flex justify-between mt-4 p-4" id="action-buttons">
                <x-danger-button id="delete-button" type="button">Delete</x-danger-button>
                <div class="flex gap-2">
                    <x-secondary-button type="button" class="close-modal">Cancel</x-secondary-button>
                    <x-primary-button type="button">Confirm</x-primary-button>
                </div>
            </div>
        </form>
    </x-custom-modal>
</div>


@script
    <script>
        $(document).ready(function() {
            let selectedCategory = null;

            $('.open-modal').on('click', function(e) {
                let category = this.getAttribute('data-category') ?? null;

                $('#custom-modal').removeClass('hidden');
                $('#input-error').hide();

                selectedCategory = JSON.parse(category) ?? null;

                organizeActionButtons();

                $('#input-name').val(selectedCategory?.name || '');
            });

            $('.close-modal').on('click', function() {
                closeModal();
            });

            $('#category-form').on('submit', function(e) {
                e.preventDefault();
                let actionUrl = selectedCategory 
                    ? `{{ route('categories.update', '') }}/${selectedCategory.id}` 
                    : '{{ route('categories.store') }}';
                
                $.ajax({
                    url: actionUrl,
                    type: selectedCategory ? 'PUT' : 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $wire.$refresh();
                        closeModal();
                        showFlashMessage(response.message);
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message;
                        if (errorMessage) {
                            $('#input-error').text(errorMessage);
                            $('#input-error').show();
                        } else {
                            $('#input-error').hide();
                        }
                    }
                });
            });

            $('#delete-button').on('click', function(e) {
                const csrf = $('#category-form').find('[name="_token"]').val();
                e.preventDefault();
                $.ajax({
                    url: `{{ route('categories.destroy', '') }}/${selectedCategory.id}`,
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
                        if (errorMessage) {
                            $('#input-error').text(errorMessage);
                            $('#input-error').show();
                        } else {
                            $('#input-error').hide();
                        }
                    }
                });
            })

            function closeModal() {
                $('#custom-modal').addClass('hidden');
                $('#overlay').addClass('hidden');
            }

            function organizeActionButtons() {
                const actionButtons = $('#action-buttons');
    	        const deleteButton = $('#delete-button');

    	        deleteButton.toggleClass('hidden', !selectedCategory);

                if (selectedCategory) {
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
