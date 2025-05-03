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
 * Class Location
 * 
 * @property int $id
 * @property string $location_name
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Invoice[] $invoices
 *
 * @package App\Models
 */
class Location extends Model
{
	use SoftDeletes;
	protected $table = 'locations';

	protected $fillable = [
		'location_name'
	];

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}
}
