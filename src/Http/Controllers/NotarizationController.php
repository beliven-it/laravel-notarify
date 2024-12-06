<?php

namespace Beliven\Notarify\Http\Controllers;

use Beliven\Notarify\Facades\Notarify;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class NotarizationController extends Controller
{
    /**
     * Display the notarization test form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('notarify::test-form');
    }

    /**
     * Handle file upload for notarization.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $file = $this->validateAndGetFile($request);

        if (! $file) {
            return $this->invalidFileResponse();
        }

        return Notarify::upload($file);
    }

    /**
     * Verify a notarized file.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $file = $this->validateAndGetFile($request);

        if (! $file) {
            return $this->invalidFileResponse();
        }

        return Notarify::verify($file);
    }

    /**
     * Validate the uploaded file and return it.
     */
    private function validateAndGetFile(Request $request): ?UploadedFile
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            return $request->file('file');
        }

        return null;
    }

    /**
     * Return a standardized response for invalid file uploads.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function invalidFileResponse()
    {
        return response()->json(['error' => 'Invalid file upload'], 400);
    }
}
