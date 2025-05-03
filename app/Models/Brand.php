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
 * Class Brand
 * 
 * @property int $id
 * @property string $brand_name
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ProductType[] $product_types
 *
 * @package App\Models
 */
class Brand extends Model
{
	use SoftDeletes;
	protected $table = 'brands';

	protected $fillable = [
		'brand_name'
	];

	public function product_types()
	{
		return $this->hasMany(ProductType::class);
	}
}
