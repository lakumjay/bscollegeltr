<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Courses;
use App\Models\Subject;
use App\Models\Session;
use App\Models\StudentCourse;
use App\Models\User;
use Carbon\Carbon;

class Import2026Students extends Command
{
    protected $signature = 'students:import-2026';
    protected $description = 'Check and import 2026-2030 students from CSV file';

    public function handle()
    {
        $csvPath = base_path('database/data/students_2026_2030.csv');
        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return 1;
        }

        $adminUser = User::where('type', 'admin')->first() ?: User::first();
        $adminId = $adminUser ? $adminUser->id : 1;

        $session = Session::firstOrCreate(['session_name' => '2026-2030']);
        $sessionId = $session->id;

        $file = fopen($csvPath, 'r');
        $headers = [];
        $existingCount = 0;
        $insertedCount = 0;
        $rowNum = 0;

        while (($row = fgetcsv($file)) !== false) {
            $rowNum++;
            if (empty($row) || !isset($row[0])) continue;

            if ($row[0] === 'ROLL NO.') {
                $headers = array_map(fn($h) => strtoupper(trim((string)$h)), $row);
                continue;
            }

            if (empty($headers)) continue;

            $data = [];
            foreach ($headers as $idx => $headerName) {
                $data[$headerName] = trim($row[$idx] ?? '');
            }

            $rollNo = $data['ROLL NO.'] ?? '';
            $courseName = $data['COURSE'] ?? 'BA';
            $subjectName = $data['SUBJECT'] ?? '';
            $firstName = $data['FIRST NAME'] ?? '';
            $batch = $data['BATCH'] ?? '2026-2030';
            $admissionDate = $data['ADMISSION DATE'] ?? '2026-08-17';
            $gender = $data['GENDER'] ?? 'FEMALE';
            $semester = (int) ($data['SEM'] ?? 1);
            $dobRaw = $data['DOB'] ?? '';
            $mobile = $data['MOBILE'] ?? '';
            $category = $data['CATEGORY'] ?? 'GEN';
            $email = $data['EMAIL'] ?? '';
            $fatherName = $data['FATHERS NAME'] ?? '';
            $motherName = $data['MOTHERS NAME'] ?? '';
            $address = $data['ADDRESS'] ?? '';

            if (empty($rollNo) || empty($firstName)) continue;

            // Course resolution
            $course = Courses::where('name', $courseName)->first();
            if (!$course) {
                $course = Courses::create([
                    'user_id' => $adminId,
                    'name' => $courseName,
                    'sr' => 1
                ]);
            }

            // Subject resolution
            $subjectIds = [];
            if (!empty($subjectName)) {
                $subject = Subject::where('course_id', $course->id)
                    ->where('name', $subjectName)
                    ->first();
                if (!$subject) {
                    $subject = Subject::create([
                        'course_id' => $course->id,
                        'name' => $subjectName
                    ]);
                }
                $subjectIds = [(string)$subject->id];
            }

            // Format DOB
            $formattedDob = null;
            if (!empty($dobRaw)) {
                try {
                    $formattedDob = Carbon::createFromFormat('d/m/Y', $dobRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $formattedDob = date('Y-m-d', strtotime($dobRaw));
                }
            }

            // Check if already in database
            $exists = Student::where('roll_no', $rollNo)
                ->where('course', (string)$course->id)
                ->where('batch', $batch)
                ->exists();

            if ($exists) {
                $existingCount++;
                continue;
            }

            // Also check by email to avoid unique violation
            if (!empty($email) && Student::where('email', $email)->exists()) {
                $email = 'roll' . $rollNo . '_' . $email;
            }

            $nameParts = explode(' ', $firstName, 2);
            $fName = $nameParts[0];
            $lName = $nameParts[1] ?? '';

            $student = Student::create([
                'user_id' => $adminId,
                'course' => (string)$course->id,
                'session_id' => $sessionId,
                'roll_no' => $rollNo,
                'student_id' => $rollNo,
                'first_name' => $fName,
                'last_name' => $lName,
                'gender' => ucfirst(strtolower($gender)),
                'date_of_birth' => $formattedDob,
                'phone_number' => $mobile,
                'email' => $email,
                'father_name' => $fatherName,
                'mother_name' => $motherName,
                'batch' => $batch,
                'admission_date' => !empty($admissionDate) ? substr($admissionDate, 0, 10) : date('Y-m-d'),
                'current_year' => 1,
                'current_semester' => $semester,
                'category' => $category,
                'caste' => '',
                'permanent_address' => $address,
                'correspondence_address' => $address,
                'subjects' => $subjectIds,
                'status' => 'Active',
            ]);

            StudentCourse::firstOrCreate([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'session_id' => $sessionId,
                'semaster' => $semester,
            ], [
                'user_id' => $adminId,
            ]);

            $insertedCount++;
        }

        fclose($file);

        $this->info("=== Import Check Finished ===");
        $this->line("Students already present in database: {$existingCount}");
        $this->line("New students uploaded into database: {$insertedCount}");
        return 0;
    }
}
