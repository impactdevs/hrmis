<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editing-{{ $form->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="intro row justify-content-center bg-primary text-light">
                    <div class="col-12">
                        {{-- logo --}}
                        <div class="text-center">
                            <img src="{{ asset('images/logo_bigger_white-1.png') }}" height="80vh" class="rounded-lg"
                                alt="{{ config('zeus.site_title', config('app.name', 'Laravel')) }}">
                        </div>
                    </div>

                    <div class="col-12">
                        {{-- logo --}}
                        <div class="text-center">
                            <p class="text-center mt-5 mb-5 text-gray-100">
                                Welcome to the BPO Data survey form. You are required to answer all the questions in the
                                form as accurately as possible to help us understand you better and make better conclusions
                                and aggregations from our research. We do not intend to sell your data to any third party.
                                We are only interested in understanding the data to help us make better decisions. Rest
                                assured that the data you provide is secure and will not be shared with any third party.
                                Also, this is a government-approved survey form. Thank you for your time and cooperation.
                                <br>
                                <strong>NOTE: Spare 25-30 minutes to complete the survey form.</strong>
                            </p>
                        </div>
                    </div>

                    <div class="col-12">
                        {{-- logo --}}
                        <div class="text-center">
                            @auth
                                <p><strong>Logged in as {{ auth()->user()->name }}</strong></p>
                            @endauth

                            @guest
                                <p><strong>Not logged in</strong></p>
                            @endguest
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $form->name }}</h5>
                        <form method="POST" action="{{ route('entry.update-up', $entry->id) }}" accept-charset="UTF-8"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @include ('entries.edit-form', ['formMode' => 'edit'])
                        </form>
                    </div>
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

    <script>
        $(document).ready(function() {


            //show swal alert
            @if (session('success'))
                swal({
                    title: "Success!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    button: "OK",
                });
            @elseif (session('error'))
                swal({
                    title: "Error!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    button: "OK",
                });
            @endif

        });

        // On radio button click, check conditions again
        $('input[type="radio"]').on("click", function() {
            console.log("clicking");
            let selectedRadioId = $(this).attr("id"); // The clicked radio button ID
            console.log(selectedRadioId);
            let selectedValue = $(this).val(); // The selected value of the radio button
            console.log(selectedValue);
            // Iterate through all fields with conditional visibility
            $(".question[data-radio-field]").each(function() {
                let controllingFieldId = $(this).data("radio-field"); // The field controlling visibility
                console.log("controlling field:", controllingFieldId);
                let triggerValue = $(this).data("trigger-value"); // The value that triggers visibility
                console.log("trigger value:", triggerValue);
                //remove _value from selectedRadioId like 3_1_value to 3
                selectedRadioId = selectedRadioId.split("_")[0];
                // Check if the clicked radio button controls this field

                if (controllingFieldId == selectedRadioId) {
                    console.log("found controlling field");
                    if (selectedValue.trim() === triggerValue.trim()) {
                        $(this).show();

                    } else {
                        $(this).hide();

                    }
                }
            });
        });
    </script>
</body>

</html>
