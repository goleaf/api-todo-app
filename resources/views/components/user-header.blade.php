<div class="user-header d-flex align-items-center justify-content-end">
    @auth
        <div class="d-flex align-items-center">
            <!-- Greeting section -->
            <div class="greeting-text me-3 text-end">
                <div class="fw-bold text-dark">Hello, {{ $user->name }}!</div>
                <div class="text-secondary small">
                    @if($taskCount > 0)
                        {{ $taskCount }} {{ Str::plural('task', $taskCount) }} pending
                    @else
                        No tasks pending
                    @endif
                </div>
            </div>
            
            <!-- Avatar with dropdown -->
            <div class="dropdown">
                <button class="btn p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle shadow-sm" 
                                     width="40" height="40">
                            @else
                                <div class="rounded-circle bg-purple text-white d-flex align-items-center justify-content-center shadow-sm" 
                                     style="width: 40px; height: 40px; background-color: #6f42c1;">
                                    <span class="fw-medium">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2" aria-labelledby="userDropdown">
                    <li class="px-3 py-2 text-muted small">Signed in as <strong>{{ $user->email }}</strong></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('tasks.index') }}">
                        <i class="bi bi-check2-square me-2"></i> Tasks
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    @else
        <div class="d-flex gap-2">
            <a href="{{ route('login') }}" class="btn btn-outline-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
        </div>
    @endauth
</div> 