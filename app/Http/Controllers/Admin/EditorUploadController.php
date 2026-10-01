<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Receives images dropped or pasted into a CKEditor body field.
 *
 * CKEditor's SimpleUploadAdapter defines the contract on both ends: the file
 * arrives as `upload`, a success is `{"url": "..."}` and a failure is
 * `{"error": {"message": "..."}}` — which is why validation is handled by hand
 * here instead of with $request->validate(), whose 422 body the adapter cannot
 * read and would surface to the author as a generic "Couldn't upload file".
 *
 * The route carries the `can:media.manage` middleware; there is no second
 * check in here, so the gate lives in exactly one place.
 *
 * These files are written straight to the public disk and are deliberately NOT
 * given a `media` row: that table's owner is a non-nullable morph, and a body
 * image is uploaded before a brand-new post has an id to own it. The Media
 * section (still unbuilt) is where they get adopted into the library; until
 * then an image removed from a body leaves its file behind.
 */
class EditorUploadController extends Controller
{
    /** Matches the post's own main-image limit. */
    private const MAX_KILOBYTES = 4096;

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:'.self::MAX_KILOBYTES],
        ], [
            'upload.max' => 'Images must be under '.(self::MAX_KILOBYTES / 1024).' MB.',
            'upload.mimes' => 'Use a JPG, PNG, WebP or GIF.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => ['message' => $validator->errors()->first('upload')],
            ], 422);
        }

        // Dated folders keep a directory listing usable years in; the filename
        // itself is hashed by store(), so an upload can never overwrite another.
        $path = $request->file('upload')->store('editor/'.now()->format('Y/m'), 'public');
        $url = Storage::disk('public')->url($path);

        /*
         * Returned root-relative, not absolute. The URL is written into the post
         * body and stored, so it outlives the host it was authored on: an
         * absolute one carries whatever APP_URL happened to be — localhost, a
         * staging domain — into production HTML and breaks every image. The
         * sanitiser allows relative media for exactly this reason.
         */
        return response()->json([
            'url' => parse_url($url, PHP_URL_PATH) ?: $url,
        ]);
    }
}
