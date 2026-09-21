<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index()
    {
        return view('admin.konten');
    }

    public function storeMateri(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|max:50',
        ]);

        $allowedStudents = $request->input('allowed_students', []);
        $allowedStr = is_array($allowedStudents) ? implode(',', $allowedStudents) : '';

        $filePath = null;
        $fileSize = null;

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/materi'), $fileName);
            $filePath = '/uploads/materi/' . $fileName;
            $fileSize = $file->getSize();
        }

        Material::create([
            'title' => $request->input('title'),
            'category' => $request->input('category', 'TWK'),
            'description' => $request->input('description', ''),
            'type' => $request->input('type', 'teks'),
            'content' => $request->input('content', ''),
            'allowed_students' => $allowedStr,
            'status' => $request->input('status', 'aktif'),
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'uploaded_by' => auth()->id() ?? (session('user_id') ?? 2),
        ]);

        $redirect = $request->input('redirect_to', '/admin/konten?tab=materi');
        $sep = str_contains($redirect, '?') ? '&' : '?';
        return redirect($redirect . $sep . 'success=' . urlencode('Materi berhasil ditambahkan.'));
    }

    public function updateMateri(Request $request)
    {
        $id = (int) $request->input('id');
        $materi = Material::find($id);
        $redirect = $request->input('redirect_to', '/admin/konten?tab=materi');
        $sep = str_contains($redirect, '?') ? '&' : '?';

        if (!$materi) {
            return redirect($redirect . $sep . 'error=' . urlencode('Materi tidak ditemukan.'));
        }

        $data = [
            'title' => $request->input('title', $materi->title),
            'category' => $request->input('category', $materi->category),
            'description' => $request->input('description', $materi->description),
            'type' => $request->input('type', $materi->type),
            'content' => $request->input('content', $materi->content),
        ];

        if ($request->has('allowed_students')) {
            $allowed = $request->input('allowed_students', []);
            $data['allowed_students'] = is_array($allowed) ? implode(',', $allowed) : '';
        }

        $materi->update($data);

        return redirect($redirect . $sep . 'success=' . urlencode('Materi berhasil diperbarui.'));
    }

    public function toggleMateri(Request $request)
    {
        $id = (int) $request->input('id');
        $materi = Material::find($id);
        $redirect = $request->input('redirect_to', '/admin/konten?tab=materi');
        $sep = str_contains($redirect, '?') ? '&' : '?';

        if (!$materi) {
            return redirect($redirect . $sep . 'error=' . urlencode('Materi tidak ditemukan.'));
        }

        $newStatus = ($materi->status === 'aktif') ? 'nonaktif' : 'aktif';
        $materi->update(['status' => $newStatus]);

        return redirect($redirect . $sep . 'success=' . urlencode('Status materi diubah menjadi ' . $newStatus . '.'));
    }

    public function deleteMateri(Request $request)
    {
        $id = (int) $request->input('id');
        $materi = Material::find($id);
        $redirect = $request->input('redirect_to', '/admin/konten?tab=materi');
        $sep = str_contains($redirect, '?') ? '&' : '?';

        if ($materi) {
            $materi->delete();
            return redirect($redirect . $sep . 'success=' . urlencode('Materi berhasil dihapus.'));
        }
        return redirect($redirect . $sep . 'error=' . urlencode('Materi tidak ditemukan.'));
    }

    public function storeCat(Request $request)
    {
        $request->validate([
            'exam_name' => 'required|string|max:150',
        ]);

        $allowedStudents = $request->input('allowed_students', []);
        $allowedStr = is_array($allowedStudents) ? implode(',', $allowedStudents) : '';

        ExamSession::create([
            'exam_name' => $request->input('exam_name'),
            'category' => $request->input('category', 'Umum'),
            'description' => $request->input('description', ''),
            'duration_minutes' => (int) $request->input('duration_minutes', 60),
            'total_questions' => (int) $request->input('total_questions', 0),
            'passing_score' => (int) $request->input('passing_score', 70),
            'show_discussion' => $request->has('show_discussion') ? 1 : 0,
            'allowed_students' => $allowedStr,
            'status' => $request->input('status', 'nonaktif'),
        ]);

        $redirect = $request->input('redirect_to', '/admin/konten?tab=cat');
        $sep = str_contains($redirect, '?') ? '&' : '?';
        return redirect($redirect . $sep . 'success=' . urlencode('Simulasi CAT berhasil dibuat.'));
    }

    public function updateCat(Request $request)
    {
        $id = (int) $request->input('id');
        $cat = ExamSession::find($id);
        $redirect = $request->input('redirect_to', '/admin/konten?tab=cat');
        $sep = str_contains($redirect, '?') ? '&' : '?';

        if (!$cat) {
            return redirect($redirect . $sep . 'error=' . urlencode('Simulasi CAT tidak ditemukan.'));
        }

        $data = [
            'exam_name' => $request->input('exam_name', $cat->exam_name),
            'category' => $request->input('category', $cat->category),
            'description' => $request->input('description', $cat->description),
            'duration_minutes' => (int) $request->input('duration_minutes', $cat->duration_minutes),
            'show_discussion' => $request->has('show_discussion') ? 1 : 0,
        ];

        if ($request->has('allowed_students')) {
            $allowed = $request->input('allowed_students', []);
            $data['allowed_students'] = is_array($allowed) ? implode(',', $allowed) : '';
        }

        $cat->update($data);

        return redirect($redirect . $sep . 'success=' . urlencode('Simulasi CAT berhasil diperbarui.'));
    }

    public function toggleCat(Request $request)
    {
        $id = (int) $request->input('id');
        $cat = ExamSession::find($id);
        $redirect = $request->input('redirect_to', '/admin/konten?tab=cat');
        $sep = str_contains($redirect, '?') ? '&' : '?';

        if (!$cat) {
            return redirect($redirect . $sep . 'error=' . urlencode('Simulasi CAT tidak ditemukan.'));
        }

        $newStatus = ($cat->status === 'aktif') ? 'nonaktif' : 'aktif';
        $cat->update(['status' => $newStatus]);

        return redirect($redirect . $sep . 'success=' . urlencode('Status CAT diubah menjadi ' . $newStatus . '.'));
    }

    public function deleteCat(Request $request)
    {
        $id = (int) $request->input('id');
        $cat = ExamSession::find($id);
        $redirect = $request->input('redirect_to', '/admin/konten?tab=cat');
        $sep = str_contains($redirect, '?') ? '&' : '?';

        if ($cat) {
            $cat->delete();
            return redirect($redirect . $sep . 'success=' . urlencode('Simulasi CAT berhasil dihapus.'));
        }
        return redirect($redirect . $sep . 'error=' . urlencode('Simulasi CAT tidak ditemukan.'));
    }

    public function updatePermissions(Request $request)
    {
        $type = $request->input('type'); // 'materi' or 'cat'
        $id = (int) $request->input('id');
        $allowed = $request->input('allowed_students', []);
        $allowedStr = is_array($allowed) ? implode(',', $allowed) : '';

        $redirectTo = $request->input('redirect_to', '/admin/konten');
        $sep = str_contains($redirectTo, '?') ? '&' : '?';

        if ($type === 'materi') {
            $item = Material::find($id);
            if ($item) {
                $item->update(['allowed_students' => $allowedStr]);
                return redirect($redirectTo . $sep . 'tab=materi&success=' . urlencode('Izin siswa untuk materi berhasil diperbarui.'));
            }
        } elseif ($type === 'cat') {
            $item = ExamSession::find($id);
            if ($item) {
                $item->update(['allowed_students' => $allowedStr]);
                return redirect($redirectTo . $sep . 'tab=cat&success=' . urlencode('Izin siswa untuk simulasi CAT berhasil diperbarui.'));
            }
        }

        return redirect($redirectTo . $sep . 'error=' . urlencode('Gagal memperbarui izin siswa.'));
    }
}
