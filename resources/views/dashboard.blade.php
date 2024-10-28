<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 text-gray-900">
        {{ __("You're logged in!") }}
    </div>
    @can('edit articles')
        You can EDIT ARTICLES.
    @endcan
    @can('publish articles')
        You can PUBLISH ARTICLES.
    @endcan
    @can('only super-admins can see this section')
        Congratulations, you are a super-admin!
    @endcan
</x-app-layout>
