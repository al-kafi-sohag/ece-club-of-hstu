<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory,SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVE = -1;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'variables',
        'blade_template',
        'status'
    ];

    protected $casts = [
        'variables' => 'array',
        'status' => 'integer'
    ];

    public $appends = [
        'status_text',
        'status_class',
        'status_update_text'
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DEACTIVE);
    }

    public function getStatusTextAttribute()
    {
        return $this->status == self::STATUS_ACTIVE ? 'Active' : 'Deactive';
    }
    public function getStatusClassAttribute()
    {
        return $this->status == self::STATUS_ACTIVE ? 'success' : 'danger';
    }
    public function getStatusUpdateTextAttribute()
    {
        return $this->status == self::STATUS_ACTIVE ? 'Deactivate' : 'Activate';
    }
    public function getVariablesListAttribute()
    {
        return $this->variables ?? [];
    }

    public static function getAvailableBladeTemplates()
    {
        return [
            'backend.email.default' => 'Default Email Template',
        ];
    }

    public function getVariablesAttribute($value) {
        return json_decode($value, true) ?? [];
    }
}
