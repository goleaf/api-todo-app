<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PhotoUploader extends Component
{
    use WithFileUploads;

    public $photo;
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);
    }

    public function save()
    {
        $this->validate([
            'photo' => 'required|image|max:1024',
        ]);

        // Delete old photo if exists
        if ($this->user->photo_path) {
            Storage::disk('public')->delete($this->user->photo_path);
        }

        // Store the new photo
        $photoPath = $this->photo->store('profile-photos', 'public');

        // Update user's photo_path
        $this->user->update([
            'photo_path' => $photoPath,
        ]);

        $this->reset('photo');
        session()->flash('success', 'Photo updated successfully!');
        $this->dispatch('profile-photo-updated');
    }

    public function deletePhoto()
    {
        if ($this->user->photo_path) {
            Storage::disk('public')->delete($this->user->photo_path);

            $this->user->update([
                'photo_path' => null,
            ]);

            session()->flash('success', 'Profile photo removed successfully!');
            $this->dispatch('profile-photo-updated');
        }
    }

    public function render()
    {
        return view('livewire.profile.photo-uploader');
    }
} 