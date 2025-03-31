@props(['user'])

@if($user->onboarding()->inProgress())
    <div class="onboarding-container bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Getting Started</h2>
        <p class="mb-4">Complete these steps to get the most out of your account</p>
        
        <div class="flex items-center mb-3">
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $user->onboarding()->percentageCompleted() }}%"></div>
            </div>
            <span class="ml-2 text-sm text-gray-600">{{ $user->onboarding()->percentageCompleted() }}%</span>
        </div>
        
        <div class="space-y-4 mt-6">
            @foreach($user->onboarding()->steps() as $step)
                <div class="flex items-start p-3 {{ $step->complete() ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded-lg">
                    <div class="flex-shrink-0 mr-3">
                        @if($step->complete())
                            <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-500 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <span class="flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-500 rounded-full">
                                {{ $loop->iteration }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-medium {{ $step->complete() ? 'line-through text-gray-500' : 'text-gray-800' }}">
                            {{ $step->title }}
                        </h3>
                        @if(!$step->complete())
                            <a href="{{ $step->link }}" class="inline-flex items-center mt-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $step->cta }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 text-right">
            <form action="{{ route('onboarding.skip') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                    Skip onboarding
                </button>
            </form>
        </div>
    </div>
@endif 