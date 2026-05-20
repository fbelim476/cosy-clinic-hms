<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\HospitalSetting;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClinicCareSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view', 'patients.view', 'patients.create', 'patients.edit',
            'visits.view', 'visits.create', 'visits.send-doctor', 'visits.send-pharmacy',
            'consultations.view', 'consultations.create', 'prescriptions.create',
            'pharmacy.view', 'pharmacy.dispense', 'pharmacy.inventory',
            'billing.view', 'billing.create', 'billing.payment',
            'lab.view', 'lab.process', 'lab.upload',
            'medicines.manage', 'users.manage', 'settings.manage',
            'reports.view', 'audit.view',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $roles = [
            'super-admin' => $permissions,
            'receptionist' => ['dashboard.view', 'patients.view', 'patients.create', 'patients.edit', 'visits.view', 'visits.create', 'visits.send-doctor'],
            'doctor' => ['dashboard.view', 'patients.view', 'visits.view', 'consultations.view', 'consultations.create', 'prescriptions.create', 'visits.send-pharmacy'],
            'pharmacist' => ['dashboard.view', 'patients.view', 'visits.view', 'pharmacy.view', 'pharmacy.dispense', 'pharmacy.inventory'],
            'accountant' => ['dashboard.view', 'billing.view', 'billing.create', 'billing.payment', 'reports.view'],
            'lab-technician' => ['dashboard.view', 'lab.view', 'lab.process', 'lab.upload'],
            'nurse' => ['dashboard.view', 'patients.view', 'visits.view', 'visits.create'],
            'patient' => ['dashboard.view', 'patients.view'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        $branch = Branch::firstOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'ClinicCare Charitable Trust Hospital',
                'address' => '123 Medical Campus Road',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'phone' => '+91 98765 43210',
                'email' => 'info@cliniccare.org',
                'gst_number' => '27AAAAA0000A1Z5',
            ]
        );

        $dept = Department::firstOrCreate(
            ['code' => 'GEN', 'branch_id' => $branch->id],
            ['name' => 'General OPD', 'description' => 'General Out Patient Department']
        );

        Department::firstOrCreate(
            ['code' => 'PED', 'branch_id' => $branch->id],
            ['name' => 'Pediatrics', 'description' => 'Child Care OPD']
        );

        $users = [
            ['name' => 'Super Admin', 'email' => 'admin@cliniccare.test', 'role' => 'super-admin', 'designation' => 'Administrator'],
            ['name' => 'Reception Desk', 'email' => 'reception@cliniccare.test', 'role' => 'receptionist', 'designation' => 'Receptionist'],
            ['name' => 'Dr. Rajesh Kumar', 'email' => 'doctor@cliniccare.test', 'role' => 'doctor', 'designation' => 'Senior Physician'],
            ['name' => 'Pharmacy Counter', 'email' => 'pharmacy@cliniccare.test', 'role' => 'pharmacist', 'designation' => 'Pharmacist'],
            ['name' => 'Accounts Desk', 'email' => 'accounts@cliniccare.test', 'role' => 'accountant', 'designation' => 'Accountant'],
            ['name' => 'Lab Technician', 'email' => 'lab@cliniccare.test', 'role' => 'lab-technician', 'designation' => 'Lab Technician'],
            ['name' => 'Nurse Station', 'email' => 'nurse@cliniccare.test', 'role' => 'nurse', 'designation' => 'Staff Nurse'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'phone' => '9000000001',
                    'branch_id' => $branch->id,
                    'department_id' => $dept->id,
                    'designation' => $u['designation'],
                    'is_active' => true,
                ]
            );
            $user->assignRole($u['role']);

            if ($u['role'] === 'doctor') {
                Doctor::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id' => $dept->id,
                        'registration_number' => 'MCI-12345',
                        'specialization' => 'General Medicine',
                        'consultation_fee' => 200,
                        'is_available' => true,
                    ]
                );
            }
        }

        $medicines = [
            ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'sku' => 'MED001', 'selling_price' => 2, 'gst_percent' => 5],
            ['name' => 'Amoxicillin 250mg', 'generic_name' => 'Amoxicillin', 'sku' => 'MED002', 'selling_price' => 8, 'gst_percent' => 5],
            ['name' => 'Cetirizine 10mg', 'generic_name' => 'Cetirizine', 'sku' => 'MED003', 'selling_price' => 3, 'gst_percent' => 5],
            ['name' => 'Omeprazole 20mg', 'generic_name' => 'Omeprazole', 'sku' => 'MED004', 'selling_price' => 5, 'gst_percent' => 5],
            ['name' => 'ORS Sachet', 'generic_name' => 'Oral Rehydration', 'sku' => 'MED005', 'selling_price' => 15, 'gst_percent' => 0],
        ];

        foreach ($medicines as $m) {
            $med = Medicine::firstOrCreate(['sku' => $m['sku']], array_merge($m, ['mrp' => $m['selling_price'] * 1.2, 'unit' => 'strip']));
            MedicineBatch::firstOrCreate(
                ['medicine_id' => $med->id, 'batch_number' => 'BATCH-' . $med->id],
                ['expiry_date' => now()->addYear(), 'quantity' => 500, 'purchase_price' => $m['selling_price'] * 0.7, 'selling_price' => $m['selling_price'], 'branch_id' => $branch->id]
            );
        }

        $labTests = [
            ['name' => 'Complete Blood Count', 'code' => 'CBC', 'price' => 350],
            ['name' => 'Blood Sugar (Fasting)', 'code' => 'BSF', 'price' => 80],
            ['name' => 'Lipid Profile', 'code' => 'LIPID', 'price' => 600],
            ['name' => 'Urine Routine', 'code' => 'URINE', 'price' => 120],
            ['name' => 'Thyroid Profile', 'code' => 'THY', 'price' => 900],
        ];

        foreach ($labTests as $t) {
            LabTest::firstOrCreate(['code' => $t['code']], $t);
        }

        $settings = [
            ['key' => 'hospital_name', 'value' => 'ClinicCare Charitable Trust Hospital', 'group' => 'general'],
            ['key' => 'hospital_address', 'value' => '123 Medical Campus Road, Mumbai - 400001', 'group' => 'general'],
            ['key' => 'hospital_phone', 'value' => '+91 98765 43210', 'group' => 'general'],
            ['key' => 'gst_number', 'value' => '27AAAAA0000A1Z5', 'group' => 'billing'],
            ['key' => 'prescription_header', 'value' => 'ClinicCare HMS - OPD Prescription', 'group' => 'print'],
            ['key' => 'invoice_footer', 'value' => 'Thank you for visiting ClinicCare. Get well soon!', 'group' => 'print'],
        ];

        foreach ($settings as $s) {
            HospitalSetting::firstOrCreate(
                ['branch_id' => $branch->id, 'key' => $s['key']],
                ['value' => $s['value'], 'group' => $s['group']]
            );
        }
    }
}
