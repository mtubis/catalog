<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolarPanelSpec extends Model
{
    protected $fillable = ['product_id','power_output'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
