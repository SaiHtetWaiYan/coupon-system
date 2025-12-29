@props(['title', 'value'])

<div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</dt>
    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $value }}</dd>
</div>
