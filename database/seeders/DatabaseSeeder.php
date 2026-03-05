<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\User;
use App\Models\ServiceJob;
use App\Models\CommissionRule;
use App\Models\RewardTier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // HQ Admin
        $hq = User::create([
            'name' => 'Admin HQ',
            'email' => 'admin@confinement.com',
            'phone' => '0123456789',
            'ic_number' => '900101010001',
            'password' => bcrypt('password'),
            'role' => 'hq',
            'state' => 'Selangor',
            'district' => 'Petaling Jaya',
            'status' => 'active',
        ]);
        $hq->assignRole('hq');

        // Staff (admin assistant with limited permissions)
        $staff = User::create([
            'name' => 'Sarah Staff',
            'email' => 'staff@confinement.com',
            'phone' => '0121112233',
            'ic_number' => '910505010010',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'state' => 'Selangor',
            'district' => 'Petaling Jaya',
            'status' => 'active',
        ]);
        $staff->assignRole('staff');
        $staff->syncPermissions([
            'access-therapists',
            'access-jobs',
            'access-bookings',
            'access-commissions',
        ]);

        // Leader
        $leader = User::create([
            'name' => 'Siti Aminah',
            'email' => 'leader@confinement.com',
            'phone' => '0129876543',
            'ic_number' => '880515060002',
            'password' => bcrypt('password'),
            'role' => 'leader',
            'state' => 'Selangor',
            'district' => 'Shah Alam',
            'kkm_cert_no' => 'KKM-2024-001',
            'bank_name' => 'Maybank',
            'bank_account' => '1234567890',
            'status' => 'active',
        ]);
        $leader->assignRole('leader');

        // Therapist 1
        $t1 = User::create([
            'name' => 'Nurul Huda',
            'email' => 'therapist1@confinement.com',
            'phone' => '0111234567',
            'ic_number' => '950320080003',
            'password' => bcrypt('password'),
            'role' => 'therapist',
            'leader_id' => $leader->id,
            'state' => 'Selangor',
            'district' => 'Subang Jaya',
            'kkm_cert_no' => 'KKM-2024-010',
            'bank_name' => 'CIMB',
            'bank_account' => '0987654321',
            'status' => 'active',
        ]);
        $t1->assignRole('therapist');

        // Therapist 2
        $t2 = User::create([
            'name' => 'Fatimah Zahra',
            'email' => 'therapist2@confinement.com',
            'phone' => '0117654321',
            'ic_number' => '920810040004',
            'password' => bcrypt('password'),
            'role' => 'therapist',
            'leader_id' => $leader->id,
            'state' => 'Selangor',
            'district' => 'Klang',
            'kkm_cert_no' => 'KKM-2024-011',
            'bank_name' => 'Bank Islam',
            'bank_account' => '1122334455',
            'status' => 'active',
        ]);
        $t2->assignRole('therapist');

        // Commission Rules (with booking portal fields)
        CommissionRule::create([
            'service_type' => 'Urut Bersalin',
            'description' => 'Traditional post-natal massage therapy to aid recovery and improve circulation.',
            'price' => 150.00,
            'therapist_commission' => 80.00,
            'leader_override' => 20.00,
            'points_per_job' => 10,
            'requires_review' => true,
            'status' => 'active',
        ]);
        CommissionRule::create([
            'service_type' => 'Bengkung',
            'description' => 'Traditional belly binding treatment for post-natal body recovery.',
            'price' => 120.00,
            'therapist_commission' => 60.00,
            'leader_override' => 15.00,
            'points_per_job' => 8,
            'requires_review' => false,
            'status' => 'active',
        ]);
        CommissionRule::create([
            'service_type' => 'Tangas',
            'description' => 'Herbal steam bath therapy for detoxification and relaxation.',
            'price' => 100.00,
            'therapist_commission' => 50.00,
            'leader_override' => 10.00,
            'points_per_job' => 5,
            'requires_review' => true,
            'status' => 'active',
        ]);

        // Reward Tiers
        RewardTier::create([
            'title' => 'Bronze',
            'min_points' => 50,
            'reward_description' => 'Bronze tier — RM50 bonus + certificate',
            'status' => 'active',
        ]);
        RewardTier::create([
            'title' => 'Silver',
            'min_points' => 150,
            'reward_description' => 'Silver tier — RM150 bonus + gift hamper',
            'status' => 'active',
        ]);
        RewardTier::create([
            'title' => 'Gold',
            'min_points' => 300,
            'reward_description' => 'Gold tier — RM500 bonus + exclusive retreat',
            'status' => 'active',
        ]);

        // Sample Jobs
        ServiceJob::create([
            'job_code' => 'JOB-20260301-001',
            'client_name' => 'Aisyah binti Ahmad',
            'client_phone' => '0123001001',
            'client_address' => 'No 15, Jalan SS2/24, Petaling Jaya, Selangor',
            'state' => 'Selangor',
            'district' => 'Petaling Jaya',
            'service_type' => 'Urut Bersalin',
            'job_date' => '2026-03-01',
            'job_time' => '10:00',
            'assigned_by' => $leader->id,
            'assigned_to' => $t1->id,
            'status' => 'pending',
        ]);

        ServiceJob::create([
            'job_code' => 'JOB-20260302-001',
            'client_name' => 'Nur Farah binti Ismail',
            'client_phone' => '0123002002',
            'client_address' => 'Blok A-12-3, Kondominium Seri Maya, Shah Alam',
            'state' => 'Selangor',
            'district' => 'Shah Alam',
            'service_type' => 'Bengkung',
            'job_date' => '2026-03-02',
            'job_time' => '14:00',
            'assigned_by' => $leader->id,
            'assigned_to' => $t1->id,
            'status' => 'accepted',
        ]);

        ServiceJob::create([
            'job_code' => 'JOB-20260303-001',
            'client_name' => 'Siti Khadijah binti Yusof',
            'client_phone' => '0123003003',
            'client_address' => 'No 8, Lorong Merbah, Taman Bukit Kemuning, Klang',
            'state' => 'Selangor',
            'district' => 'Klang',
            'service_type' => 'Tangas',
            'job_date' => '2026-03-03',
            'job_time' => '09:00',
            'assigned_by' => $leader->id,
            'assigned_to' => $t2->id,
            'status' => 'pending',
        ]);

        ServiceJob::create([
            'job_code' => 'JOB-20260228-001',
            'client_name' => 'Raihana binti Omar',
            'client_phone' => '0123004004',
            'client_address' => 'No 3, Jalan Dato Onn, Subang Jaya',
            'state' => 'Selangor',
            'district' => 'Subang Jaya',
            'service_type' => 'Urut Bersalin',
            'job_date' => '2026-02-28',
            'job_time' => '11:00',
            'assigned_by' => $leader->id,
            'assigned_to' => $t2->id,
            'status' => 'completed',
            'checked_in_at' => '2026-02-28 11:05:00',
            'checked_in_lat' => 3.0738,
            'checked_in_lng' => 101.5971,
            'checked_out_at' => '2026-02-28 12:30:00',
            'checked_out_lat' => 3.0739,
            'checked_out_lng' => 101.5972,
            'completed_at' => '2026-02-28 12:30:00',
        ]);

        // Sample Clients
        $client1 = Client::create([
            'name' => 'Aisyah binti Razak',
            'email' => 'client@confinement.com',
            'phone' => '0123100100',
            'password' => bcrypt('password'),
            'address' => 'No 10, Jalan Mawar, Taman Melati, Selangor',
            'state' => 'Selangor',
            'district' => 'Petaling Jaya',
            'status' => 'active',
        ]);

        $client2 = Client::create([
            'name' => 'Nadia binti Hassan',
            'email' => 'nadia@example.com',
            'phone' => '0123200200',
            'password' => bcrypt('password'),
            'address' => 'Blok C-5-2, Kondominium Seri Alam, Shah Alam',
            'state' => 'Selangor',
            'district' => 'Shah Alam',
            'status' => 'active',
        ]);

        // Sample Bookings
        Booking::create([
            'booking_code' => 'BK-20260301-001',
            'client_id' => $client1->id,
            'client_name' => $client1->name,
            'client_phone' => $client1->phone,
            'client_email' => $client1->email,
            'client_address' => $client1->address,
            'state' => 'Selangor',
            'district' => 'Petaling Jaya',
            'service_type' => 'Urut Bersalin',
            'preferred_date' => '2026-03-05',
            'preferred_time' => '10:00',
            'status' => 'pending_review',
            'source' => 'registered',
        ]);

        Booking::create([
            'booking_code' => 'BK-20260301-002',
            'client_id' => null,
            'client_name' => 'Zara binti Ali',
            'client_phone' => '0123300300',
            'client_email' => 'zara@example.com',
            'client_address' => 'No 5, Jalan Anggerik, Taman Sentosa, Klang',
            'state' => 'Selangor',
            'district' => 'Klang',
            'service_type' => 'Tangas',
            'preferred_date' => '2026-03-06',
            'preferred_time' => '14:00',
            'status' => 'pending_review',
            'source' => 'guest',
        ]);

        Booking::create([
            'booking_code' => 'BK-20260228-001',
            'client_id' => $client2->id,
            'client_name' => $client2->name,
            'client_phone' => $client2->phone,
            'client_email' => $client2->email,
            'client_address' => $client2->address,
            'state' => 'Selangor',
            'district' => 'Shah Alam',
            'service_type' => 'Bengkung',
            'preferred_date' => '2026-03-04',
            'preferred_time' => '09:00',
            'status' => 'approved',
            'source' => 'registered',
        ]);
    }
}
