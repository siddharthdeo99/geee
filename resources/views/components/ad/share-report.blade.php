@props(['ad'])
<div class="flex items-center gap-x-6 font-medium">
    <!-- Share Icon with Text -->
    <div x-on:click="$dispatch('open-modal', { id: 'share-ad' })" class="flex items-center gap-x-1 cursor-pointer transition-all hover:transform hover:-translate-y-1">
        <x-heroicon-o-share class="w-5 h-5" />
        <span>Share</span>
    </div>

    <!-- Report Icon with Text -->
    <div wire:click="openReportAd" class="flex items-center gap-x-1 cursor-pointer transition-all hover:transform hover:-translate-y-1">
        <x-heroicon-o-shield-exclamation class="w-5 h-5" />
        <span>Report</span>
    </div>
</div>
