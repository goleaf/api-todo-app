<div>
    <div class="mb-4">
        <div class="flex items-center justify-center">
            <div class="relative">
                @if ($user->photo_url)
                    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                    <button type="button" wire:click="deletePhoto" wire:confirm="Are you sure you want to delete your profile photo?" class="absolute bottom-0 right-0 bg-red-500 text-white rounded-full p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                @else
                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-4">
        <div class="flex flex-col items-center">
            <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profile Photo</label>
            <input type="file" id="photo" wire:model="photo" class="hidden">
            <label for="photo" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 cursor-pointer">
                Choose Photo
            </label>
            <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500">
                Uploading...
            </div>
            @error('photo')
                <span class="mt-1 text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>

        @if ($photo)
            <div class="mt-2 flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400 mb-2">Preview:</span>
                <img src="{{ $photo->temporaryUrl() }}" class="w-32 h-32 rounded-full object-cover">
                <div class="mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Photo
                    </button>
                </div>
            </div>
        @endif
    </form>
</div> 