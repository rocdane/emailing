<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface IEmailParsingService
{
    public function parseEmailFile(UploadedFile $file): array;
}
