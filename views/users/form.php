<?php
$pageTitle = $userModel && $userModel->id ? 'Edit User' : 'Add User';
if (!isset($user)) $user = null;
if (!isset($errors)) $errors = [];
$isEdit = $userModel && $userModel->id;
$currentPage = 'users';
require $basePath . '/layout/admin_header.php';
?>
<div class="bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-4"><?= $isEdit ? 'Edit User' : 'Add User' ?></h1>
    <form method="post" action="<?= $isEdit ? '/users/' . (int) $userModel->id . '/update' : '/users/create' ?>" class="space-y-4 max-w-md">
        <div>
            <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username *</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($userModel->username ?? '') ?>" required minlength="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            <?php if (!empty($errors['username'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password <?= $isEdit ? '(leave blank to keep)' : '*' ?></label>
            <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required minlength="6"' ?> class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            <?php if (!empty($errors['password'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role *</label>
            <select id="role" name="role" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                <option value="user" <?= ($userModel->role ?? '') === 'user' ? 'selected' : '' ?>>user</option>
                <option value="admin" <?= ($userModel->role ?? '') === 'admin' ? 'selected' : '' ?>>admin</option>
            </select>
            <?php if (!empty($errors['role'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['role']) ?></p>
            <?php endif; ?>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition"><?= $isEdit ? 'Update' : 'Create' ?></button>
            <a href="/users" class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium py-2 px-4 rounded-lg transition inline-block">Cancel</a>
        </div>
    </form>
</div>
<?php require $basePath . '/layout/admin_footer.php'; ?>
