<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InvoiceProduct
 * 
 * @property int $id
 * @property int $invoice_id
 * @property int $product_id
 * @property int $quantity
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Invoice $invoice
 * @property Product $product
 *
 * @package App\Models
 */
class InvoiceProduct extends Model
{
	use SoftDeletes;
	protected $table = 'invoice_products';

	protected $casts = [
		'invoice_id' => 'int',
		'product_id' => 'int',
		'quantity' => 'int'
	];

	protected $fillable = [
		'invoice_id',
		'product_id',
		'quantity'
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var list<string>
	 */
	
	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}
	
	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
