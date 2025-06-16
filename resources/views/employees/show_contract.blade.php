<x-app-layout>
    <div class="mt-3">
        <fieldset class="p-2 mb-4 border">
            <legend class="w-auto">Contract Information</legend>

            <div class="mb-3 row">
                <div class="col-md-6 mb-2">
                    <strong>Start Date:</strong>
                    <div>
                        {{ isset($contract) && $contract->start_date ? $contract->start_date->toDateString() : '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <strong>End Date:</strong>
                    <div>
                        {{ isset($contract) && $contract->end_date ? $contract->end_date->toDateString() : '-' }}
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <strong>Contract Description:</strong>
                    <div>
                        {{ $contract->description ?? '-' }}
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <strong>Contract Documents:</strong>
                    <div>
                        @if ($contract->contract_documents)
                            <div class="flex-wrap gap-2 d-flex">
                                @foreach ($contract->contract_documents as $attachment)
                                    <a href="{{ asset('storage/' . $attachment['proof']) }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                                        @if (Str::endsWith($attachment['proof'], ['.pdf']))
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                        @else
                                            <i class="fas fa-file-image text-primary me-2"></i>
                                        @endif
                                        {{ Str::limit($attachment['title'], 15) }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">No attachments</span>
                        @endif
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</x-app-layout>
