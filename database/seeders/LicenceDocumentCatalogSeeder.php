<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Default licence document slots (Level 1 / 2). Used by migration and optional artisan seed.
 */
class LicenceDocumentCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('licence_document_catalog')->count() > 0) {
            return;
        }
        $now = now();
        $rows = self::defaultRows();
        foreach ($rows as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);
        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('licence_document_catalog')->insert($chunk);
        }
    }

    public static function defaultRows(): array
    {
        $l1 = [
            ['art_level_1_certificate', 'ART level 1 certificate'],
            ['drug_licence', 'Drug licence'],
            ['fire_licence', 'Fire licence'],
            ['trade_licence', 'Trade licence'],
            ['professional_tax', 'Professional tax'],
            ['sanitary_certificate', 'Sanitary certificate'],
            ['clinical_establishment', 'Clinical Establishment'],
            ['labour_certificate', 'Labour certificate'],
            ['pollution_control_one_time_authorization', 'Pollution control certificate one time Authorization'],
            ['pcpndt_licence', 'PCPNDT licence'],
            ['biomedical_waste_agreement', 'Biomedical waste agreement'],
            ['lease_agreement', 'Lease agreement'],
            ['schedule_x_drug_licence', 'Schedule X drug licence'],
            ['building_stability_certificate', 'Building stability certificate'],
            ['building_property_tax', 'Building property tax'],
            ['building_plan_approval_blueprint', 'Building plan approval (Blue print)'],
            ['building_water_tax', 'Building water tax'],
            ['building_owner_noc_letter', 'Building owner NOC letter'],
            ['building_lift_licence', 'Building Lift licence'],
            ['building_lift_invoice', 'Building Lift invoice'],
        ];

        $l2 = [
            ['art_level_2_certificate', 'ART Level 2 certificate'],
            ['drug_licence', 'Drug licence'],
            ['fire_licence', 'Fire licence'],
            ['trade_licence', 'Trade licence'],
            ['professional_tax', 'Professional tax'],
            ['sanitary_certificate', 'Sanitary certificate'],
            ['clinical_establishment_act_certificate', 'Clinical Establishment act certificate'],
            ['labour_certificate', 'Labour certificate'],
            ['pollution_control_bedded_certificate', 'Pollution control certificate Bedded certificate'],
            ['pcpndt_licence', 'PCPNDT licence'],
            ['biomedical_waste_agreement', 'Biomedical waste agreement'],
            ['lease_agreement', 'Lease agreement'],
            ['schedule_x_drug_licence', 'Schedule X drug licence'],
            ['building_stability_certificate', 'Building stability certificate'],
            ['building_property_tax', 'Building property tax'],
            ['building_plan_approval_blueprint', 'Building plan approval (Blue print)'],
            ['building_water_tax', 'Building water tax'],
            ['building_owner_noc_letter', 'Building owner NOC letter'],
            ['building_lift_licence', 'Building Lift licence'],
            ['building_lift_invoice', 'Building Lift invoice'],
            ['surrogacy_certificate', 'Surrogacy certificate'],
            ['building_form_d_certificate', 'Building Form D certificate'],
            ['mtp_licence', 'MTP Licence'],
            ['narcotic_drug_licence', 'Narcotic drug licence'],
            ['building_patta_copy', 'Building patta copy'],
            ['hospital_registration_certificate', 'Hospital Registration certificate'],
            ['building_completed_certificate', 'Building completed certificate'],
        ];

        $out = [];
        $i = 1;
        foreach ($l1 as [$key, $label]) {
            $out[] = [
                'document_key' => $key,
                'label' => $label,
                'level' => 1,
                'is_active' => true,
            ];
        }
        $i = 1;
        foreach ($l2 as [$key, $label]) {
            $out[] = [
                'document_key' => $key,
                'label' => $label,
                'level' => 2,
                'is_active' => true,
            ];
        }

        return $out;
    }
}
