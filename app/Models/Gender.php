<?php
// app/Models/Gender.php
namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    // Gender chromosomes
    const MALE = 'XY';
    const FEMALE = 'XX';

    /**
     * Gender mapping: Gender names to chromosome codes
     */
    public static $GENDERS = [
        'Male' => self::MALE,
        'Female' => self::FEMALE,
    ];

    /**
     * Get a collection of all genders.
     *
     * @return Collection
     */
    public static function getGenders(): Collection
    {
        return collect(self::$GENDERS);
    }

    /**
     * Get the gender name for a given chromosome code.
     *
     * @param string $chromosome
     * @return string|null
     */
    public static function getGenderName(string $chromosome): ?string
    {
        return array_search($chromosome, self::$GENDERS, true) ?: null;
    }

    /**
     * Get gender options: Chromosome codes to gender names.
     *
     * @return array
     */
    public static function getGenderOptions(): array
    {
        return array_flip(self::$GENDERS);
    }

    /**
     * Get the chromosome code by gender label.
     *
     * @param string $label
     * @return string|null
     */
    public static function getGenderValueByLabel(string $label): ?string
    {
        return self::$GENDERS[ucfirst(strtolower($label))] ?? null;
    }
}
