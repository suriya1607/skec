<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoteStreamService
{
    public function generateStreamToken(Note $note, User $user): string
    {
        return URL::temporarySignedRoute(
            'notes.stream',
            now()->addMinutes(30),
            ['id' => $note->id, 'user' => $user->id]
        );
    }

    public function streamNote(Note $note, User $user): StreamedResponse
    {
        $filePath = $note->file_path;

        abort_unless(Storage::disk('local')->exists($filePath), 404, 'File not found.');

        $fileContent = Storage::disk('local')->get($filePath);
        $fileSize = Storage::disk('local')->size($filePath);

        return response()->stream(function () use ($fileContent) {
            echo $fileContent;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline; filename="' . $note->file_name . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }
}
