<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Tenant;
use App\Models\Campus\Campus;
use App\Models\Academic\Session;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\CampusClass;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create a demo tenant
        $tenant = Tenant::updateOrCreate(
            ['subdomain' => 'alhikma'],
            ['name' => 'Al-Hikma School System']
        );
        
        // 2. Register first campus
        $campus = Campus::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Main Campus'],
            ['address' => 'Block 5, City Center', 'is_active' => true]
        );

        // 3. Create active session
        $session = Session::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => '2024-2025'],
            [
                'start_date' => '2024-04-01',
                'end_date' => '2025-03-31',
                'is_active' => true
            ]
        );

        // 4. Create Classes & Sections
        $class1 = SchoolClass::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Nursery'], ['numeric_value' => 1]);
        $class2 = SchoolClass::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Prep'], ['numeric_value' => 2]);
        $class3 = SchoolClass::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'One'], ['numeric_value' => 3]);

        Section::updateOrCreate(['tenant_id' => $tenant->id, 'school_class_id' => $class1->id, 'name' => 'A']);
        Section::updateOrCreate(['tenant_id' => $tenant->id, 'school_class_id' => $class2->id, 'name' => 'A']);
        Section::updateOrCreate(['tenant_id' => $tenant->id, 'school_class_id' => $class3->id, 'name' => 'A']);

        // 5. Map classes to campus
        CampusClass::updateOrCreate(['tenant_id' => $tenant->id, 'campus_id' => $campus->id, 'school_class_id' => $class1->id]);
        CampusClass::updateOrCreate(['tenant_id' => $tenant->id, 'campus_id' => $campus->id, 'school_class_id' => $class2->id]);
        CampusClass::updateOrCreate(['tenant_id' => $tenant->id, 'campus_id' => $campus->id, 'school_class_id' => $class3->id]);

        // 6. Fee Particulars
        $tuition = \App\Models\Finance\FeeParticular::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Tuition Fee']);
        $adm = \App\Models\Finance\FeeParticular::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Admission Fee']);
        \App\Models\Finance\FeeParticular::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Transport Fee']);

        // 7. Fee Plans
        $plan = \App\Models\Finance\FeePlan::updateOrCreate(['tenant_id' => $tenant->id, 'name' => 'Regular Plan']);
        
        // 8. Mapping
        \App\Models\Finance\FeePlanParticular::updateOrCreate([
            'tenant_id' => $tenant->id, 
            'campus_id' => $campus->id, 
            'fee_plan_id' => $plan->id, 
            'fee_particular_id' => $tuition->id
        ], [
            'amount' => 2500,
            'jan' => true, 'feb' => true, 'mar' => true, 'apr' => true, 'may' => true, 'jun' => true,
            'jul' => true, 'aug' => true, 'sep' => true, 'oct' => true, 'nov' => true, 'dec' => true
        ]);

        \App\Models\Finance\FeePlanParticular::updateOrCreate([
            'tenant_id' => $tenant->id, 
            'campus_id' => $campus->id, 
            'fee_plan_id' => $plan->id, 
            'fee_particular_id' => $adm->id
        ], [
            'amount' => 5000,
            'is_first_time' => true
        ]);
    }
}
