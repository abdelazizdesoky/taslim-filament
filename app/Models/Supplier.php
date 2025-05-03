<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Supplier
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property bool $status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Invoice[] $invoices
 *
 * @package App\Models
 */
class Supplier extends Model
{
	use SoftDeletes;
	protected $table = 'suppliers';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'address',
		'phone',
		'status'
	];

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}
}
