<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;

class AiCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::findOrFail(1); // Artificial Intelligence (FSTAI26100)

        $structure = [
            'Diploma in Artificial Intelligence' => [
                'Semester 1' => [
                    'Introduction to Artificial Intelligence',
                    'Python for AI & Data Science',
                    'Mathematics for AI (Linear Algebra & Probability)',
                    'Machine Learning Basics',
                ],
                'Semester 2' => [
                    'Data Handling & Visualization (Pandas/Matplotlib)',
                    'Deep Learning Foundations (Neural Networks)',
                    'AI in Real World (Projects & Applications)',
                    'Final Project (AI-based project)',
                ],
            ],
            'HND in Artificial Intelligence' => [
                'Semester 1' => [
                    'Professional Practice in the Digital Economy',
                    'Innovation and Digital Transformation',
                    'Cyber Security',
                    'Programming',
                ],
                'Semester 2' => [
                    'Big Data & Visualization',
                    'Cloud Fundamentals',
                    'Software Development Lifecycles',
                    'Fundamentals of Artificial Intelligence (AI) & Intelligent Systems',
                ],
                'Semester 3' => [
                    'Business Intelligence',
                    'Internet of Things',
                    'Emerging Technologies',
                    'Risk Analysis and Systems Testing',
                ],
                'Semester 4' => [
                    'Application Development',
                    'Application Program Interfaces',
                    'Digital Sustainability',
                    'Final Project',
                ],
            ],
            'Degree in Artificial Intelligence' => [
                'Semester 1' => [
                    'Introduction to Computing',
                    'Programming Fundamentals (Python, C++)',
                    'Mathematics for AI (Linear Algebra, Calculus)',
                    'Statistics and Probability',
                ],
                'Semester 2' => [
                    'Computer Systems and Architecture',
                    'Data Structures and Algorithms',
                    'Communication and Academic Writing',
                    'Principles of Artificial Intelligence',
                ],
                'Semester 3' => [
                    'Object-Oriented Programming (Java / Python)',
                    'Database Management Systems (SQL, NoSQL)',
                    'Operating Systems',
                    'Machine Learning Fundamentals',
                ],
                'Semester 4' => [
                    'Data Analytics and Visualization',
                    'Computer Networks and Internet Technologies',
                    'Human–Computer Interaction',
                    'Research Methods and Ethics',
                ],
                'Semester 5' => [
                    'Deep Learning and Neural Networks',
                    'Natural Language Processing (NLP)',
                    'Computer Vision',
                    'Cloud Computing and Big Data',
                    'Intelligent Robotics',
                ],
                'Semester 6' => [
                    'Internet of Things (IoT) and Edge AI',
                    'AI in Cybersecurity',
                    'Mini Project / Industrial Training',
                ],
            ],
        ];

        foreach ($structure as $levelName => $semesters) {

            $level = Level::firstOrCreate([
                'course_id' => $course->id,
                'name' => $levelName,
            ]);

            $levelSlug = strtoupper(collect(explode(' ', $levelName))->map(fn($w) => $w[0])->implode(''));

            foreach ($semesters as $semesterName => $subjects) {

                $semester = Semester::firstOrCreate([
                    'level_id' => $level->id,
                    'course_id' => $course->id,
                    'name' => $semesterName,
                ]);

                $semesterNumber = (int) preg_replace('/[^0-9]/', '', $semesterName);

                foreach ($subjects as $index => $subjectName) {

                    $subjectCode = sprintf(
                        '%s-%s-S%d-%02d',
                        $course->code,
                        $levelSlug,
                        $semesterNumber,
                        $index + 1
                    );

                    Subject::firstOrCreate(
                        [
                            'course_id' => $course->id,
                            'level_id' => $level->id,
                            'semester_id' => $semester->id,
                            'name' => $subjectName,
                        ],
                        [
                            'code' => $subjectCode,
                            'description' => null,
                            'credits' => null,
                            'status' => 'active',
                        ]
                    );
                }
            }
        }
    }
}