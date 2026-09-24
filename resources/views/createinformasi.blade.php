<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add for you informasi</title>
</head>
<body>
    <style>
        body {
            background-color: lightslategrey;
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
        input[type=reset] {
            background-color: orangered;
            color: white;
            margin: 10px;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        input[type=submit]:hover {
            background-color: green;
        }

        input[type=reset]:hover {
            background-color: red;
        }

        .container {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .col-25 {
            float: left;
            width: 25%;
            margin-top: 6px;
        }

        .col-75 {
            float: left;
            width: 75%;
            margin-top: 6px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .danger {
            color: red;
        }

        @media screen and (max-width: 600px) {
            .col-25, .col-75 {
                width: 100%;
                margin-top: 0;
            }
            
            input[type=submit] {
                margin-top: 0;
                float: left;
                width: 40%;
            }

            input[type=reset] {
                margin-top: 0;
                float: left;
            }
        }

    </style>
    <div class="container">
        <h3>ADD INFORMASI</h3>
            <form action="{{ route('postsinformasi.store') }}" method="POST" enctype="multipart/form-data">              
                @csrf
                <div class="row">
                    <div class="col-25">
                        <label for="fname">FOTO</label>
                    </div>
                    <div class="col-75">
                        <input type="file" id="fname" name="image">
                    </div>        
                    @error('image')
                        <div class="danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-25">
                        <label for="fname">DESKRIPSI</label>
                    </div>
                    <div class="col-75">
                        <textarea type="text" id="fname" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Masukkan deskripsi" style="height: 300px;"></textarea>
                    </div>       
                    @error('deskripsi')
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
</body>
</html>