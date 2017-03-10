<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FailedLogin extends Model {
    protected $table = 'failed_logins';
    
    protected $fillable = ['username', 'ip'];
    
    /**
     * Minutes to count failures
     *
     * @var int
     */
    protected static $attemptCount = 15;
    
    /**
     * Count login failure
     */
    public static function countFailure() {
        $count = self::whereBetween('updated_at', [Carbon::now()->subMinutes(self::$attemptCount), Carbon::now()])->count();
        if ($count <= 0 AND rand(1, 3) == 3) {
            self::truncate();
        } elseif (rand(0, 9) == 9) {
            // Randomly clean the table
            self::whereBetween('updated_at', [Carbon::createFromTimestamp(0), Carbon::now()->subMinutes(120)])->delete();
        }
        
        return $count;
    }
    
    public static function isFailOver (int $threshold) {
        return self::countFailure() > $threshold;
    }
    
    public static function add(string $username, string $ip) {
        return self::create(['username' => $username, 'ip' => $ip]);
    }
    
    public static function captchaNeeded() {
        return self::isFailOver(8);
    }
    
    public static function ipAndUsernameFrequent (string $ip = '', string $username = '') {
        return self::where('ip', $ip)->where('username', $username)->whereBetween('updated_at', [Carbon::now()->subMinutes(self::$attemptCount), Carbon::now()])->count() > 3;
    }
}
