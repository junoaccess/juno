<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocsController extends Controller
{
    /**
     * Serve the VitePress documentation.
     *
     * This controller handles routing for the static VitePress documentation site.
     * It serves static files directly and returns the SPA index.html for all other routes
     * to enable client-side routing.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        $path = $request->path();
        $relativePath = str_replace('docs/', '', $path);
        $filePath = public_path('docs/'.$relativePath);

        // Serve static files directly if they exist
        if ('' !== $relativePath && file_exists($filePath) && is_file($filePath)) {
            return response()->file($filePath);
        }

        // Return the SPA index.html for all other routes (client-side routing)
        return response()->file(public_path('docs/index.html'));
    }
}
