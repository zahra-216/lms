<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;

class CourseStructureSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            1 => $this->artificialIntelligence(),
            2 => $this->computerScience(),
            3 => $this->cyberSecurity(),
            4 => $this->dataScience(),
            5 => $this->mobileDevelopment(),
        ];

        foreach ($courses as $courseId => $structure) {

            $course = Course::findOrFail($courseId);

            foreach ($structure as $levelName => $semesters) {

                $level = Level::firstOrCreate([
                    'course_id' => $course->id,
                    'name' => $levelName,
                ]);

                $levelSlug = strtoupper(substr($levelName, 0, 3));

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

    private function artificialIntelligence(): array
    {
        return [
            'Diploma' => [
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
            'HND' => [
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
            'Degree' => [
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
    }

    private function computerScience(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Computer Science',
                    'Programming Fundamentals (Python)',
                    'Web Development (HTML/CSS/JS Basics)',
                    'Computer Networks Basics',
                ],
                'Semester 2' => [
                    'Database Management Systems (SQL)',
                    'Software Development Life Cycle',
                    'Operating Systems Basics',
                    'Final Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Programming Fundamentals',
                    'Computer Systems & Architecture',
                    'Database Systems',
                    'Networking Fundamentals',
                ],
                'Semester 2' => [
                    'Web Development',
                    'Mathematics for Computing',
                    'Operating Systems',
                    'Professional Skills & IT Ethics',
                ],
                'Semester 3' => [
                    'Object-Oriented Programming',
                    'Software Engineering & Project Management',
                    'Computer Networks & Security',
                ],
                'Semester 4' => [
                    'Data Structures & Algorithms',
                    'IT Project / Capstone Project',
                    'Electives (varies)',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Computer Science',
                    'Programming Fundamentals (Python / C)',
                    'Mathematics for Computing',
                    'Digital Logic and Computer Architecture',
                ],
                'Semester 2' => [
                    'Database Fundamentals',
                    'Data Structures and Algorithms',
                    'Communication Skills and Technical Writing',
                    'Operating Systems Basics',
                ],
                'Semester 3' => [
                    'Object-Oriented Programming (Java / C++)',
                    'Database Management Systems',
                    'Computer Networks',
                    'Software Engineering Principles',
                ],
                'Semester 4' => [
                    'Web Technologies and Web Development',
                    'Data Analytics and Visualization',
                    'System Analysis and Design',
                    'Research Methods and Ethics',
                    'Internet of Things (IoT)',
                ],
                'Semester 5' => [
                    'Artificial Intelligence and Machine Learning',
                    'Mobile Application Development',
                    'Cloud Computing',
                    'Cybersecurity and Ethical Hacking',
                ],
                'Semester 6' => [
                    'Human-Computer Interaction (HCI)',
                    'Advanced Algorithms and Complexity',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function cyberSecurity(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Cyber Security',
                    'Network Security',
                    'OS & Application Security',
                    'Ethical Hacking',
                ],
                'Semester 2' => [
                    'Digital Forensics',
                    'Security Management',
                    'Final Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Programming',
                    'Networking',
                    'Professional Practice',
                    'Database Design & Development',
                ],
                'Semester 2' => [
                    'Security',
                    'Data Analytics',
                    'Cyber Security',
                    'Website Design and Development',
                ],
                'Semester 3' => [
                    'Computing Research Project',
                    'Business Process Support',
                    'Applied Cryptography in the Cloud',
                    'Forensics',
                ],
                'Semester 4' => [
                    'Information Security Management',
                    'Network Management',
                    'System Analysis and Design',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Web Technologies',
                    'Mechanics',
                    'Communication Skills',
                    'Introduction to Computer Science & Programming in Python',
                    'Management Thoughts and Applications',
                    'Introduction to Computer Science & Programming in Python Lab',
                    'Web Technologies Lab',
                    'Mechanics Lab',
                ],
                'Semester 2' => [
                    'Number Theory',
                    'Environmental Studies',
                    'Cyberspace & the Law',
                    'Open Elective',
                    'Object-oriented Programming',
                    'Object-oriented Programming Lab',
                    'Linux Environment Lab',
                ],
                'Semester 3' => [
                    'Introduction to Cyber Security',
                    'Computer Networks',
                    'Operating Systems',
                    'Data Structures',
                    'Probability & Statistics',
                    'Disaster Management',
                    'Introduction to Cyber Security Lab',
                    'Operating Systems Lab',
                ],
                'Semester 4' => [
                    'Database Management Systems',
                    'Analysis & Design of Algorithms',
                    'Network Security & Stenography',
                    'Cryptography',
                    'Artificial Intelligence',
                    'Database Management Systems Lab',
                    'Analysis & Design of Algorithms Lab',
                    'Network Security & Stenography Lab',
                ],
                'Semester 5' => [
                    'Foundation of Computer Systems',
                    'Cyber Forensics',
                    'Programming in MATLAB',
                    'Programming in MATLAB - Lab',
                    'Cyber Forensics Lab',
                    'Probability & Statistics Lab',
                ],
                'Semester 6' => [
                    'Intrusion Detection & Prevention Systems',
                    'Blockchains',
                    'Software Engineering',
                    'Computer Organization & Architecture',
                    'Computer Organization & Architecture Lab',
                    'Blockchains Lab',
                    'Final Year Project / Internship',
                ],
            ],
        ];
    }

    private function dataScience(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Data Science',
                    'Mathematics & Statistics for Data Science',
                    'Programming for Data Science',
                    'Data Wrangling & Data Visualization',
                ],
                'Semester 2' => [
                    'Database Management & SQL',
                    'Machine Learning & AI Fundamentals',
                    'Big Data & Cloud Analytics',
                    'Project / Research',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Fundamentals of Programming (Python/R)',
                    'Mathematics for Data Science (Linear Algebra, Statistics)',
                    'Data Collection and Cleaning',
                    'Data Visualization and Reporting',
                ],
                'Semester 2' => [
                    'Exploratory Data Analysis (EDA)',
                    'Supervised & Unsupervised Learning',
                    'Introduction to SQL and Databases',
                    'Deep Learning and Neural Networks & Natural Language Processing (NLP)',
                ],
                'Semester 3' => [
                    'Time Series Analysis',
                    'Big Data Technologies (Hadoop, Spark)',
                    'Cloud Computing for Data Science (AWS, Azure, GCP)',
                ],
                'Semester 4' => [
                    'Data Science Ethics and Governance',
                    'Model Deployment and MLOps',
                    'Case Studies in Data Science',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Programming (Python)',
                    'Computer & IT Fundamentals',
                    'Mathematics for Data Science',
                    'Statistics Fundamentals',
                ],
                'Semester 2' => [
                    'Data Structures & Algorithms',
                    'Probability & Statistical Analysis',
                    'Python for Data Analysis',
                    'Data Visualization Basics',
                ],
                'Semester 3' => [
                    'Database Management Systems (SQL / NoSQL)',
                    'Data Wrangling & Preprocessing',
                    'Exploratory Data Analysis (EDA)',
                    'Data Visualization Tools (Matplotlib / Seaborn / Power BI / Tableau)',
                ],
                'Semester 4' => [
                    'Machine Learning Fundamentals',
                    'Supervised & Unsupervised Learning',
                    'Data Mining Techniques',
                ],
                'Semester 5' => [
                    'Advanced Machine Learning',
                    'Artificial Intelligence Fundamentals',
                    'Deep Learning Basics',
                    'Cloud Computing for Data Science (AWS / Azure / Google Cloud)',
                ],
                'Semester 6' => [
                    'Big Data Concepts',
                    'Data Ethics, Privacy & Security',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function mobileDevelopment(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Foundational Programming',
                    'Android Application Development',
                    'iOS Application Development',
                    'Cross-Platform Mobile Development',
                ],
                'Semester 2' => [
                    'UI / UX Design for Mobile Apps',
                    'Database Management',
                    'Mobile Networking & APIs',
                    'Final Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to Information Technology',
                    'Programming Fundamentals',
                    'HTML & CSS Fundamentals',
                    'JavaScript Programming',
                ],
                'Semester 2' => [
                    'UI / UX Design for Web',
                    'Database Management Systems',
                    'Version Control Systems',
                    'Communication & Professional Skills',
                ],
                'Semester 3' => [
                    'Advanced JavaScript',
                    'Frontend Frameworks',
                    'Backend Web Development',
                    'Web Application Development',
                ],
                'Semester 4' => [
                    'Web Security Fundamentals',
                    'Web Testing & Debugging',
                    'Software Engineering & Agile',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Programming',
                    'Programming with Java / Python',
                    'Fundamentals of Mobile Computing',
                    'Computer Systems & Operating Systems',
                ],
                'Semester 2' => [
                    'Database Fundamentals',
                    'Web Technologies (HTML, CSS, JavaScript)',
                    'Mathematics for Computing',
                    'Professional Practice & Communication Skills',
                ],
                'Semester 3' => [
                    'Object-Oriented Programming (Java / Kotlin / Swift basics)',
                    'Android App Development – I',
                    'iOS App Development – I',
                    'Mobile UI/UX Design Principles',
                ],
                'Semester 4' => [
                    'Database Systems & SQL',
                    'Software Engineering Methods',
                    'API Development & Integration',
                    'Mobile Security Fundamentals',
                    'Mobile App Performance Optimization',
                ],
                'Semester 5' => [
                    'Advanced Android Development',
                    'Advanced iOS Development',
                    'Cross-Platform Mobile Development (Flutter / React Native)',
                    'Mobile Application Testing & Debugging',
                ],
                'Semester 6' => [
                    'Cloud Services for Mobile Apps (Firebase / AWS)',
                    'Emerging Mobile Technologies (AI, IoT, AR/VR)',
                    'Final Project / Internship',
                ],
            ],
        ];
    }
}