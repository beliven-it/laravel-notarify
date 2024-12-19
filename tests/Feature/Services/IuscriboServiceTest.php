<?php

use Beliven\Notarify\Entities\Notarization;
use Beliven\Notarify\Exceptions\NotarizationAuthException;
use Beliven\Notarify\Exceptions\NotarizationUploadException;
use Beliven\Notarify\Exceptions\NotarizationVerificationException;
use Beliven\Notarify\Services\IuscriboService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\File\File;

beforeEach(function () {
    Config::set('notarify.services.iuscribo.endpoint', 'https://example.com');
    Config::set('notarify.services.iuscribo.company', 'testcompany');

    $this->service = new IuscriboService;
});

it('uploads a file successfully', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
        '*/notarization/testcompany/createandfinalize' => Http::response([
            'value' => [
                'id' => 'test-id',
                'notarizationDate' => '2024-12-11T17:05:10.5198288+01:00',
                'document' => [
                    'documentHash' => 'test-hash',
                ],
                'blockchainExplorer' => 'https://explorer.com/tx/',
                'coordinatesStep' => [
                    'transactionId' => 'test-tx',
                ],
            ],
        ], 200),
    ]);

    $file = Mockery::mock(File::class);
    $file->shouldReceive('getClientOriginalName')->andReturn('testfile.txt');
    $file->shouldReceive('getContent')->andReturn('file content');

    $notarization = $this->service->upload($file);

    expect($notarization)->toBeInstanceOf(Notarization::class);
    expect($notarization->getExplorerUrls()[0])->toBe('https://explorer.com/tx/test-tx');
    expect($notarization->getHash())->toBe('test-hash');
    expect($notarization->getTimestamp())->toEqual(Carbon::parse('2024-12-11T17:05:10.5198288+01:00'));
});

it('throws an exception when upload fails', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
        '*/notarization/testcompany/createandfinalize' => Http::response([
            'errorSummary' => 'Upload failed',
        ], 400),
    ]);

    $file = Mockery::mock(File::class);
    $file->shouldReceive('getClientOriginalName')->andReturn('testfile.txt');
    $file->shouldReceive('getContent')->andReturn('file content');

    $this->service->upload($file);
})->throws(NotarizationUploadException::class);

it('verifies a notarization successfully from a Notarization instance', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
        '*/notarization/testcompany/infobyhashcode*' => Http::response([
            'value' => [
                'id' => 'test-id',
                'notarizationDate' => '2024-12-11T17:05:10.5198288+01:00',
                'document' => [
                    'documentHash' => 'test-hash',
                ],
                'blockchainExplorer' => 'https://explorer.com/tx/',
                'coordinatesStep' => [
                    'transactionId' => 'test-tx',
                ],
            ],
        ], 200),
    ]);

    $notarization = new Notarization('test-id', 'test-hash');

    $verifiedNotarization = $this->service->verify($notarization);

    expect($verifiedNotarization)->toBeInstanceOf(Notarization::class);
    expect($verifiedNotarization->getExplorerUrls()[0])->toBe('https://explorer.com/tx/test-tx');
    expect($verifiedNotarization->getHash())->toBe('test-hash');
    expect($verifiedNotarization->getTimestamp())->toEqual(Carbon::parse('2024-12-11T17:05:10.5198288+01:00'));
});

it('verifies a notarization successfully from a file', function () {
    $file = Mockery::mock(File::class);
    $file->shouldReceive('getContent')->andReturn('file content');

    Http::fake([
        '*/auth/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
        '*/notarization/testcompany/infobyhashcode*' => Http::response([
            'value' => [
                'id' => 'test-id',
                'notarizationDate' => '2024-12-11T17:05:10.5198288+01:00',
                'document' => [
                    'documentHash' => 'test-hash',
                ],
                'blockchainExplorer' => 'https://explorer.com/tx/',
                'coordinatesStep' => [
                    'transactionId' => 'test-tx',
                ],
            ],
        ], 200),
    ]);

    $verifiedNotarization = $this->service->verify($file);

    expect($verifiedNotarization)->toBeInstanceOf(Notarization::class);
    expect($verifiedNotarization->getExplorerUrls()[0])->toBe('https://explorer.com/tx/test-tx');
    expect($verifiedNotarization->getHash())->toBe('test-hash');
    expect($verifiedNotarization->getTimestamp())->toEqual(Carbon::parse('2024-12-11T17:05:10.5198288+01:00'));
});

it('throws an exception when verification fails', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
        '*/notarization/testcompany/infobyhashcode*' => Http::response([
            'errorSummary' => 'Verification failed',
        ], 400),
    ]);

    $notarization = new Notarization('test-id', 'test-hash');

    $this->service->verify($notarization);
})->throws(NotarizationVerificationException::class);

it('throws an exception when auth token retrieval fails', function () {
    Http::fake([
        '*/auth/login' => Http::response([
            'errorSummary' => 'Auth failed',
        ], 400),
    ]);

    $notarization = new Notarization('test-id', 'test-hash');

    $this->service->verify($notarization);
})->throws(NotarizationAuthException::class);
