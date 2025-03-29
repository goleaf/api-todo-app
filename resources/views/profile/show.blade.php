<x-app-layout>
    <div class="bg-white shadow rounded p-4 mb-4">
        <h2 class="h4 mb-4">My Profile</h2>
        
        <div class="row">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="rounded-circle img-thumbnail shadow-sm" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-purple text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" 
                         style="width: 150px; height: 150px; background-color: #6f42c1;">
                        <span class="display-4">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                @endif
                
                <h3 class="h5 mt-3">{{ auth()->user()->name }}</h3>
                <p class="text-muted">{{ auth()->user()->email }}</p>
                
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Name</label>
                            <div>{{ auth()->user()->name }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <div>{{ auth()->user()->email }}</div>
                        </div>
                        
                        <div>
                            <label class="form-label text-muted small">Member Since</label>
                            <div>{{ auth()->user()->created_at->format('F j, Y') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Task Summary</h5>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4 text-center">
                                <div class="h3 mb-0">{{ auth()->user()->tasks()->where('status', 'pending')->count() }}</div>
                                <div class="small text-muted">Pending</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="h3 mb-0">{{ auth()->user()->tasks()->where('status', 'in_progress')->count() }}</div>
                                <div class="small text-muted">In Progress</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="h3 mb-0">{{ auth()->user()->tasks()->where('status', 'completed')->count() }}</div>
                                <div class="small text-muted">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 