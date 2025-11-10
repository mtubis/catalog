<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','name','manufacturer','price','description',
        'source_category','source_id'
    ];

    protected $casts = [ 'price' => 'decimal:2' ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function batterySpec(): HasOne { return $this->hasOne(BatterySpec::class); }
    public function solarPanelSpec(): HasOne { return $this->hasOne(SolarPanelSpec::class); }
    public function connectorSpec(): HasOne { return $this->hasOne(ConnectorSpec::class); }
}
