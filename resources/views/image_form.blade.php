<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR Image to LaTeX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container mt-5">
        <h2>Upload Image for OCR and LaTeX Conversion</h2>

        <!-- Form for uploading an image -->
        <form id="imageForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="image" class="form-label">Choose Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Section for displaying notifications -->
        <div id="notifications" class="mt-4">
            <div id="loading" style="display: none;" class="alert alert-info">Processing image...</div>
            <div id="success" style="display: none;" class="alert alert-success">Image processed successfully!</div>
            <div id="error" style="display: none;" class="alert alert-danger"></div>
        </div>

        <!-- Section for displaying results -->
        <div id="output" class="mt-4">
            <h4>OCR Result:</h4>
            <pre id="ocrResult"></pre>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set the CSRF token in the headers for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#imageForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                // Show loading message
                $('#loading').show();
                $('#success').hide();
                $('#error').hide();
                $('#ocrResult').text('');

                $.ajax({
                    url: '{{ route('image.runScript') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Hide loading and show success message
                        $('#loading').hide();
                        $('#success').show();

                        // Display the OCR result
                        $('#ocrResult').text(response.output);
                    },
                    error: function(xhr, status, error) {
                        // Hide loading and show error message
                        $('#loading').hide();
                        $('#error').show().text('Error: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
