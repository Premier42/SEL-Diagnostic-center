<?php

namespace App\Models;

use App\Core\Database\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['username', 'password', 'role', 'email', 'full_name'];
    protected array $guarded = ['id', 'created_at', 'updated_at'];

    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->findBy('username', $username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }

    public function createUser(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->create($data);
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    public function isAdmin(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'admin';
    }

    public function isStaff(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'staff';
    }

    public function getUsersByRole(string $role): array
    {
        return $this->all(['role' => $role], 'username ASC');
    }
}
