<x-app-layout>
    <div class="m-3">
        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4">
                <div class="alert alert-danger p-4 rounded">
                    <strong>Whoops! Something went wrong.</strong>
                    <ul class="mt-2 mb-0 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('offdesk.store') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            @include ('offdesk.form', ['formMode' => 'create'])
        </form>
    </div>
</x-app-layout>

