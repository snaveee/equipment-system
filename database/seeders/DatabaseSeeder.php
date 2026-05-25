<?php

namespace Database\Seeders;

use App\Models\BorrowingTransaction;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Custodian',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Staff User
        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // Equipment
        $equipmentData = [
            ['Dell Latitude 5530 Laptop', 'Business laptop with 16GB RAM', 'Laptop', 'DLL-5530-001', 'new', 'available'],
            ['Epson EB-X51 Projector', 'XGA 3LCD projector', 'Projector', 'EPS-X51-002', 'good', 'available'],
            ['Canon EOS R6 Camera', 'Mirrorless full-frame camera', 'Camera', 'CAN-R6-003', 'good', 'available'],
            ['Wacom Intuos Pro Tablet', 'Medium drawing tablet', 'Tablet', 'WAC-INT-004', 'fair', 'available'],
            ['HP LaserJet Pro Printer', 'Monochrome laser printer', 'Printer', 'HP-LJP-005', 'good', 'available'],
            ['Logitech C920 Webcam', 'Full HD 1080p webcam', 'Accessory', 'LOG-C920-006', 'new', 'available'],
            ['Bose SoundLink Mini', 'Portable Bluetooth speaker', 'Audio', 'BOS-SLM-007', 'good', 'available'],
            ['Apple iPad Air 5', '10.9-inch tablet with M1', 'Tablet', 'APL-IPA-008', 'new', 'available'],
            ['DJI Osmo Mobile 6', 'Smartphone gimbal stabilizer', 'Camera', 'DJI-OM6-009', 'good', 'available'],
            ['Yamaha P-45 Keyboard', '88-key digital piano', 'Instrument', 'YAM-P45-010', 'fair', 'available'],
            ['ASUS ROG Strix Laptop', 'Gaming laptop with RTX 4060', 'Laptop', 'ASU-ROG-011', 'new', 'available'],
            ['BenQ Eye-Care Monitor', '27-inch QHD monitor', 'Monitor', 'BNQ-EYE-012', 'good', 'available'],
        ];

        foreach ($equipmentData as [$name, $desc, $cat, $serial, $cond, $status]) {
            Equipment::updateOrCreate(
                ['serial_number' => $serial],
                [
                    'name' => $name,
                    'description' => $desc,
                    'category' => $cat,
                    'condition' => $cond,
                    'status' => $status,
                ]
            );
        }

        // Borrower Users
        $borrowerData = [
            ['Maria Santos', 'Computer Science', 'Instructor', '09171234567', 'maria.santos@example.com'],
            ['Juan Dela Cruz', 'Engineering', 'Lab Assistant', '09181234567', 'juan.delacruz@example.com'],
            ['Ana Reyes', 'Multimedia Arts', 'Student Leader', '09191234567', 'ana.reyes@example.com'],
            ['Carlos Mendoza', 'Administration', 'Officer', '09201234567', 'carlos.mendoza@example.com'],
            ['Liza Garcia', 'Library Services', 'Librarian', '09211234567', 'liza.garcia@example.com'],
        ];

        foreach ($borrowerData as [$name, $dept, $pos, $contact, $email]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('TempPassword123!'), // Temporary password for first login
                    'role' => 'borrower',
                    'department' => $dept,
                    'position' => $pos,
                    'contact_number' => $contact,
                ]
            );
        }

        // Sample transactions (only if none exist)
        if (BorrowingTransaction::count() === 0) {
            $admin = User::where('role', 'admin')->first();
            $maria = User::where('email', 'maria.santos@example.com')->first();
            $juan = User::where('email', 'juan.delacruz@example.com')->first();
            $ana = User::where('email', 'ana.reyes@example.com')->first();

            $laptop = Equipment::where('serial_number', 'DLL-5530-001')->first();
            $projector = Equipment::where('serial_number', 'EPS-X51-002')->first();
            $camera = Equipment::where('serial_number', 'CAN-R6-003')->first();

            // Active borrow
            BorrowingTransaction::create([
                'user_id' => $maria->id,
                'equipment_id' => $laptop->id,
                'purpose' => 'Lecture preparation for CS101 course',
                'borrow_date' => now()->subDays(3)->toDateString(),
                'expected_return_date' => now()->addDays(4)->toDateString(),
                'status' => 'active',
                'processed_by' => $admin->id,
            ]);
            $laptop->update(['status' => 'borrowed']);

            // Overdue borrow
            BorrowingTransaction::create([
                'user_id' => $juan->id,
                'equipment_id' => $projector->id,
                'purpose' => 'Department seminar',
                'borrow_date' => now()->subDays(15)->toDateString(),
                'expected_return_date' => now()->subDays(5)->toDateString(),
                'status' => 'overdue',
                'processed_by' => $admin->id,
            ]);
            $projector->update(['status' => 'borrowed']);

            // Returned with damage
            BorrowingTransaction::create([
                'user_id' => $ana->id,
                'equipment_id' => $camera->id,
                'purpose' => 'Student film project',
                'borrow_date' => now()->subDays(10)->toDateString(),
                'expected_return_date' => now()->subDays(3)->toDateString(),
                'actual_return_date' => now()->subDays(2)->toDateString(),
                'return_condition' => 'damaged',
                'damage_remarks' => 'Lens has visible scratches and minor dent on body.',
                'follow_up_actions' => 'Sent to repair shop for assessment.',
                'status' => 'returned',
                'processed_by' => $admin->id,
            ]);
            $camera->update(['condition' => 'damaged', 'status' => 'under_repair']);
        }
    }
}
