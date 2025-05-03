<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Invoice[] $invoices
 *
 * @package App\Models
 */
class User extends Model
{
	use HasFactory, Notifiable,SoftDeletes;
    use HasRoles;
	protected $table = 'users';

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


	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'remember_token'
	];

	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'employee_id');
	}

	
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole('admin') ) {
            return true;
        }
        if ($this->hasRole('user') ) {
            return true;
        }
       

        return false;
    }


    protected function getRedirectUrl(): ?string
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'getRedirectPanel')) {
            $panel = $user->getRedirectPanel();
            if ($panel) {
                return $panel->getUrl();
            }
        }

        return route('filament.admin.auth.login');
    }
}
