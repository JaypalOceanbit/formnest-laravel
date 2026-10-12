<?php

use App\Models\MasterFieldType;
use Illuminate\Database\Seeder;

class MasterFieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fieldTypes = [
            // Core Required Types
            [
                'name' => 'Short answer',
                'type_key' => 'short_answer',
                'category' => 'basic',
                'icon' => 'text-cursor-input',
                'description' => 'Single-line text input for student names, roll numbers, IDs, or short text',
                'default_config' => [
                    'label' => 'Short answer question',
                    'placeholder' => 'Enter short answer',
                    'required' => false,
                    'max_length' => 255,
                ],
                'sort_order' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Long answer',
                'type_key' => 'long_answer',
                'category' => 'basic',
                'icon' => 'align-left',
                'description' => 'Multi-line text area for descriptive feedback, essay answers, or detailed reasons',
                'default_config' => [
                    'label' => 'Long answer question',
                    'placeholder' => 'Enter your detailed response...',
                    'required' => false,
                    'rows' => 4,
                    'max_length' => 2000,
                ],
                'sort_order' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Multiple choice',
                'type_key' => 'multiple_choice',
                'category' => 'choice',
                'icon' => 'disc',
                'description' => 'Radio button list where respondents can pick exactly one option',
                'default_config' => [
                    'label' => 'Multiple choice question',
                    'required' => false,
                    'options' => ['Option 1', 'Option 2', 'Option 3'],
                    'allow_other' => false,
                ],
                'sort_order' => 3,
                'status' => 1,
            ],
            [
                'name' => 'Checkboxes',
                'type_key' => 'checkboxes',
                'category' => 'choice',
                'icon' => 'check-square',
                'description' => 'Square checkboxes allowing selection of multiple options',
                'default_config' => [
                    'label' => 'Checkboxes question',
                    'required' => false,
                    'options' => ['Option 1', 'Option 2', 'Option 3'],
                    'min_selection' => null,
                    'max_selection' => null,
                ],
                'sort_order' => 4,
                'status' => 1,
            ],
            [
                'name' => 'Dropdown',
                'type_key' => 'dropdown',
                'category' => 'choice',
                'icon' => 'chevron-down-circle',
                'description' => 'Compact select menu for departments, semesters, branches, or academic years',
                'default_config' => [
                    'label' => 'Dropdown question',
                    'placeholder' => 'Select an option',
                    'required' => false,
                    'options' => ['Option 1', 'Option 2', 'Option 3'],
                ],
                'sort_order' => 5,
                'status' => 1,
            ],
            [
                'name' => 'Number',
                'type_key' => 'number',
                'category' => 'basic',
                'icon' => 'hash',
                'description' => 'Strict numeric input for marks, credits, semester, age, or ranking',
                'default_config' => [
                    'label' => 'Number question',
                    'placeholder' => 'Enter number',
                    'required' => false,
                    'min' => null,
                    'max' => null,
                    'step' => 1,
                ],
                'sort_order' => 6,
                'status' => 1,
            ],
            [
                'name' => 'Date/Time',
                'type_key' => 'date_time',
                'category' => 'date_time',
                'icon' => 'calendar-clock',
                'description' => 'Combined date and time picker for event slots, appointment booking, or deadlines',
                'default_config' => [
                    'label' => 'Date and Time',
                    'required' => false,
                    'format' => 'YYYY-MM-DD HH:mm',
                ],
                'sort_order' => 7,
                'status' => 1,
            ],
            [
                'name' => 'File upload',
                'type_key' => 'file_upload',
                'category' => 'media',
                'icon' => 'upload-cloud',
                'description' => 'Upload student assignments, fee receipts, identity cards, or project reports',
                'default_config' => [
                    'label' => 'File upload',
                    'required' => false,
                    'max_file_size_mb' => 10,
                    'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'png'],
                    'max_files' => 1,
                ],
                'sort_order' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Rating',
                'type_key' => 'rating',
                'category' => 'evaluation',
                'icon' => 'star',
                'description' => 'Star or heart rating scale for professor evaluations and satisfaction scoring',
                'default_config' => [
                    'label' => 'Rating question',
                    'required' => false,
                    'max_stars' => 5,
                    'shape' => 'star',
                ],
                'sort_order' => 9,
                'status' => 1,
            ],

            // Other Useful College Form Components
            [
                'name' => 'Email',
                'type_key' => 'email',
                'category' => 'basic',
                'icon' => 'mail',
                'description' => 'Validated email address field, with optional institutional domain filter',
                'default_config' => [
                    'label' => 'College Email Address',
                    'placeholder' => 'student@college.edu',
                    'required' => true,
                    'domain_restriction' => null,
                ],
                'sort_order' => 10,
                'status' => 1,
            ],
            [
                'name' => 'Phone',
                'type_key' => 'phone',
                'category' => 'basic',
                'icon' => 'phone',
                'description' => 'Phone / WhatsApp contact number with formatting validation',
                'default_config' => [
                    'label' => 'Mobile Number',
                    'placeholder' => '+91 9876543210',
                    'required' => false,
                ],
                'sort_order' => 11,
                'status' => 1,
            ],
            [
                'name' => 'Date',
                'type_key' => 'date',
                'category' => 'date_time',
                'icon' => 'calendar',
                'description' => 'Date selector for birth dates, joining dates, or event dates',
                'default_config' => [
                    'label' => 'Date',
                    'required' => false,
                    'format' => 'YYYY-MM-DD',
                ],
                'sort_order' => 12,
                'status' => 1,
            ],
            [
                'name' => 'Time',
                'type_key' => 'time',
                'category' => 'date_time',
                'icon' => 'clock',
                'description' => 'Time picker for schedule hours, interview slots, or class timing',
                'default_config' => [
                    'label' => 'Time',
                    'required' => false,
                    'format' => 'HH:mm',
                ],
                'sort_order' => 13,
                'status' => 1,
            ],
            [
                'name' => 'Linear scale',
                'type_key' => 'linear_scale',
                'category' => 'evaluation',
                'icon' => 'sliders',
                'description' => 'Numerical range slider/radio scale (1-5 or 1-10) with custom low/high labels',
                'default_config' => [
                    'label' => 'How would you rate this course?',
                    'required' => false,
                    'min' => 1,
                    'max' => 5,
                    'min_label' => 'Poor',
                    'max_label' => 'Excellent',
                ],
                'sort_order' => 14,
                'status' => 1,
            ],
            [
                'name' => 'Signature',
                'type_key' => 'signature',
                'category' => 'advanced',
                'icon' => 'pen-tool',
                'description' => 'Digital touch/mouse signature drawing pad for student undertakings & consent',
                'default_config' => [
                    'label' => 'Student Digital Signature',
                    'required' => false,
                    'declaration' => 'I hereby certify that all information submitted is true and correct.',
                ],
                'sort_order' => 15,
                'status' => 1,
            ],
            [
                'name' => 'Section heading',
                'type_key' => 'heading',
                'category' => 'layout',
                'icon' => 'heading',
                'description' => 'Section divider and title header for multi-part forms',
                'default_config' => [
                    'title' => 'Personal Information',
                    'description' => 'Please fill out your verified details below',
                ],
                'sort_order' => 16,
                'status' => 1,
            ],
            [
                'name' => 'Notice / Instructions',
                'type_key' => 'paragraph',
                'category' => 'layout',
                'icon' => 'file-text',
                'description' => 'Static informational text or exam/assignment guidelines',
                'default_config' => [
                    'content' => 'Ensure all answers are submitted before the deadline.',
                ],
                'sort_order' => 17,
                'status' => 1,
            ],
            [
                'name' => 'Website / URL',
                'type_key' => 'url',
                'category' => 'basic',
                'icon' => 'link',
                'description' => 'URL link for GitHub repositories, portfolios, LinkedIn profiles, or Google Drive',
                'default_config' => [
                    'label' => 'Project / GitHub Link',
                    'placeholder' => 'https://github.com/username/project',
                    'required' => false,
                ],
                'sort_order' => 18,
                'status' => 1,
            ],
        ];

        foreach ($fieldTypes as $data) {
            MasterFieldType::updateOrCreate(
                ['type_key' => $data['type_key']],
                $data
            );
        }
    }
}
