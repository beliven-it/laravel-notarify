<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notarization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            text-align: center;
            padding: 50px;
        }

        h1 {
            color: #2c3e50;
            font-size: 2.5em;
        }

        input[type="file"] {
            margin: 20px 0;
            padding: 10px;
            border: 2px solid #2980b9;
            border-radius: 5px;
        }

        button {
            background-color: #2980b9;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1c598a;
        }

        form {
            display: inline;
            margin: 10px;
        }

        .hidden-input {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Test Notarization</h1>

    <input type="file" name="file" id="file">

    <form id="uploadForm" action="{{ route('notarify.upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <button type="submit" onclick="return attachFileToForm('uploadForm')">Upload</button>
    </form>

    <form id="verifyForm" action="{{ route('notarify.verify') }}" method="post" enctype="multipart/form-data">
        @csrf
        <button type="submit" onclick="return attachFileToForm('verifyForm')">Verify</button>
    </form>

    <script>
        function attachFileToForm(formId) {
            const fileInput = document.getElementById('file');
            if (!fileInput.files.length) {
                alert("Please select a file first.");
                return false;
            }

            const form = document.getElementById(formId);
            const hiddenFileInput = document.createElement('input');
            hiddenFileInput.type = 'file';
            hiddenFileInput.name = 'file';
            hiddenFileInput.files = fileInput.files;
            hiddenFileInput.classList.add('hidden-input');
            form.appendChild(hiddenFileInput);

            return true;
        }
    </script>
</body>
</html>
