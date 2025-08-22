<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Kunjungans yang dibuat oleh user ini
     */
    public function kunjungansCreated()
    {
        return $this->hasMany(Kunjungan::class, 'created_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope untuk user yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check apakah user adalah dokter
     */
    public function isDokter()
    {
        return $this->role === 'dokter';
    }

    /**
     * Check apakah user adalah petugas pendaftaran
     */
    public function isPendaftaran()
    {
        return $this->role === 'pendaftaran';
    }

    /**
     * Get formatted role name
     */
    public function getRoleTextAttribute()
    {
        $roles = [
            'admin' => 'Administrator',
            'dokter' => 'Dokter',
            'pendaftaran' => 'Petugas Pendaftaran'
        ];

        return $roles[$this->role] ?? $this->role;
    }

    /**
     * Check if user can access specific feature
     */
    public function canAccess($feature)
    {
        $permissions = [
            'admin' => ['*'], // admin can access everything
            'dokter' => [
                'kunjungan.show',
                'kunjungan.update',
                'tindakan.*',
                'diagnosa.*',
                'pasien.show'
            ],
            'pendaftaran' => [
                'pasien.*',
                'kunjungan.*',
                'poli.show',
                'dokter.show',
                'jadwal_dokter.show'
            ]
        ];

        if ($this->role === 'admin') {
            return true;
        }

        $userPermissions = $permissions[$this->role] ?? [];

        foreach ($userPermissions as $permission) {
            if ($permission === '*' || $permission === $feature) {
                return true;
            }

            // Check wildcard permissions (e.g., 'tindakan.*')
            if (strpos($permission, '.*') !== false) {
                $prefix = str_replace('.*', '', $permission);
                if (strpos($feature, $prefix) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    // ============================================
    // API TOKEN METHODS
    // ============================================

    /**
     * Generate new API token
     *
     * @return string
     */
    public function generateApiToken()
    {
        $this->api_token = Str::random(80);
        $this->save();

        return $this->api_token;
    }

    /**
     * Revoke API token
     *
     * @return void
     */
    public function revokeApiToken()
    {
        $this->api_token = null;
        $this->save();
    }

    /**
     * Check if user has valid API token
     *
     * @return bool
     */
    public function hasValidApiToken()
    {
        return !empty($this->api_token);
    }

    /**
     * Regenerate API token (untuk security)
     *
     * @return string
     */
    public function regenerateApiToken()
    {
        return $this->generateApiToken();
    }
}
