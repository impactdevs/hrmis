<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-12 card card-body">
            <div class="intro row justify-content-center text-light bg-primary py-5">
                <div class="col-12 text-center mb-4">
                    <img src="{{ asset('assets/img/quotient.png') }}" style="max-height: 80px; width: auto;" class="rounded-lg"
                        alt="{{ config('zeus.site_title', config('app.name', 'Laravel')) }}">
                </div>

                <div class="col-12">
                    <ul class="list-unstyled">
                        <li class="mb-3">Completing Staff Performance Assessment Forms is mandatory for all UNCST
                            members of staff, including those on probation or temporary terms of service. Any employee
                            on leave or absent for any reason should have a review completed within 15 days of return to
                            work.</li>
                        <li class="mb-3">The Appraisal process offers an opportunity for both appraisers and
                            appraisees to discuss and obtain feedback on performance; therefore, a participatory
                            approach to the appraisal process, consistency, and objectivity are crucial.</li>
                        <li class="mb-3">Oral interviews and appearances before a UNCST Management Assessment Panel
                            may be conducted (under Section 4) when deemed necessary, with the approval of the Executive
                            Secretary, before making the overall assessment and final comments.</li>
                        <li>In cases where information does not fit in the space provided, the back of the same sheet
                            may be used with an indication of “PTO” where applicable.</li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-lg mt-4">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $form->name }}</h5>
                    <form method="POST" action="{{ route('appraisals.store') }}" accept-charset="UTF-8"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        @include('entries.form', ['formMode' => 'create'])
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</x-app-layout>
