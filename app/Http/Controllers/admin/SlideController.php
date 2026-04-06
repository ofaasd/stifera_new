<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterSlide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index()
    {
        $data['title'] = 'Slideshow';
        $data['CurrentPage'] = 'content';
        $data['slideList'] = MasterSlide::orderByDesc('id')->get();

        return view('admin.slide.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Slide';
        $data['CurrentPage'] = 'content';

        return view('admin.slide.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gambar' => 'required|string|max:255',
            'caption' => 'required|string',
            'link'   => 'nullable|string|max:255',
        ]);

        MasterSlide::create([
            'gambar'  => trim($validated['gambar']),
            'caption' => $validated['caption'],
            'link'    => trim($validated['link'] ?? ''),
        ]);

        return redirect()->route('slide.index')->with('status', 'Slide berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $slide = MasterSlide::find($id);
        if (!$slide) {
            return redirect()->route('slide.index')->with('error', 'Data slide tidak ditemukan.');
        }

        $data['title'] = 'Edit Slide';
        $data['CurrentPage'] = 'content';
        $data['d'] = $slide;

        return view('admin.slide.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $slide = MasterSlide::find($id);
        if (!$slide) {
            return redirect()->route('slide.index')->with('error', 'Data slide tidak ditemukan.');
        }

        $validated = $request->validate([
            'gambar' => 'required|string|max:255',
            'caption' => 'required|string',
            'link'   => 'nullable|string|max:255',
        ]);

        $slide->update([
            'gambar'  => trim($validated['gambar']),
            'caption' => $validated['caption'],
            'link'    => trim($validated['link'] ?? ''),
        ]);

        return redirect()->route('slide.index')->with('status', 'Slide berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $slide = MasterSlide::find($id);
        if (!$slide) {
            return redirect()->route('slide.index')->with('error', 'Data slide tidak ditemukan.');
        }

        $slide->delete();

        return redirect()->route('slide.index')->with('status', 'Slide berhasil dihapus.');
    }
}
