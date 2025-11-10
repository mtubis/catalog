<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectorSpec extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','connector_type'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
