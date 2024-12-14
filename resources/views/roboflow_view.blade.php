<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token untuk AJAX -->
    <title>Roboflow Image Detection</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Roboflow Image Detection</h1>
    <form id="upload-form">
        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" required>
        <button type="submit">Detect</button>
    </form>

    <div id="result-container">
        <!-- Hasil dari Roboflow akan ditampilkan di sini -->
    </div>

    <script>
        $(function () {
            // Setup CSRF untuk setiap AJAX request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Tangani submit form dengan AJAX
            $("#upload-form").submit(function (e) {
                e.preventDefault(); // Mencegah form melakukan submit biasa
                var formData = new FormData();
                formData.append('image', $('#image')[0].files[0]);

                $.ajax({
                    url: "{{ route('roboflow.detect') }}",
                    type: "POST",
                    data: formData,
                    contentType: false, // Jangan kirim sebagai string biasa
                    processData: false, // Biarkan jQuery menangani data dengan FormData
                    success: function (response) {
                        $("#result-container").html(''); // Bersihkan hasil sebelumnya
                        if (response.predictions) {
                            response.predictions.forEach(prediction => {
                                $("#result-container").append(`
                                    <div>
                                        <h4>Class Detected: ${prediction.class}</h4>
                                        <p>Confidence: ${(prediction.confidence * 100).toFixed(2)}%</p>
                                    </div>
                                    <hr>
                                `);
                            });
                        } else {
                            $("#result-container").html('<p>No predictions found.</p>');
                        }
                    },
                    error: function () {
                        alert('Gagal memproses permintaan');
                    }
                });
            });
        });
    </script>
</body>
</html>
