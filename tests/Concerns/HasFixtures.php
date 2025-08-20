<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

trait HasFixtures
{
    protected function getFixturePath(string $filename): string
    {
        return __DIR__ . '/../Fixtures/files/' . $filename;
    }

    protected function createUploadedFileFromFixture(string $filename, string $mimeType = null): UploadedFile
    {
        $path = $this->getFixturePath($filename);
        
        if (!file_exists($path)) {
            throw new \Exception("Fixture file not found: {$path}");
        }

        return new UploadedFile(
            $path,
            $filename,
            $mimeType,
            null,
            true // test mode
        );
    }

    protected function getFixtureContent(string $filename): string
    {
        $path = $this->getFixturePath($filename);
        
        if (!file_exists($path)) {
            throw new \Exception("Fixture file not found: {$path}");
        }

        return file_get_contents($path);
    }
}