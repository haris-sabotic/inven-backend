<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    public function availablePositionsLeft()
    {
        $applications = Application::where('ad_id', $this->id)->get();
        return $this->max_available_positions - $applications->count();
    }
}
