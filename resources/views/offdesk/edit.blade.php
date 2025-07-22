<x-app-layout>
    <div class="mt-3">
        <form method="POST" action="{{ route('offdesk.update', $offdesk->off_desk_id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            @include ('offdesk.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
