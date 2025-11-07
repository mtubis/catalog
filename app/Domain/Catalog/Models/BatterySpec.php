<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatterySpec extends Model
{
    protected $fillable = ['product_id','capacity'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
