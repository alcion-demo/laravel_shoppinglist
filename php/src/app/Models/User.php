<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ユーザー一覧検索
     * @param request $request
     * @return \App\Models\User
     */
    public function userList($request) {
        $query = $this->query();

        if (!empty($request)) {
            // 検索条件を ( ) で囲むように修正（grouping）
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request}%")
                  ->orWhere('email', 'LIKE', "%{$request}%");
            });
        }

        $users = $query->latest()->paginate(25);

        return $users;
    }
}
