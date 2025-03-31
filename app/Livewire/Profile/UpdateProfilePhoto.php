<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfilePhoto extends Component
{
    use WithFileUploads;

    public $photo;
    public $user;
    public $photoUploaded = false;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024|mimes:jpg,jpeg,png,gif',
        ]);

        $this->photoUploaded = true;
    }

    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024|mimes:jpg,jpeg,png,gif',
        ]);

        // Remove old photo if it exists
        if ($this->user->photo_path && Storage::disk('public')->exists($this->user->photo_path)) {
            Storage::disk('public')->delete($this->user->photo_path);
        }

        // Store the new photo
        $photoPath = $this->photo->store('profile-photos', 'public');
        
        // Update user's photo path
        $this->user->photo_path = $photoPath;
        $this->user->save();

        // Reset state and show success message
        $this->photoUploaded = false;
        $this->photo = null;
        
        session()->flash('success', 'Profile photo updated successfully!');
        $this->dispatch('profile-photo-updated');
    }

    public function removePhoto()
    {
        // Remove current photo if it exists
        if ($this->user->photo_path && Storage::disk('public')->exists($this->user->photo_path)) {
            Storage::disk('public')->delete($this->user->photo_path);
        }

        // Clear the photo path in the database
        $this->user->photo_path = null;
        $this->user->save();
        
        session()->flash('success', 'Profile photo removed successfully!');
        $this->dispatch('profile-photo-updated');
    }

    public function cancel()
    {
        $this->photo = null;
        $this->photoUploaded = false;
    }

    public function render()
    {
        return view('livewire.profile.update-profile-photo');
    }
} 