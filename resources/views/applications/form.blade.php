<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $form->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="intro row justify-content-center text-light bg-primary">
                <div class="col-12">
                    {{-- logo --}}
                    <div class="text-center">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="company logo"
                        class="object-fit-contain border rounded img-fluid" style="max-width: 100%; height: auto;">
                    </div>
                </div>

                <div class="col-12">
                    {{-- logo --}}
                    <div class="text-center">
                        <p class="text-center mt-5 mb-5 text-gray-100">
                            Welcome to the UNSCT Job Application. Kindly Provide As Enough details as you can.
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('applications.store') }}" accept-charset="UTF-8"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}

                        @include ('entries.form', ['formMode' => 'create'])
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
            let selectedRadioId = $(this).attr("id"); // The clicked radio button ID
            let selectedValue = $(this).val(); // The selected value of the radio button
            // Iterate through all fields with conditional visibility
            $(".question[data-radio-field]").each(function() {
                let controllingFieldId = $(this).data("radio-field"); // The field controlling visibility
                let triggerValue = $(this).data("trigger-value"); // The value that triggers visibility
                //remove _value from selectedRadioId like 3_1_value to 3
                selectedRadioId = selectedRadioId.split("_")[0];
                // Check if the clicked radio button controls this field

                if (controllingFieldId == selectedRadioId) {
                    if (selectedValue.trim() === triggerValue.trim()) {
                        $(this).show();

                    } else {
                        $(this).hide();

                    }
                }
            });
        });

        //repeater js
        let index = 1; // Start indexing from 1 since the first entry is 0

        $("#add-repeater").click(function() {
            const newRepeater = $(".repeater:first").clone();
            newRepeater.find("input").each(function() {
                // Update the name attribute with the new index
                const name = $(this)
                    .attr("name")
                    .replace(/\[\d+\]/, `[${index}]`);
                $(this).attr("name", name).val(""); // Clear the input values
            });
            newRepeater.attr("data-index", index);
            $("#repeater-container tbody").append(newRepeater);
            index++; // Increment index for next clone
        });

        $("#repeater-container").on("click", ".remove-btn", function() {
            if ($("#repeater-container .repeater").length > 1) {
                $(this).closest(".repeater").remove(); // Remove the repeater
                updateIndices(); // Update indices after removal
            } else {
                alert("At least one entry is required.");
            }
        });

        function updateIndices() {
            $("#repeater-container .repeater").each(function(i) {
                $(this).attr("data-index", i); // Update the data-index attribute
                $(this)
                    .find("input")
                    .each(function() {
                        // Update the name attribute with the new index
                        const name = $(this)
                            .attr("name")
                            .replace(/\[\d+\]/, `[${i}]`);
                        $(this).attr("name", name);
                    });
            });
        }
    </script>
</body>

</html>
