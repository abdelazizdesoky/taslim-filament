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
 * Class Product
 * 
 * @property int $id
 * @property string $product_name
 * @property string $detail_name
 * @property string $product_code
 * @property int $type_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ProductType $product_type
 * @property Collection|Invoice[] $invoices
 *
 * @package App\Models
 */
class Product extends Model
{
	use SoftDeletes;
	protected $table = 'products';

	protected $casts = [
		'type_id' => 'int'
	];

	protected $fillable = [
		'product_name',
		'detail_name',
		'product_code',
		'type_id'
	];

	public function product_type()
	{
		return $this->belongsTo(ProductType::class, 'type_id');
	}

	public function invoices()
	{
		return $this->belongsToMany(Invoice::class, 'invoice_products')
					->withPivot('id', 'quantity', 'deleted_at')
					->withTimestamps();
	}
}
