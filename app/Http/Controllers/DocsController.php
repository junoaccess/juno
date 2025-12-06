<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DocsController extends Controller
{
    /**
     * Serve the VitePress documentation site.
     */
    public function __invoke(Request $request): Response|BinaryFileResponse
    {
        $path = $request->path();
        $docsPath = public_path('docs');

        // Remove 'docs/' prefix from path
        $relativePath = str_replace('docs/', '', $path);

        // Default to index.html for directory requests
        if (empty($relativePath) || $relativePath === 'docs') {
            $relativePath = 'index.html';
        }

        // Append .html if no extension
        if (! str_contains($relativePath, '.')) {
            $relativePath .= '.html';
        }

        $filePath = $docsPath.'/'.$relativePath;

        // Security: prevent directory traversal
        $realPath = realpath($filePath);
        $realDocsPath = realpath($docsPath);

        if (! $realPath || ! str_starts_with($realPath, $realDocsPath)) {
            abort(404);
        }

        if (! File::exists($realPath)) {
            // Try index.html in directory
            $indexPath = $docsPath.'/'.$relativePath.'/index.html';
            if (File::exists($indexPath)) {
                return response()->file($indexPath);
            }

            abort(404);
        }

        return response()->file($realPath);
    }
}
