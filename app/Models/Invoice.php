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
 * Class Invoice
 * 
 * @property int $id
 * @property string $code
 * @property int $invoice_type
 * @property Carbon $invoice_date
 * @property int $location_id
 * @property int $employee_id
 * @property int|null $supplier_id
 * @property int|null $customer_id
 * @property int|null $created_by
 * @property int $invoice_status
 * @property string|null $notes
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Customer|null $customer
 * @property Location $location
 * @property Supplier|null $supplier
 * @property Collection|Product[] $products
 * @property Collection|SerialNumber[] $serial_numbers
 *
 * @package App\Models
 */
class Invoice extends Model
{
	use SoftDeletes;
	protected $table = 'invoices';

	protected $casts = [
		'invoice_type' => 'int',
		'invoice_date' => 'datetime',
		'location_id' => 'int',
		'employee_id' => 'int',
		'supplier_id' => 'int',
		'customer_id' => 'int',
		'created_by' => 'int',
		'invoice_status' => 'int'
	];

	protected $fillable = [
		'code',
		'invoice_type',
		'invoice_date',
		'location_id',
		'employee_id',
		'supplier_id',
		'customer_id',
		'created_by',
		'invoice_status',
		'notes'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'employee_id');
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	public function invoice_products()
	{
		return $this->hasMany(InvoiceProduct::class);
	}
	
	public function serial_numbers()
	{
		return $this->hasMany(SerialNumber::class);
	}

	public function createdBy()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function getInvoiceTypeNameAttribute(): string
	{
		return [
			1 => 'مرتجع',
			2 => 'استلام',
			3 => 'تسليم',
		][$this->invoice_type] ?? 'غير معروف';
	}
	
	// public function getInvoiceStatusAttribute($value)
	// {
	// 	return [
	// 		1 => 'مفتوح',
	// 		2 => 'مغلق',
	// 	][$value] ?? 'غير معروف';
	// }
}
