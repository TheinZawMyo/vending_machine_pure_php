<?php
$pageTitle = 'Users';
if (!isset($user)) $user = null;
$currentPage = 'users';
require $basePath . '/layout/admin_header.php';
?>
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-slate-800">Users</h1>
        <a href="/users/create" class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition">Add User</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">ID</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Username</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Role</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 px-4 text-slate-700"><?= (int) $u->id ?></td>
                    <td class="py-3 px-4 font-medium text-slate-800"><?= htmlspecialchars($u->username) ?></td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-sm <?= $u->role === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' ?>"><?= htmlspecialchars($u->role) ?></span></td>
                    <td class="py-3 px-4">
                        <a href="/users/<?= (int) $u->id ?>/edit" class="text-amber-600 hover:underline mr-2">Edit</a>
                        <a href="/users/<?= (int) $u->id ?>/delete" onclick="return confirm('Delete this user?')" class="text-red-600 hover:underline">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require $basePath . '/layout/admin_footer.php'; ?>
