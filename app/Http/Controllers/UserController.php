<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Departemen;
use App\Models\LokasiUnit;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): View
    {
        $users = $this->userService->getAll();
        $roles = Role::orderBy('id')->get();
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $lokasis = LokasiUnit::orderBy('nama_lokasi')->get();

        return view('admin.user.index', compact('users', 'roles', 'departemens', 'lokasis'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->route('users.index')->with('success', __('User berhasil ditambahkan!'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', __('Data user berhasil diperbarui!'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', __('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.'));
        }

        $this->userService->delete($user);

        return redirect()->route('users.index')->with('success', __('Data user berhasil dihapus!'));
    }
}