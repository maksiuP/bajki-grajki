@push('scripts')
  @livewireStyles
  @livewireScripts
@endpush

<div class="w-full">
  <div class="space-y-3 mb-8 flex flex-col items-center">
    <input type="search" wire:key="search" wire:model.debounce.100ms="search" autocomplete="off" autofocus
      class="w-full px-4 py-2 rounded-lg shadow-lg overflow-hidden bg-gray-50 dark:bg-gray-900 focus:outline-none">

    @foreach ($artists as $artist)
      <a href="{{ route('artists.show', $artist) }}" wire:key="{{ $artist->id }}"
        class="w-full h-12 sm:h-14 flex items-center bg-gray-50 dark:bg-gray-900 rounded-lg shadow-lg overflow-hidden">
        <div class="relative flex-none bg-placeholder-artist w-12 h-12 sm:w-14 sm:h-14 overflow-hidden">
          @if ($artist->photo())
            <div class="bg-gray-400 dark:bg-gray-800 bg-center bg-cover absolute -inset-px"
              style="background-image: url(&quot;{{ $artist->photo_face_placeholder }}&quot;)">
              <img src="{{ $artist->photo('112') }}"
                srcset="
                  {{ $artist->photo('56') }} 1x,
                  {{ $artist->photo('84') }} 1.5x,
                  {{ $artist->photo('112') }} 2x"
                loading="lazy"
                class="w-full h-full object-center object-cover transition-opacity duration-300 opacity-0">
            </div>
          @elseif ($artist->discogsPhoto() && Auth::guest())
            <img src="{{ $artist->discogsPhoto('150') }}"
              class="w-12 h-12 sm:w-14 sm:h-14 object-cover">
          @endif
        </div>
        <div class="flex-grow p-2 pl-3">
          <span class="flex-shrink-0 font-medium">{{ $artist->name }}</span>
        </div>
        <div class="flex-none pr-4">
          @if ($artist->appearances > 1)
            <small class="ml-1.5 h-6 w-6 text-xs inline-flex items-center justify-center bg-yellow-300 text-yellow-800 rounded-full shadow-md">
              {{ $artist->appearances }}
            </small>
          @endif
        </div>
      </a>
    @endforeach
  </div>

  <div class="w-full flex flex-col items-center ">
    {{ $artists->links() }}
  </div>
</div>
