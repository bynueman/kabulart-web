<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add for you gallery</title>
</head>
<body>
    <style>
        body {
            background-color: lightgreen;
        }

        h3 {
            text-align: center;
            font-size: 30px;
        }

        * {
            box-sizing: border-box;
        }

        input[type=text], select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        label {
            padding: 12px 12px 12px 0;
            display: inline-block;
        }

        input[type=submit] {
            background-color: #04AA6D;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
            margin: 10px;
        }

        input[type=file] {
            background-color: #04AA6D;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
            float: left;
        }

        input[type=reset] {
            background-color: red;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
            margin: 10px;
        }

        input[type=submit]:hover {
            background-color: green;
        }

        input[type=reset]:hover {
            background-color: orangered;
        }

        .container {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .col-25 {
            float: left;
            width: 15%;
            margin-top: 6px;
        }

        .col-75 {
            float: left;
            width: 85%;
            margin-top: 6px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        @media screen and (max-width: 600px) {
            .col-25, .col-75 {
                width: 100%;
                margin-top: 0;
            }

            input[type=submit] {
                margin: 10px;
                float: left;
                width: 40%;
            }

            input[type=reset] {
                margin: 10px;
                float: left;
            }
        }
        .danger {
            color: red;
        }
    </style>
    <div class="container">
        <div>
            <h3>Add Galery</h3>
        </div>

        <form action="{{ route('postsgalery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf 
            <div class="row">
                <div class="col-25">
                    <label for="fname">FOTO</label>
                </div>
                <div class="col-75">
                    <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp,image/avif">
                    <div id="imagePreviewWrap" style="display:none; margin-top:10px;">
                        <img id="imagePreview" src="" alt="Pratinjau Foto" style="max-height: 180px; max-width: 100%; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                        <p id="imageFileInfo" style="font-size: 13px; color: #444; margin-top: 4px;"></p>
                    </div>
                    <div id="clientError" class="danger" style="display:none; margin-top:6px;"></div>
                </div>
                @error('image')
                    <div class="danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row">
                <div class="col-25">
                    <label for="fname">NAMA</label>
                </div>
                <div class="col-75">
                    <input type="text" id="fname" name="nama" value="{{ old('nama') }}" placeholder="Masukkan Nama Produk">
                </div>
                @error('nama')
                    <div class="danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row">
                <div class="col-25">
                    <label for="fname">UKURAN</label>
                </div>
                <div class="col-75">
                    <input type="text" id="rupiah" name="dimensi" value="{{ old('dimensi') }}" placeholder="Masukkan Dimensi Produk">
                </div>
                @error('dimensi')
                    <div class="danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row">
                <div class="col-25">
                    <label for="fname">LINK WA</label>
                </div>
                <div class="col-75">
                    <input type="text" id="fname" name="link"  value="{{ old('link') }}" placeholder="Masukkan Link Produk">
                </div>
                @error('link')
                    <div class="danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row">
                <input type="submit" value="SIMPAN" ></input>
                <input type="reset" value="RESET" ></input>
            </div>

        </form>
    </div>
    <script>
        var currentPreviewUrl = null;
        var imageInput = document.getElementById('imageInput');
        var previewWrap = document.getElementById('imagePreviewWrap');
        var previewImg = document.getElementById('imagePreview');
        var fileInfo = document.getElementById('imageFileInfo');
        var clientError = document.getElementById('clientError');

        function cleanupPreview() {
            if (currentPreviewUrl) {
                URL.revokeObjectURL(currentPreviewUrl);
                currentPreviewUrl = null;
            }
            if (previewWrap) previewWrap.style.display = 'none';
            if (clientError) clientError.style.display = 'none';
        }

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                cleanupPreview();
                var file = e.target.files[0];
                if (!file) return;

                if (file.size > 15 * 1024 * 1024) {
                    clientError.textContent = 'Ukuran file melebihi 15MB (' + (file.size / 1048576).toFixed(1) + ' MB). Harap pilih file yang lebih kecil.';
                    clientError.style.display = 'block';
                    imageInput.value = '';
                    return;
                }

                currentPreviewUrl = URL.createObjectURL(file);
                previewImg.src = currentPreviewUrl;
                fileInfo.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB) — Dioptimasi otomatis saat disimpan.';
                previewWrap.style.display = 'block';
            });
        }

        var form = document.querySelector('form');
        if (form) {
            form.addEventListener('reset', function() {
                cleanupPreview();
            });
        }
    </script>
</body>
</html>