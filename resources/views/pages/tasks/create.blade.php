@extends('layouts.app')

@section('title', __('Create Task'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Use Page Header Component --}}
        <x-ui.page-header :title="__('Create New Task')">
            <x-slot name="actions">
                <x-ui.button :href="route('tasks.index')" variant="secondary" icon="<path fill-rule='evenodd' d='M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z' clip-rule='evenodd' />">
                    {{ __('Back to Tasks') }}
                </x-ui.button>
            </x-slot>
        </x-ui.page-header>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <form action="{{ route('tasks.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Title --}}
                    <x-ui.form-group :label="__('Title')" for="title" :error="$errors->first('title')" required class="md:col-span-2">
                        <x-ui.text-input id="title" name="title" type="text" class="block w-full" :value="old('title')" required autofocus />
                    </x-ui.form-group>

                    {{-- Description --}}
                    <x-ui.form-group :label="__('Description')" for="description" :error="$errors->first('description')" class="md:col-span-2">
                        <x-ui.textarea id="description" name="description" rows="5" class="block w-full">{{ old('description') }}</x-ui.textarea>
                    </x-ui.form-group>

                    {{-- Status --}}
                    <x-ui.form-group :label="__('Status')" for="status" :error="$errors->first('status')">
                        <x-ui.select id="status" name="status" class="block w-full">
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        </x-ui.select>
                    </x-ui.form-group>

                    {{-- Priority --}}
                    <x-ui.form-group :label="__('Priority')" for="priority" :error="$errors->first('priority')">
                        <x-ui.select id="priority" name="priority" class="block w-full">
                            <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                            <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                            <option value="urgent" {{ old('priority', 'medium') == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                        </x-ui.select>
                    </x-ui.form-group>

                    {{-- Category --}}
                    <x-ui.form-group :label="__('Category')" for="category_id" :error="$errors->first('category_id')">
                        <x-ui.select id="category_id" name="category_id" class="block w-full">
                            <option value="">-- {{ __('Select Category') }} --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    </x-ui.form-group>

                    {{-- Due Date --}}
                    <x-ui.form-group :label="__('Due Date')" for="due_date" :error="$errors->first('due_date')">
                        <x-ui.text-input id="due_date" name="due_date" type="date" class="block w-full" :value="old('due_date')" />
                    </x-ui.form-group>

                    {{-- Tags --}}
                    <x-ui.form-group :label="__('Tags')" for="tags" :error="$errors->first('tags')" class="md:col-span-2">
                        @php
                            $tagsOptions = $tags->pluck('name', 'id')->toArray();
                            $selectedTags = old('tags', []);
                        @endphp
                        
                        <x-ui.tom-select
                            name="tags"
                            id="tags"
                            :options="$tagsOptions"
                            :selected="$selectedTags"
                            multiple
                            create-option
                        />
                    </x-ui.form-group>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-ui.button :href="route('tasks.index')" variant="secondary">
                        {{ __('Cancel') }}
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary">
                        {{ __('Create Task') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 