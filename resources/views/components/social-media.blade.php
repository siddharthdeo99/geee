<div class="flex space-x-4">
    @if(!empty($socialSettings->facebook_link))
        <a href="{{ $socialSettings->facebook_link }}" class="text-gray-700 hover:text-blue-800  transition duration-300">
            <x-icon-facebook-2 class="w-8 h-8 " />
        </a>
    @endif

    @if(!empty($socialSettings->twitter_link))
        <a href="{{ $socialSettings->twitter_link }}" class="text-gray-700 hover:text-blue-600 transition duration-300">
            <x-icon-twitter-1 class="w-8 h-8" />
        </a>
    @endif

    @if(!empty($socialSettings->linkedin_link))
        <a href="{{ $socialSettings->linkedin_link }}" class="text-gray-700 hover:text-blue-900 transition duration-300">
            <x-icon-linkedin class="w-8 h-8" />
        </a>
    @endif

    @if(!empty($socialSettings->instagram_link))
        <a href="{{ $socialSettings->instagram_link }}" class="text-gray-700 hover:text-pink-800 transition duration-300">
            <x-icon-instagram-2 class="w-8 h-8" />
        </a>
    @endif
</div>
