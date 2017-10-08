<?php
/*
    TUSSO: Central Authentication Service Provider
    Copyright (C) 2017 Siwat Techavoranant

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
    
    public static function isFailOver(int $threshold) {
        return self::countFailure() > $threshold;
    }
    
    public static function add(string $username, string $ip) {
        return self::create(['username' => $username, 'ip' => $ip]);
    }
    
    public static function captchaNeeded() {
        return self::isFailOver(8);
    }
    
    public static function ipAndUsernameFrequent(string $ip = '', string $username = '') {
        return self::where('ip', $ip)->where('username', $username)->whereBetween('updated_at', [Carbon::now()->subMinutes(self::$attemptCount), Carbon::now()])->count() > 3;
    }
}
