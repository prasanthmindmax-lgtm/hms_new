<?php

namespace App\Support;

/**
 * Canonical Mocdoc API entitylocation keys (locationN) ↔ display names.
 * Keep in sync with SuperAdminController::cityArray() — single source via this class.
 */
final class MocdocLocationKeys
{
    /**
     * @return array<string, string> location_key => branch label
     */
    public static function locationKeyToNameMap(): array
    {
        return [
            'location1' => 'Kerala - Palakkad',
            'location7' => 'Erode',
            'location14' => 'Tiruppur',
            'location6' => 'Kerala - Kozhikode',
            'location20' => 'Coimbatore - Ganapathy',
            'location21' => 'Hosur',
            'location22' => 'Chennai - Sholinganallur',
            'location23' => 'Chennai - Urapakkam',
            'location24' => 'Chennai - Madipakkam',
            'location26' => 'Kanchipuram',
            'location27' => 'Coimbatore - Sundarapuram',
            'location28' => 'Trichy',
            'location29' => 'Thiruvallur',
            'location30' => 'Pollachi',
            'location31' => 'Bengaluru - Electronic City',
            'location32' => 'Bengaluru - Konanakunte',
            'location33' => 'Chennai - Tambaram',
            'location34' => 'Tanjore',
            'location36' => 'Harur',
            'location39' => 'Coimbatore - Thudiyalur',
            'location40' => 'Madurai',
            'location41' => 'Bengaluru - Hebbal',
            'location42' => 'Kallakurichi',
            'location43' => 'Vellore',
            'location44' => 'Tirupati',
            'location45' => 'Aathur',
            'location46' => 'Namakal',
            'location47' => 'Bengaluru - Dasarahalli',
            'location48' => 'Chengalpattu',
            'location49' => 'Chennai - Vadapalani',
            'location50' => 'Pennagaram',
            'location51' => 'Thirupathur',
            'location52' => 'Sivakasi',
            'location13' => 'Salem',
            'location54' => 'Nagapattinam',
            'location56' => 'Krishnagiri',
            'location57' => 'Karur',
        ];
    }

    /**
     * Resolve tbl_locations row → billing_list.location_id values.
     *
     * Important: Mocdoc keys are location{N} where N is the API entity id, NOT necessarily tbl_locations.id.
     * Using 'location'.$loc->id was wrong: e.g. Karnataka branch with id=43 would match all billing for
     * location43 (Vellore). We only emit keys that match this branch's name in the canonical map, plus
     * the numeric alias for that same entity (billing sometimes stores "43" instead of "location43").
     *
     * @return string[]
     */
    public static function billingLocationIdVariantsForBranch(\App\Models\TblLocationModel $loc): array
    {
        $name = trim((string) ($loc->name ?? ''));
        $matchedKey = null;
        foreach (self::locationKeyToNameMap() as $key => $label) {
            if ($name !== '' && strcasecmp($name, trim($label)) === 0) {
                $matchedKey = $key;
                break;
            }
        }
        if ($matchedKey === null) {
            return [];
        }
        $variants = [$matchedKey];
        if (preg_match('/^location(\d+)$/', $matchedKey, $m)) {
            $variants[] = $m[1];
        }

        return array_values(array_unique($variants));
    }

    /**
     * @param  iterable<\App\Models\TblLocationModel>  $locations
     * @return string[]
     */
    public static function billingLocationIdVariantsForBranches(iterable $locations): array
    {
        $all = [];
        foreach ($locations as $loc) {
            foreach (self::billingLocationIdVariantsForBranch($loc) as $v) {
                $all[] = $v;
            }
        }

        return array_values(array_unique($all));
    }
}
