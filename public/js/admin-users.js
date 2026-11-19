window.openEditUserModal = function (btn) {
    document.getElementById('editUserForm').action = '/admin/users/' + btn.dataset.id;
    document.getElementById('editUserName').value = btn.dataset.name;
    document.getElementById('editUserEmail').value = btn.dataset.email;
    document.getElementById('editUserRole').value = btn.dataset.role;
    document.getElementById('editUserModal').classList.remove('hidden');
};
