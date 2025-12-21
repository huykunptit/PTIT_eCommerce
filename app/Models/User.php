<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'photo',
        'avatar',
        'role_id',
        'address',
        'status',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function getRole()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(ShoppingCart::class);
    }

    /**
     * Kiểm tra quyền của nhân viên
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions;
        // Nếu chưa cấu hình quyền, mặc định không chặn (giữ hành vi cũ)
        if ($permissions === null) {
            return true;
        }
        return in_array($permission, $permissions, true);
    }
}
