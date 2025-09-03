<?php
// app/Models/SpecialInterestGroup.php
namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class SpecialInterestGroup extends Model
{
    // Special Interest Groups
    const PERSONS_WITH_DISABILITY = 'Persons With Disabilty';
    const YOUTH = 'Youth';
    const WOMEN = 'Women';
    const PERSONS_MARGINALIZED = 'Persons Marginalized';
    const PERSONS_MINORITY = 'Persons Minority';

    /**
     * Special Interest Group mapping: Special Interest Group names to group codes
     */
    public static $GROUPS = [
        'Persons With Disabilty' => self::PERSONS_WITH_DISABILITY,
        'Youth' => self::YOUTH,
        'Women' => self::WOMEN,
        'Persons Marginalized' => self::PERSONS_MARGINALIZED,
        'Persons Minority' => self::PERSONS_MINORITY,
    ];

    /**
     * Get a collection of all special interest groups.
     *
     * @return Collection
     */
    public static function getSpecialInterestGroups(): Collection
    {
        return collect(self::$GROUPS);
    }

    /**
     * Get the special interest group name for a given group code.
     *
     * @param string $code
     * @return string|null
     */
    public static function getSpecialInterestGroupName(string $code): ?string
    {
        return array_search($code, self::$GROUPS, true) ?: null;
    }

    /**
     * Get special interest group options: Group codes to group names.
     *
     * @return array
     */
    public static function getSpecialInterestGroupOptions(): array
    {
        return array_flip(self::$GROUPS);
    }

    /**
     * Get the group code by group label.
     *
     * @param string $label
     * @return string|null
     */
    public static function getSpecialInterestGroupValueByLabel(string $label): ?string
    {
        return self::$GROUPS[ucfirst(strtolower($label))] ?? null;
    }
}
