<?php

use App\Models\MasterTemplate;
use App\Models\MasterTemplateCategory;
use Illuminate\Database\Seeder;

class MasterTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $feedbackCat = MasterTemplateCategory::where('slug', 'feedback-and-evaluations')->first();
        $registrationCat = MasterTemplateCategory::where('slug', 'registrations-and-admissions')->first();
        $eventCat = MasterTemplateCategory::where('slug', 'events-and-workshops')->first();
        $assignmentCat = MasterTemplateCategory::where('slug', 'assignments-and-submissions')->first();
        $surveyCat = MasterTemplateCategory::where('slug', 'campus-surveys-and-polls')->first();

        $templates = [
            [
                'name' => 'Faculty & Course Feedback Form',
                'slug' => 'faculty-and-course-feedback-form',
                'description' => 'Comprehensive student evaluation of teaching effectiveness, course syllabus coverage, and laboratory sessions.',
                'category_id' => $feedbackCat ? $feedbackCat->id : 1,
                'preview_image' => 'faculty_feedback.png',
                'is_featured' => true,
                'sort_order' => 1,
                'status' => 1,
                'json' => [
                    'version' => '1.0',
                    'title' => 'End-Semester Faculty & Course Evaluation',
                    'description' => 'Your honest feedback helps us improve teaching quality and curriculum design.',
                    'settings' => [
                        'allow_anonymous' => true,
                        'submit_btn_text' => 'Submit Feedback',
                        'confirmation_message' => 'Thank you! Your feedback has been recorded securely.',
                    ],
                    'fields' => [
                        [
                            'id' => 'field_1',
                            'type' => 'dropdown',
                            'label' => 'Academic Department',
                            'required' => true,
                            'options' => ['Computer Engineering', 'Information Technology', 'Mechanical Engineering', 'Electronics & Comm.'],
                        ],
                        [
                            'id' => 'field_2',
                            'type' => 'short_answer',
                            'label' => 'Course Title / Code',
                            'placeholder' => 'e.g. CS401 - Database Management Systems',
                            'required' => true,
                        ],
                        [
                            'id' => 'field_3',
                            'type' => 'short_answer',
                            'label' => 'Professor Name',
                            'placeholder' => 'Enter instructor name',
                            'required' => true,
                        ],
                        [
                            'id' => 'field_4',
                            'type' => 'linear_scale',
                            'label' => 'Punctuality and syllabus completion',
                            'min' => 1,
                            'max' => 5,
                            'min_label' => 'Poor',
                            'max_label' => 'Excellent',
                            'required' => true,
                        ],
                        [
                            'id' => 'field_5',
                            'type' => 'rating',
                            'label' => 'Clarity in explaining complex topics',
                            'max_stars' => 5,
                            'required' => true,
                        ],
                        [
                            'id' => 'field_6',
                            'type' => 'long_answer',
                            'label' => 'Constructive suggestions for improvement',
                            'placeholder' => 'Share any constructive comments...',
                            'required' => false,
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Student Club & Society Membership Form',
                'slug' => 'student-club-membership-form',
                'description' => 'Registration form for university coding, robotics, cultural, and sports clubs.',
                'category_id' => $registrationCat ? $registrationCat->id : 2,
                'preview_image' => 'club_registration.png',
                'is_featured' => true,
                'sort_order' => 2,
                'status' => 1,
                'json' => [
                    'version' => '1.0',
                    'title' => 'College Coding & Robotics Club Registration',
                    'description' => 'Join the developer community! Showcase your passion and build real-world projects.',
                    'settings' => [
                        'allow_anonymous' => false,
                        'submit_btn_text' => 'Join Club',
                        'confirmation_message' => 'Application received! You will receive interview details via email.',
                    ],
                    'fields' => [
                        [
                            'id' => 'reg_1',
                            'type' => 'short_answer',
                            'label' => 'Full Name',
                            'placeholder' => 'Enter full student name',
                            'required' => true,
                        ],
                        [
                            'id' => 'reg_2',
                            'type' => 'short_answer',
                            'label' => 'Enrollment / Student ID',
                            'placeholder' => 'e.g. 210101001',
                            'required' => true,
                        ],
                        [
                            'id' => 'reg_3',
                            'type' => 'email',
                            'label' => 'College Email ID',
                            'placeholder' => 'student@college.edu',
                            'required' => true,
                        ],
                        [
                            'id' => 'reg_4',
                            'type' => 'phone',
                            'label' => 'WhatsApp Number',
                            'placeholder' => '+91 9876543210',
                            'required' => true,
                        ],
                        [
                            'id' => 'reg_5',
                            'type' => 'checkboxes',
                            'label' => 'Areas of Interest',
                            'required' => true,
                            'options' => ['Web Development', 'AI / Machine Learning', 'App Development', 'IoT & Embedded Systems', 'UI/UX Design', 'Competitive Programming'],
                        ],
                        [
                            'id' => 'reg_6',
                            'type' => 'url',
                            'label' => 'GitHub Profile / Portfolio Link',
                            'placeholder' => 'https://github.com/yourhandle',
                            'required' => false,
                        ],
                        [
                            'id' => 'reg_7',
                            'type' => 'long_answer',
                            'label' => 'Why do you want to join this club?',
                            'placeholder' => 'Write a short motivation statement...',
                            'required' => true,
                        ],
                    ],
                ],
            ],
            [
                'name' => 'College Hackathon Team Registration',
                'slug' => 'college-hackathon-team-registration',
                'description' => '36-hour Inter-College Hackathon registration with team member details and problem statements.',
                'category_id' => $eventCat ? $eventCat->id : 3,
                'preview_image' => 'hackathon.png',
                'is_featured' => false,
                'sort_order' => 3,
                'status' => 1,
                'json' => [
                    'version' => '1.0',
                    'title' => 'ByteHacks 2026 - Team Registration',
                    'description' => 'Register your 2-4 member team for the annual innovation hackathon.',
                    'settings' => [
                        'allow_anonymous' => false,
                        'submit_btn_text' => 'Register Team',
                        'confirmation_message' => 'Team registered successfully! Check your email for badge passes.',
                    ],
                    'fields' => [
                        [
                            'id' => 'hack_1',
                            'type' => 'short_answer',
                            'label' => 'Team Name',
                            'placeholder' => 'Enter innovative team name',
                            'required' => true,
                        ],
                        [
                            'id' => 'hack_2',
                            'type' => 'dropdown',
                            'label' => 'Chosen Track / Theme',
                            'required' => true,
                            'options' => ['EdTech & Smart Campus', 'Healthcare & Biotech', 'FinTech & Blockchain', 'Sustainability & CleanTech', 'Open Innovation'],
                        ],
                        [
                            'id' => 'hack_3',
                            'type' => 'short_answer',
                            'label' => 'Team Leader Name & Contact',
                            'placeholder' => 'Name, Phone, Email',
                            'required' => true,
                        ],
                        [
                            'id' => 'hack_4',
                            'type' => 'number',
                            'label' => 'Number of Team Members',
                            'required' => true,
                            'min' => 2,
                            'max' => 4,
                        ],
                        [
                            'id' => 'hack_5',
                            'type' => 'file_upload',
                            'label' => 'Project Abstract / PPT (Optional)',
                            'required' => false,
                            'allowed_extensions' => ['pdf', 'ppt', 'pptx'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Capstone Project Final Submission',
                'slug' => 'capstone-project-final-submission',
                'description' => 'Final semester project code repository, documentation PDF, and guide signoff form.',
                'category_id' => $assignmentCat ? $assignmentCat->id : 4,
                'preview_image' => 'project_submission.png',
                'is_featured' => false,
                'sort_order' => 4,
                'status' => 1,
                'json' => [
                    'version' => '1.0',
                    'title' => 'Final Year Capstone Project Submission',
                    'description' => 'Official submission portal for degree dissertation and project viva evaluation.',
                    'settings' => [
                        'allow_anonymous' => false,
                        'submit_btn_text' => 'Submit Capstone Deliverables',
                        'confirmation_message' => 'Project submitted to examination department.',
                    ],
                    'fields' => [
                        [
                            'id' => 'proj_1',
                            'type' => 'short_answer',
                            'label' => 'Project Title',
                            'placeholder' => 'Enter approved project title',
                            'required' => true,
                        ],
                        [
                            'id' => 'proj_2',
                            'type' => 'short_answer',
                            'label' => 'Faculty Guide Name',
                            'placeholder' => 'Prof. / Dr. ...',
                            'required' => true,
                        ],
                        [
                            'id' => 'proj_3',
                            'type' => 'url',
                            'label' => 'GitHub Repository Link (Public/Shared)',
                            'placeholder' => 'https://github.com/...',
                            'required' => true,
                        ],
                        [
                            'id' => 'proj_4',
                            'type' => 'file_upload',
                            'label' => 'Final Dissertation Report (PDF)',
                            'required' => true,
                            'allowed_extensions' => ['pdf'],
                        ],
                        [
                            'id' => 'proj_5',
                            'type' => 'signature',
                            'label' => 'Student Undertaking',
                            'declaration' => 'I hereby state that this project work is original and free from plagiarism.',
                            'required' => true,
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Campus Facility & Hostel Satisfaction Survey',
                'slug' => 'campus-facility-satisfaction-survey',
                'description' => 'Gather feedback on library resources, Wi-Fi connectivity, cafeteria, and hostel amenities.',
                'category_id' => $surveyCat ? $surveyCat->id : 5,
                'preview_image' => 'campus_survey.png',
                'is_featured' => false,
                'sort_order' => 5,
                'status' => 1,
                'json' => [
                    'version' => '1.0',
                    'title' => 'Annual Campus Facilities Survey',
                    'description' => 'Help college administration upgrade campus infrastructure.',
                    'settings' => [
                        'allow_anonymous' => true,
                        'submit_btn_text' => 'Submit Survey',
                        'confirmation_message' => 'Responses submitted to the administration council.',
                    ],
                    'fields' => [
                        [
                            'id' => 'surv_1',
                            'type' => 'multiple_choice',
                            'label' => 'Are you a Day Scholar or Hostel Resident?',
                            'required' => true,
                            'options' => ['Day Scholar', 'Hostel Resident'],
                        ],
                        [
                            'id' => 'surv_2',
                            'type' => 'rating',
                            'label' => 'Campus Wi-Fi speed and coverage',
                            'max_stars' => 5,
                            'required' => true,
                        ],
                        [
                            'id' => 'surv_3',
                            'type' => 'rating',
                            'label' => 'Library book collection and quiet study space',
                            'max_stars' => 5,
                            'required' => true,
                        ],
                        [
                            'id' => 'surv_4',
                            'type' => 'rating',
                            'label' => 'Canteen hygiene and food quality',
                            'max_stars' => 5,
                            'required' => true,
                        ],
                        [
                            'id' => 'surv_5',
                            'type' => 'long_answer',
                            'label' => 'What facility improvement is your highest priority?',
                            'placeholder' => 'Tell us what could be better...',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($templates as $t) {
            MasterTemplate::updateOrCreate(
                ['slug' => $t['slug']],
                $t
            );
        }
    }
}
