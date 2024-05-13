@props(['description'])

<div class="p-4 border-t border-gray-200 dark:border-white/20 classic:border-black">
    <h3 class="text-xl mb-4 font-semibold">Description:</h3>
    <div class="prose prose-slate dark:prose-invert">
       {!! $description !!}
    </div>
</div>
