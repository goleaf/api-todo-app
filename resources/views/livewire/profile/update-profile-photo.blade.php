<div>
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-camera mr-2"></i> Profile Photo
        </h3>

        <div class="flex items-start">
            <!-- Current Profile Photo -->
            <div class="mr-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Current Photo</div>
                <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    @if($user->photo_path)
                        <img src="{{ Storage::url($user->photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full bg-purple-600 dark:bg-purple-500 text-white text-2xl flex items-center justify-center font-semibold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upload New Photo -->
            <div class="flex-1">
                @if($photoUploaded)
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Preview</div>
                        <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover">
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button wire:click="save" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-save mr-2"></i> Save
                        </button>
                        <button wire:click="cancel" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        <label for="photo-upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Upload new photo
                        </label>
                        <div 
                            x-data="{ isHovering: false }" 
                            @dragover.prevent="isHovering = true" 
                            @dragleave.prevent="isHovering = false"
                            @drop.prevent="isHovering = false"
                            :class="{ 'bg-purple-50 dark:bg-purple-900/20 border-purple-300 dark:border-purple-600': isHovering }"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="photo" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-purple-600 dark:text-purple-400 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                        <span>Upload a file</span>
                                        <input id="photo" wire:model="photo" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    PNG, JPG, GIF up to 1MB
                                </p>
                            </div>
                        </div>
                        
                        @error('photo') 
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($user->photo_path)
                        <div class="mt-4">
                            <button wire:click="removePhoto" wire:confirm="Are you sure you want to remove your profile photo?" type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-red-600 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash-alt mr-2"></i> Remove Photo
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div> 