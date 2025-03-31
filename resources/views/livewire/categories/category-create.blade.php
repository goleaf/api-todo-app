<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Create New Category</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <form wire:submit.prevent="create">
            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    wire:model.defer="name" 
                    class="w-full px-3 py-2 border rounded-md @error('name') border-red-500 @enderror"
                    autofocus
                >
                @error('name') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea 
                    id="description" 
                    wire:model.defer="description" 
                    rows="3" 
                    class="w-full px-3 py-2 border rounded-md @error('description') border-red-500 @enderror"
                ></textarea>
                @error('description') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>
            
            <!-- Color Picker -->
            <div class="mb-4">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                    Color <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center">
                    <input 
                        type="color" 
                        id="color" 
                        wire:model.defer="color" 
                        class="h-10 w-10 border rounded-md cursor-pointer mr-2"
                    >
                    <input 
                        type="text" 
                        wire:model.defer="color" 
                        class="w-full px-3 py-2 border rounded-md @error('color') border-red-500 @enderror"
                    >
                </div>
                @error('color') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>
            
            <!-- Icon Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Icon
                </label>
                <div class="grid grid-cols-6 gap-2 mb-2">
                    @foreach($icons as $iconName)
                        <div 
                            wire:click="$set('icon', '{{ $iconName }}')"
                            class="flex items-center justify-center border rounded-md p-2 cursor-pointer hover:bg-gray-100 {{ $icon === $iconName ? 'bg-blue-100 border-blue-500' : '' }}"
                        >
                            <i class="fas fa-{{ $iconName }} text-gray-700"></i>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center">
                    <span class="mr-2">Selected Icon:</span>
                    @if($icon)
                        <div class="flex items-center">
                            <i class="fas fa-{{ $icon }} text-lg mr-2"></i>
                            <span>{{ $icon }}</span>
                        </div>
                    @else
                        <span class="text-gray-500">None selected</span>
                    @endif
                </div>
                @error('icon') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <button 
                    type="button" 
                    wire:click="cancel" 
                    class="px-4 py-2 border rounded-md hover:bg-gray-100"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>
