<x-mail-layout
    :title="$note->title"
    :appName="$appName"
>

<h2 style="color:#1A3C6E;margin-bottom:15px;">
    New Note Available
</h2>

<p style="margin-bottom:20px;line-height:1.7;">
    A new note has been uploaded to {{ $appName }}.
</p>

<div class="panel">

    <h3>{{ $note->title }}</h3>

    @if($note->description)
        <p>{{ $note->description }}</p>
    @endif

    <p>
        <strong>Subject:</strong>
        {{ $note->subject->name ?? 'General' }}
    </p>

    <p>
        <strong>Uploaded by:</strong>
        {{ $note->uploader->name }}
    </p>

</div>

</x-mail-layout>