<?php

namespace Beliven\Notarify;

use Illuminate\Support\Facades\Storage;
use Beliven\Notarify\Entities\FileToNotarize;
use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Symfony\Component\HttpFoundation\File\File;

class Notarify {
    public function __construct(private NotarizationServiceContract $notarizationService)
    {
    }

    public function upload(File $file) {
        return $this->handleFile($file, function ($fileToNotarize) {
            return $this->notarizationService->upload($fileToNotarize);
        });
    }

    public function verify($file) {
        return $this->handleFile($file, function ($fileToNotarize) {
            return $this->notarizationService->verify($fileToNotarize);
        });
    }

    /**
     * Handle file temporary storage, processing, and cleanup.
     *
     * @param Symfony\Component\HttpFoundation\File\File $file
     * @param \Closure $process
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleFile(File $file, \Closure $process)
    {
        $storedFilePath = $file->store('notarify/temp');

        try {
            $fileToNotarize = new FileToNotarize($storedFilePath);
            $result = $process($fileToNotarize);

            return response()->json($result);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            if (Storage::exists($storedFilePath)) {
                Storage::delete($storedFilePath);
            }
        }
    }
}
