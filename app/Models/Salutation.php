<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salutation extends Model
{   
    // Salutation constants
    const MR = 1;
    const MRS = 2;
    const MS = 3;
    const MISS = 4;
    const MASTER = 5;
    const SIR = 6;
    const LADY = 7;
    const DR = 8;
    const PROF = 9;
    const REV = 10;
    const HON = 11;
    const FR = 12;
    const BR = 13;
    const SR = 14;
    const MADAM = 15;
    const LORD = 16;
    const DAME = 17;
    const CAPT = 18;
    const MAJ = 19;
    const LT = 20;
    const COL = 21;
    const GEN = 22;
    const ADM = 23;
    const PRES = 24;
    const VP = 25;
    const CHANCELLOR = 26;
    const PRINCE = 27;
    const PRINCESS = 28;
    const KING = 29;
    const QUEEN = 30;

    public static function getSalutationOptions()
    {
        return [
            self::MR => 'Mr',
            self::MRS => 'Mrs',
            self::MS => 'Ms',
            self::MISS => 'Miss',
            self::MASTER => 'Master',
            self::SIR => 'Sir',
            self::LADY => 'Lady',
            self::DR => 'Dr',
            self::PROF => 'Prof',
            self::REV => 'Rev',
            self::HON => 'Hon',
            self::FR => 'Fr',
            self::BR => 'Br',
            self::SR => 'Sr',
            self::MADAM => 'Madam',
            self::LORD => 'Lord',
            self::DAME => 'Dame',
            self::CAPT => 'Capt',
            self::MAJ => 'Maj',
            self::LT => 'Lt',
            self::COL => 'Col',
            self::GEN => 'Gen',
            self::ADM => 'Adm',
            self::PRES => 'Pres',
            self::VP => 'VP',
            self::CHANCELLOR => 'Chancellor',
            self::PRINCE => 'Prince',
            self::PRINCESS => 'Princess',
            self::KING => 'King',
            self::QUEEN => 'Queen',
        ];
    }

    public static function getSalutationValueByLabel($label)
    {
        $salutationOptions = self::getSalutationOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($salutationOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }
}
