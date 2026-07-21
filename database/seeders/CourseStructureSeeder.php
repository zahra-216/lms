<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;

class CourseStructureSeeder extends Seeder
{
    // Short codes used as the first segment of every subject code
    private array $courseShortCodes = [
        1 => 'AI',
        2 => 'CS',
        3 => 'CFB',
        4 => 'DS',
        5 => 'MD',
        6 => 'AC',
        7 => 'BM',
        8 => 'DM',
        9 => 'HRM',
        10 => 'HT',
        11 => 'CE',
        12 => 'AM',
        13 => 'ID',
        14 => 'QS',
        15 => 'EN',
    ];

    // Words ignored when generating the module code segment
    private array $fillerWords = ['and', 'of', 'to', 'the', 'for', 'in', '&'];

    public function run(): void
    {
        $courses = [
            1 => $this->artificialIntelligence(),
            2 => $this->computerScience(),
            3 => $this->cyberSecurity(),
            4 => $this->dataScience(),
            5 => $this->mobileDevelopment(),
            6 => $this->accounting(),
            7 => $this->businessManagement(),
            8 => $this->digitalMarketing(),
            9 => $this->humanResourceManagement(),
            10 => $this->hospitalityTourism(),
            11 => $this->civilEngineering(),
            12 => $this->autocadMep(),
            13 => $this->interiorDesigning(),
            14 => $this->quantitySurveying(),
            15 => $this->english(),
        ];

        foreach ($courses as $courseId => $structure) {

            $course = Course::findOrFail($courseId);
            $shortCode = $this->courseShortCodes[$courseId];

            foreach ($structure as $levelName => $semesters) {

                $level = Level::firstOrCreate([
                    'course_id' => $course->id,
                    'name' => $levelName,
                ]);

                foreach ($semesters as $semesterName => $subjects) {

                    $semester = Semester::firstOrCreate([
                        'level_id' => $level->id,
                        'course_id' => $course->id,
                        'name' => $semesterName,
                    ]);

                    foreach ($subjects as $index => $subjectName) {

                        $moduleCode = $this->generateModuleCode($subjectName);

                        $subjectCode = sprintf(
                            '%s-%s-%02d',
                            $shortCode,
                            $moduleCode,
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

    /**
     * Strips filler words and takes the first letter of each remaining word.
     * e.g. "Introduction to Business and Management" -> IBM
     *      "Evolution of Management Thought" -> EMT
     *      "Recruitment and Selection" -> RS
     */
    private function generateModuleCode(string $name): string
    {
        // Split on spaces, hyphens, slashes
        $words = preg_split('/[\s\-\/]+/', $name);

        $letters = '';

        foreach ($words as $word) {
            $clean = trim($word, "()–—.,");
            if ($clean === '') continue;

            if (in_array(strtolower($clean), $this->fillerWords)) continue;

            $letters .= strtoupper($clean[0]);
        }

        return $letters !== '' ? $letters : 'GEN';
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

    private function accounting(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Accounting',
                    'Financial Accounting',
                    'Final Accounts Preparation',
                    'Cost Accounting',
                ],
                'Semester 2' => [
                    'Management Accounting',
                    'Accounting Software & Computerized Accounting',
                    'Taxation & Auditing Basics',
                    'Accounting Project / Practical Training',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to Accounting',
                    'Principles of Financial Accounting',
                    'Financial Accounting',
                    'Management Accounting',
                ],
                'Semester 2' => [
                    'Cost Accounting',
                    'Business Mathematics & Statistics',
                    'Accounting Software Applications',
                    'Financial Reporting',
                ],
                'Semester 3' => [
                    'Auditing Principles',
                    'Taxation',
                    'Business Economics',
                    'Corporate & Business Law',
                ],
                'Semester 4' => [
                    'Financial Management',
                    'Ethics & Professional Practice',
                    'Communication & IT Skills',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Accounting & Finance',
                    'Business Environment',
                    'Quantitative Techniques for Business',
                    'Principles of Economics',
                ],
                'Semester 2' => [
                    'Business Communication & Presentation Skills',
                    'Digital Literacy for Accountants',
                    'Financial Accounting Fundamentals',
                    'Intermediate Financial Reporting',
                ],
                'Semester 3' => [
                    'Management Accounting Techniques',
                    'Corporate Law for Accountants',
                    'Principles of Taxation',
                    'Accounting Software Applications (Tally / QuickBooks / SAP – Basics)',
                ],
                'Semester 4' => [
                    'Internal Control & Risk Management',
                    'Research & Analytical Skills',
                    'International Financial Reporting Standards (IFRS)',
                ],
                'Semester 5' => [
                    'Strategic Cost Management',
                    'Advanced Auditing & Assurance',
                    'Corporate Finance & Investment Analysis',
                    'Public Sector & Non-Profit Accounting',
                ],
                'Semester 6' => [
                    'Business Ethics & Corporate Governance',
                    'Applied Accounting Project / Dissertation',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function businessManagement(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Business and Management',
                    'Evolution of Management Thought',
                    'Recruitment and Selection',
                ],
                'Semester 2' => [
                    'Motivation and Employee Management',
                    'Introduction to Business Statistics',
                    'Final Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Principles of Management',
                    'Business Communication',
                    'Marketing Management',
                    'Financial Accounting and Analysis',
                    'Human Resource Management',
                ],
                'Semester 2' => [
                    'Business Mathematics',
                    'Information Technology for Business Management',
                    'Business Law and Ethics',
                    'Entrepreneurship and Small Business Management',
                    'Operations and Supply Chain Management',
                ],
                'Semester 3' => [
                    'Strategic Management and Economics',
                    'Business Economics',
                    'Management Information Systems',
                    'Project Management',
                ],
                'Semester 4' => [
                    'Financial Accounting',
                    'Micro Economics',
                    'Leadership and Organizational Behavior',
                    'Global Business Environment',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Principles of Management',
                    'Business Communication',
                    'Microeconomics',
                    'Financial Accounting – I',
                ],
                'Semester 2' => [
                    'Business Mathematics & Statistics',
                    'Principles of Marketing',
                    'Business Environment',
                    'Information Technology for Business',
                ],
                'Semester 3' => [
                    'Organizational Behaviour',
                    'Human Resource Management',
                    'Financial Accounting – II',
                    'Cost & Management Accounting',
                ],
                'Semester 4' => [
                    'Business Law',
                    'Marketing Management',
                    'Operations Management',
                    'Research Methods for Business',
                ],
                'Semester 5' => [
                    'Strategic Management',
                    'Financial Management',
                    'Entrepreneurship & Small Business Management',
                    'International Business Management',
                ],
                'Semester 6' => [
                    'Project Management',
                    'Business Ethics & Corporate Governance',
                    'Management Information Systems',
                    'Final Year Project',
                ],
            ],
        ];
    }

    private function digitalMarketing(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Digital Marketing',
                    'Search Engine Optimization (SEO)',
                    'Social Media Marketing (SMM)',
                    'Search Engine Marketing (SEM / Google Ads)',
                ],
                'Semester 2' => [
                    'Content Marketing',
                    'Email Marketing',
                    'Web Analytics & Reporting',
                    'Digital Marketing Project / Practical',
                ],
            ],
        ];
    }

    private function humanResourceManagement(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Fundamentals of Management',
                    'Principles of Human Resource Management',
                    'Organizational Behavior',
                    'Performance Management',
                    'Labour Law and Industrial Relations',
                    'Strategic Human Resource Management',
                ],
                'Semester 2' => [
                    'Leadership and Team Building',
                    'Communication and Workplace Ethics',
                    'Employee Health, Safety & Welfare',
                    'HR Analytics and Information Systems',
                    'Research Project / Internship',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to Human Resource Management',
                    'Principles of Management',
                    'Organizational Behaviour',
                    'Human Resource Planning',
                ],
                'Semester 2' => [
                    'Recruitment & Selection',
                    'Training & Development',
                    'Performance Management',
                    'Compensation & Benefits Management',
                ],
                'Semester 3' => [
                    'Employee Relations',
                    'Labour Law & Industrial Relations',
                    'Strategic Human Resource Management',
                    'Health, Safety & Welfare',
                ],
                'Semester 4' => [
                    'Change Management',
                    'HR Analytics (Basic)',
                    'Professional Practice & Ethics',
                    'Industrial Training / Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Human Resource Management',
                    'Principles of Management',
                    'Business Economics',
                    'Organizational Behaviour',
                ],
                'Semester 2' => [
                    'Business Communication Skills',
                    'Fundamentals of Accounting',
                    'Information Technology for Business',
                ],
                'Semester 3' => [
                    'Human Resource Planning & Development',
                    'Recruitment & Selection',
                    'Training & Development',
                    'Performance Management',
                ],
                'Semester 4' => [
                    'Employment Law & Industrial Relations',
                    'Research Methods for Business',
                    'Management Information Systems',
                ],
                'Semester 5' => [
                    'Strategic Human Resource Management',
                    'Talent Management & Leadership',
                    'Compensation & Reward Management',
                    'Employee Relations & Labour Law',
                ],
                'Semester 6' => [
                    'Change Management & Organizational Development',
                    'International Human Resource Management',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function hospitalityTourism(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Tourism & Hospitality Industry',
                    'Front Office Operations',
                    'Housekeeping Management',
                    'Food & Beverage Operations',
                ],
                'Semester 2' => [
                    'Travel & Tour Operations',
                    'Customer Service & Communication Skills',
                    'Hotel Accounting & Cost Control',
                    'Industrial Training / Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to Hospitality & Tourism',
                    'Hospitality Industry Structure',
                    'Tourism Management',
                    'Front Office Operations',
                ],
                'Semester 2' => [
                    'Food & Beverage Operations',
                    'Accommodation Operations',
                    'Customer Service Management',
                    'Travel & Tour Operations',
                ],
                'Semester 3' => [
                    'Event Management',
                    'Hospitality Marketing',
                    'Tourism Economics',
                    'Sustainable Tourism',
                ],
                'Semester 4' => [
                    'Hospitality Law & Ethics',
                    'Human Resource Management in Hospitality',
                    'Communication & Professional Skills',
                    'Industrial Training / Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Hospitality & Tourism Industry',
                    'Principles of Management',
                    'Tourism Geography',
                    'Food & Beverage Operations',
                ],
                'Semester 2' => [
                    'Front Office Operations',
                    'Business Communication Skills',
                    'Information Technology for Hospitality',
                ],
                'Semester 3' => [
                    'Hospitality Operations Management',
                    'Tourism Marketing',
                    'Housekeeping Management',
                    'Customer Service & Relationship Management',
                ],
                'Semester 4' => [
                    'Event Management',
                    'Human Resource Management for Hospitality',
                    'Research Methods for Business',
                ],
                'Semester 5' => [
                    'Strategic Hospitality & Tourism Management',
                    'Sustainable Tourism Development',
                    'Revenue & Yield Management',
                    'International Tourism Management',
                ],
                'Semester 6' => [
                    'Hospitality Law & Ethics',
                    'Entrepreneurship in Hospitality & Tourism',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function autocadMep(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to AutoCAD',
                    '2D Drafting in AutoCAD',
                    '3D Modeling in AutoCAD',
                    'Introduction to MEP (Mechanical, Electrical, Plumbing)',
                ],
                'Semester 2' => [
                    'MEP Coordination & Clash Detection',
                    'MEP Documentation & Schedules',
                    'Final Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to AutoCAD & Drafting',
                    'AutoCAD 2D Drafting Fundamentals',
                    'AutoCAD 3D Modeling Basics',
                    'Engineering Drawing Standards',
                ],
                'Semester 2' => [
                    'Architectural Drafting (Plans, Sections, Elevations)',
                    'Structural Drafting (RCC & Steel Drawings)',
                    'Introduction to MEP Systems',
                    'Mechanical Systems Drafting (HVAC Basics)',
                ],
                'Semester 3' => [
                    'Electrical Systems Drafting',
                    'Plumbing & Sanitary Systems Drafting',
                    'Fire Fighting Systems Drafting',
                    'MEP Coordination & Clash Detection',
                ],
                'Semester 4' => [
                    'BOQ & Quantity Basics for MEP',
                    'Building Codes & Standards (MEP)',
                    'Project Documentation & As-Built Drawings',
                    'Final Project / Industrial Training',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Fundamentals of Engineering Drawing',
                    'Introduction to AutoCAD',
                    'Basic Mathematics for Engineers',
                    'Computer Applications & Drafting Tools',
                ],
                'Semester 2' => [
                    'Advanced AutoCAD (2D Drafting)',
                    'Engineering Drawing & Standards',
                    'Electrical Systems – Basics',
                    'Mechanical Systems – Basics',
                ],
                'Semester 3' => [
                    'AutoCAD 3D Modeling',
                    'Plumbing & Drainage Systems',
                    'HVAC Systems – Fundamentals',
                    'Building Construction Technology',
                ],
                'Semester 4' => [
                    'Electrical Design & Layouts (MEP)',
                    'HVAC Design & Load Calculations',
                    'Fire Fighting Systems Design',
                    'Building Services Coordination',
                    'Advanced BIM for MEP (Revit / Navisworks)',
                ],
                'Semester 5' => [
                    'MEP Quantity Take-Off & Estimation',
                    'MEP Project Planning & Management',
                    'BIM Concepts (Revit for MEP – Basics)',
                ],
                'Semester 6' => [
                    'Health, Safety & Environment (HSE)',
                    'Sustainable & Green Building Services',
                    'Professional Practice & Ethics',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function civilEngineering(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Civil Engineering',
                    'Engineering Drawing & Basic AutoCAD',
                    'Building Materials & Construction Technology',
                ],
                'Semester 2' => [
                    'Surveying (Basic)',
                    'Structural Engineering (Basic)',
                    'Estimation, Costing & Site Practice',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Engineering Mathematics I',
                    'Engineering Physics',
                    'Engineering Chemistry',
                    'Introduction to Civil Engineering',
                    'Engineering Drawing & CAD',
                ],
                'Semester 2' => [
                    'Engineering Mathematics II',
                    'Strength of Materials',
                    'Building Materials & Construction',
                    'Surveying – I',
                    'Environmental Engineering – I',
                ],
                'Semester 3' => [
                    'Structural Mechanics',
                    'Concrete Technology',
                    'Surveying – II',
                    'Geotechnical Engineering – I (Soil Mechanics)',
                    'Fluid Mechanics',
                ],
                'Semester 4' => [
                    'Structural Analysis',
                    'Reinforced Concrete Design',
                    'Transportation Engineering',
                    'Environmental Engineering – II',
                    'Hydrology & Irrigation Engineering',
                ],
                'Semester 5' => [
                    'Steel Structure Design',
                    'Geotechnical Engineering – II (Foundation Engineering)',
                    'Construction Planning & Management',
                    'Quantity Surveying & Cost Estimation',
                ],
                'Semester 6' => [
                    'Advanced BIM for MEP (Revit / Navisworks)',
                    'Sustainable & Green Building Services',
                    'Professional Practice & Ethics',
                    'Final Project / Internship',
                ],
            ],
        ];
    }

    private function interiorDesigning(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Interior Designing',
                    'Principles of Interior Design & Space Planning',
                    'AutoCAD for Interior Design',
                    'Introduction to Revit Architecture',
                ],
                'Semester 2' => [
                    'Revit Modeling for Interior Design',
                    'Furniture, Lighting & Material Design in Revit',
                    '3D Visualization & Rendering (Basics)',
                    'Interior Design Project (Revit Based)',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to Interior Design',
                    'Elements & Principles of Design',
                    'Color Theory & Materials',
                    'Space Planning',
                ],
                'Semester 2' => [
                    'Furniture Design',
                    'Lighting Design',
                    'Residential Interior Design',
                    'Commercial Interior Design',
                ],
                'Semester 3' => [
                    'AutoCAD for Interior Design',
                    '3D Visualization (SketchUp / 3ds Max)',
                    'Building Materials & Construction',
                    'Interior Services (Electrical & Plumbing)',
                ],
                'Semester 4' => [
                    'Sustainable Interior Design',
                    'Cost Estimation & Project Management',
                    'Professional Practice & Portfolio Development',
                    'Final Project / Industrial Training',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Interior Design',
                    'Elements & Principles of Design',
                    'Color Theory',
                    'Drawing & Sketching Techniques',
                ],
                'Semester 2' => [
                    'Design History & Theory',
                    'Materials & Finishes – I',
                    'Computer Applications for Design',
                    'Communication & Study Skills',
                ],
                'Semester 3' => [
                    'Space Planning & Ergonomics',
                    'Furniture Design',
                    'Materials & Finishes – II',
                    'Lighting Design',
                ],
                'Semester 4' => [
                    'Building Construction for Interiors',
                    'CAD for Interior Design (AutoCAD)',
                    'Sustainable Interior Design',
                    'Professional Practice & Ethics',
                ],
                'Semester 5' => [
                    'Advanced Interior Design Studio',
                    '3D Visualization (SketchUp / 3ds Max)',
                    'Building Services for Interiors',
                    'Project Management for Interior Design',
                ],
                'Semester 6' => [
                    'Interior Detailing & Working Drawings',
                    'Cost Estimation & Budgeting',
                    'Research Methods for Design',
                    'Final Year Project',
                ],
            ],
        ];
    }

    private function quantitySurveying(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Introduction to Quantity Surveying',
                    'Building Materials & Construction Technology',
                    'Construction Drawings & Basic AutoCAD',
                ],
                'Semester 2' => [
                    'Building Measurement & Estimation',
                    'Cost Control & Tendering Basics',
                    'Contracts, Site Practice & Professional Skills',
                    'Final Project',
                ],
            ],
            'HND' => [
                'Semester 1' => [
                    'Introduction to Construction Technology & Quantity Surveying',
                    'Preparation of Bills of Quantities (BoQ) for Construction Works',
                    'Site Surveying & Levelling',
                    'Material & Construction Technology',
                ],
                'Semester 2' => [
                    'Estimating & Tendering / Bidding Process',
                    'Post Contract Management & Quantity Surveying Activities',
                    'Select Procurement Methods',
                    'Prepare Bidding Documents & Bids Submittals on behalf of Contractors',
                ],
                'Semester 3' => [
                    'Measurement Building Service & Civil Engineering',
                    'Prepare Preliminary Project Cost Estimates',
                    'Quantities in Roads & Highways',
                    'Application of Information Technology in Construction',
                ],
                'Semester 4' => [
                    'Business Statistics in QS',
                    'MEP Quantity Surveying',
                    'Civil Engineering Construction & Technology',
                    'Manage Workplace Information & Communication',
                    'Final Project / Industrial Training',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to Quantity Surveying',
                    'Construction Technology – I',
                    'Construction Materials',
                    'Measurement & Quantification – I',
                ],
                'Semester 2' => [
                    'Mathematics for Construction',
                    'Construction Drawing & CAD',
                    'Building Services Basics',
                    'Communication & Study Skills',
                ],
                'Semester 3' => [
                    'Measurement & Quantification – II',
                    'Construction Technology – II',
                    'Contract Law & Procurement',
                    'Estimating & Cost Planning',
                ],
                'Semester 4' => [
                    'Building Economics',
                    'Project Management Fundamentals',
                    'Building Services – Advanced',
                    'Health, Safety & Environmental Management',
                ],
                'Semester 5' => [
                    'Advanced Measurement & Estimating',
                    'Contract Administration & Practice',
                    'Construction Law & Dispute Resolution',
                    'Cost Control & Value Engineering',
                ],
                'Semester 6' => [
                    'Sustainable Construction',
                    'Risk Management in Construction',
                    'Research Methods for QS',
                    'Final Year Project',
                ],
            ],
        ];
    }
    private function english(): array
    {
        return [
            'Diploma' => [
                'Semester 1' => [
                    'Fundamentals of English Language',
                    'Reading Skills & Vocabulary Development',
                    'Writing Skills',
                    'Speaking & Pronunciation Skills',
                ],
                'Semester 2' => [
                    'Listening Skills & Communication',
                    'Functional & Professional English',
                    'Literature Appreciation',
                    'Final Project',
                ],
            ],
            'Degree' => [
                'Semester 1' => [
                    'Introduction to English Language Studies',
                    'Fundamentals of Linguistics',
                    'Grammar & Usage in English',
                    'Academic Reading & Writing',
                ],
                'Semester 2' => [
                    'Phonetics & Phonology',
                    'Morphology & Syntax',
                    'Listening & Speaking Skills',
                    'Study Skills & Academic Communication',
                ],
                'Semester 3' => [
                    'Semantics & Pragmatics',
                    'Discourse Analysis',
                    'English Literature – Introduction',
                    'Research Skills in Linguistics',
                ],
                'Semester 4' => [
                    'Sociolinguistics',
                    'Psycholinguistics',
                    'English Language Teaching – Basics',
                    'Professional Communication',
                ],
                'Semester 5' => [
                    'Applied Linguistics',
                    'English for Specific Purposes (ESP)',
                    'Language Assessment & Evaluation',
                    'Translation Studies',
                ],
                'Semester 6' => [
                    'Stylistics',
                    'Corpus Linguistics (Introductory)',
                    'Research Methodology',
                    'Final Year Project / Internship',
                ],
            ],
        ];
    }
}