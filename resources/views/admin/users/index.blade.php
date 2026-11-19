@extends('layouts.admin')

@section('title', 'Manajemen Staf & Hak Akses')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Akun Staf (Khusus Owner)</h1>
        <p class="text-sm text-gray-500">Tambah staf baru, ubah email login, atau reset password untuk Manager dan Kasir.</p>
    </div>
    <button type="button" onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="bg-primary hover:bg-opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
        <i class="fa-solid fa-user-plus mr-1"></i> Tambah Staf Baru
    </button>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm">
    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                    <th class="py-3 px-6">Nama Staf</th>
                    <th class="py-3 px-6">Email Login</th>
                    <th class="py-3 px-6">Role / Jabatan</th>
                    <th class="py-3 px-6 text-right">Aksi (Edit Email & Password)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-6 font-semibold text-gray-800">{{ $user->name }}</td>
                    <td class="py-4 px-6 text-gray-600 font-mono text-xs">{{ $user->email }}</td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase
                            {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-700' : ($user->role === 'manager' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-primary') }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                        <button type="button"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}"
                                onclick="openEditUserModal(this)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-600 hover:text-white rounded-lg text-xs font-semibold transition">
                            <i class="fa-solid fa-key"></i> Ubah Email / Password
                        </button>

                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun staf {{ $user->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1.5 text-red-500 hover:bg-red-50 rounded-lg text-xs"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-400">Belum ada data pengguna sistem.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>

<div id="addUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Tambah Akun Staf Baru</h3>
            <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Email Login</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Password (Min. 8 Karakter)</label>
                <input type="password" name="password" required minlength="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan / Role</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="kasir">Kasir</option>
                    <option value="manager">Manager</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium">Simpan Staf</button>
            </div>
        </form>
    </div>
</div>

<div id="editUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Ubah Email & Password Staf</h3>
            <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editUserForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="editUserName" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Email Login Baru</label>
                <input type="email" id="editUserEmail" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan / Role</label>
                <select id="editUserRole" name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="kasir">Kasir</option>
                    <option value="manager">Manager</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Password Baru <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengganti password)</span></label>
                <input type="password" name="password" minlength="8" placeholder="Minimal 8 karakter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/admin-users.js') }}"></script>
@endsection
