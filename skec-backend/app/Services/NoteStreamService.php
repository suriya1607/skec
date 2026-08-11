<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoteStreamService
{
    /**
     * Secure encrypted PDF streaming for authenticated students.
     * Encrypts PDF bytes using AES-256-CBC with a key derived from (sessionToken + user.id + note.id).
     * Returns Content-Type: application/octet-stream so DevTools / browsers see non-PDF binary data.
     */
    public function streamNote(Note $note, User $user, string $sessionToken): StreamedResponse
    {
        $filePath = $note->file_path;

        abort_unless(Storage::disk('local')->exists($filePath), 404, 'File not found.');

        $fileContent = Storage::disk('local')->get($filePath);

        // Derive 256-bit AES encryption key unique to (sessionToken + user.id + note.id)
        $secretString = $sessionToken . ':' . $user->id . ':' . $note->id;
        $key = hash('sha256', $secretString, true); // 32 raw bytes

        // Generate 16-byte random IV
        $iv = random_bytes(16);

        // Encrypt raw PDF content
        $encryptedData = openssl_encrypt($fileContent, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // Prepend 16-byte IV to encrypted ciphertext payload
        $payload = $iv . $encryptedData;

        return response()->stream(function () use ($payload) {
            echo $payload;
        }, 200, [
            'Content-Type'           => 'application/octet-stream',
            'Content-Length'         => strlen($payload),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'no-store, no-cache, must-revalidate, private',
            'Pragma'                 => 'no-cache',
            'X-Frame-Options'        => 'DENY',
        ]);
    }

    /**
     * Public stream for free notes — served inline for browser PDF viewer, no watermark.
     */
    public function streamNoteFree(Note $note): StreamedResponse
    {
        $filePath = $note->file_path;

        abort_unless(Storage::disk('local')->exists($filePath), 404, 'File not found.');

        $fileContent = Storage::disk('local')->get($filePath);
        $fileSize = Storage::disk('local')->size($filePath);

        return response()->stream(function () use ($fileContent) {
            echo $fileContent;
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Length'      => $fileSize,
            'Content-Disposition' => 'inline; filename="' . $note->file_name . '"',
            'Cache-Control'       => 'public, max-age=300',
            'X-Frame-Options'     => 'SAMEORIGIN',
        ]);
    }
}
