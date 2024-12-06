<?php

namespace Beliven\Notarify;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Entities\FileToNotarize;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class Notarify
{
    public function __construct(private NotarizationServiceContract $notarizationService) {}

    public function upload(File $file)
    {
        return $this->handleFile($file, function ($fileToNotarize) {
            return $this->notarizationService->upload($fileToNotarize);
        });
    }

    public function verify($file)
    {
        return $this->handleFile($file, function ($fileToNotarize) {
            return $this->notarizationService->verify($fileToNotarize);
        });
    }

    /**
     * Handle file temporary storage, processing, and cleanup.
     *
     * @param  Symfony\Component\HttpFoundation\File\File  $file
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
