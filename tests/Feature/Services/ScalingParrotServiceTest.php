<?php

use Beliven\Notarify\Entities\Notarization;
use Beliven\Notarify\Exceptions\NotarizationAuthException;
use Beliven\Notarify\Exceptions\NotarizationUploadException;
use Beliven\Notarify\Exceptions\NotarizationVerificationException;
use Beliven\Notarify\Services\ScalingParrotsService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\File\File;

beforeEach(function () {
    Config::set('notarify.services.scalingparrots.endpoint', 'https://example.com');

    $this->service = new ScalingParrotsService;
});

it('uploads a file successfully', function () {
    Http::fake([
        '*/timestamp/stampHash' => Http::response([
            [
                'txId' => '12345',
                'hash' => 'abcde',
                'explorerUrl' => 'https://explorer.com/tx/12345',
            ],
        ], 200),
        '*/user/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
    ]);

    $file = Mockery::mock(File::class);
    $file->shouldReceive('getContent')->andReturn('file content');

    $notarization = $this->service->upload($file);

    expect($notarization)->toBeInstanceOf(Notarization::class);
    expect($notarization->getId())->toBe('12345');
    expect($notarization->getExplorerUrls()[0])->toBe('https://explorer.com/tx/12345');
    expect($notarization->getHash())->toBe('abcde');
});

it('throws an exception when upload fails', function () {
    Http::fake([
        '*/timestamp/stampHash' => Http::response([
            ['error' => 'Upload failed'],
        ], 400),
        '*/user/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
    ]);

    $file = Mockery::mock(File::class);
    $file->shouldReceive('getContent')->andReturn('file content');

    $this->service->upload($file);
})->throws(NotarizationUploadException::class);

it('verifies a notarization successfully from a Notarization instance', function () {
    Http::fake([
        '*/timestamp/checkHash/*' => Http::response([
            [
                'txId' => '12345',
                'hash' => 'abcde',
                'timestamp' => 1609459200,
                'explorerUrl' => 'https://explorer.com/tx/12345',
            ],
        ], 200),
        '*/user/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
    ]);

    $notarization = new Notarization('12345', 'abcde');

    $verifiedNotarization = $this->service->verify($notarization);

    expect($verifiedNotarization)->toBeInstanceOf(Notarization::class);
    expect($verifiedNotarization->getId())->toBe('12345');
    expect($verifiedNotarization->getHash())->toBe('abcde');
    expect($verifiedNotarization->getExplorerUrls()[0])->toBe('https://explorer.com/tx/12345');
    expect($verifiedNotarization->getTimestamp())->toEqual(Carbon::createFromTimestampUTC(1609459200));
});

it('verifies a notarization successfully from a file', function () {
    $file = Mockery::mock(File::class);
    $file->shouldReceive('getContent')->andReturn('file content');

    Http::fake([
        '*/timestamp/checkHash/*' => Http::response([
            [
                'txId' => '12345',
                'hash' => 'abcde',
                'timestamp' => 1609459200,
                'explorerUrl' => 'https://explorer.com/tx/12345',
            ],
        ], 200),
        '*/user/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
    ]);

    $notarization = $this->service->verify($file);

    expect($notarization)->toBeInstanceOf(Notarization::class);
    expect($notarization->getId())->toBe('12345');
    expect($notarization->getHash())->toBe('abcde');
    expect($notarization->getExplorerUrls()[0])->toBe('https://explorer.com/tx/12345');
    expect($notarization->getTimestamp())->toEqual(Carbon::createFromTimestampUTC(1609459200));
});

it('throws an exception when verification fails', function () {
    Http::fake([
        '*/timestamp/checkHash/*' => Http::response([
            ['error' => 'Verification failed'],
        ], 400),
        '*/user/login' => Http::response([
            'token' => 'auth-token',
        ], 200),
    ]);

    $notarization = new Notarization('12345', 'abcde');

    $this->service->verify($notarization);
})->throws(NotarizationVerificationException::class);

it('throws an exception when auth token retrieval fails', function () {
    Http::fake([
        '*/user/login' => Http::response([
            ['error' => 'Auth failed'],
        ], 400),
    ]);

    $notarization = new Notarization('12345', 'abcde');

    $this->service->verify($notarization);
})->throws(NotarizationAuthException::class);
