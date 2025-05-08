<x-app-layout>
    <h5 class="mt-5 text-center">Contract Details for {{ $contract->employee->first_name }}</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('contract.update', ['contract' => $contract->id]) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Personal Information Group -->
            <fieldset class="p-2 mb-4 border">
                {{-- employee id --}}
                <input type="hidden" name="employee_id" value="{{ $contract->employee->employee_id }}">
                <legend class="w-auto">Contract Information</legend>
                <div class="mb-3 row">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <x-forms.input name="start_date" label="Start Date" type="date" id="start_date"
                                value="{{ old('start_date', isset($contract) && $contract->start_date ? $contract->start_date->toDateString() : '') }}" />
                        </div>

                        <div class="col-md-6">
                            <x-forms.input name="end_date" label="End Date" type="date" id="end_date"
                                value="{{ old('end_date', isset($contract) && $contract->end_date ? $contract->end_date->toDateString() : '') }}" />
                        </div>

                        <div class="col-md-12">
                            <x-forms.text-area name="description" label="Contract Description" id="description"
                                :value="old('description', $contract->description ?? '')" />
                        </div>

                        <div class="col-md-12">
                            <x-forms.repeater name="contract_documents" label="Contract Documents" :values="$contract->contract_documents ?? []" />
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="form-group">
                <input class="btn btn-primary btn-block" type="submit" value="ADD">
            </div>
        </form>
    </div>
</x-app-layout>
