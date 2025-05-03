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
 * Class ProductType
 * 
 * @property int $id
 * @property string $type_name
 * @property int $brand_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Brand $brand
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class ProductType extends Model
{
	use SoftDeletes;
	protected $table = 'product_types';

	protected $casts = [
		'brand_id' => 'int'
	];

	protected $fillable = [
		'type_name',
		'brand_id'
	];

	public function brand()
	{
		return $this->belongsTo(Brand::class);
	}

	public function products()
	{
		return $this->hasMany(Product::class, 'type_id');
	}
}
