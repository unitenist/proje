<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Support\SiteContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminContentController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'sections' => SiteContent::sections(),
            'unreadMessages' => ContactMessage::query()->whereNull('read_at')->count(),
        ]);
    }

    public function edit(string $section): Response
    {
        $this->assertValidSection($section);

        return Inertia::render('Admin/EditSection', [
            'section' => $section,
            'label' => SiteContent::sections()[$section],
            'content' => SiteContent::get($section),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        $this->assertValidSection($section);

        $data = $request->input('content', []);

        if (! is_array($data)) {
            throw ValidationException::withMessages(['content' => 'Geçersiz içerik.']);
        }

        SiteContent::set($section, $data);

        return redirect()
            ->route('admin.content.edit', $section)
            ->with('success', 'İçerik güncellendi.');
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('image')->store('uploads', 'public');

        return response()->json(['url' => url('/media/'.$path)]);
    }

    protected function assertValidSection(string $section): void
    {
        if (! array_key_exists($section, SiteContent::sections())) {
            abort(404);
        }
    }
}
