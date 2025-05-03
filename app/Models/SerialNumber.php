<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SerialNumber
 * 
 * @property int $id
 * @property string $serial_number
 * @property int $invoice_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Invoice $invoice
 *
 * @package App\Models
 */
class SerialNumber extends Model
{
	use SoftDeletes;
	protected $table = 'serial_numbers';

	protected $casts = [
		'invoice_id' => 'int'
	];

	protected $fillable = [
		'serial_number',
		'invoice_id'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}
}
