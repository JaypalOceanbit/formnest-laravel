<?php

use App\Models\MasterTemplateCategory;
use Illuminate\Database\Seeder;

class MasterTemplateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Feedback & Evaluations',
                'slug' => 'feedback-and-evaluations',
                'description' => 'Course surveys, faculty appraisal, peer reviews, and lecture feedback forms.',
                'icon' => 'message-square',
                'sort_order' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Registrations & Admissions',
                'slug' => 'registrations-and-admissions',
                'description' => 'Club memberships, semester course registrations, and college event admissions.',
                'icon' => 'user-plus',
                'sort_order' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Events & Workshops',
                'slug' => 'events-and-workshops',
                'description' => 'Tech fests, hackathons, guest lectures, webinars, and cultural participation forms.',
                'icon' => 'calendar',
                'sort_order' => 3,
                'status' => 1,
            ],
            [
                'name' => 'Assignments & Submissions',
                'slug' => 'assignments-and-submissions',
                'description' => 'Homework, lab records, project reports, capstone deliverables, and code repositories.',
                'icon' => 'file-text',
                'sort_order' => 4,
                'status' => 1,
            ],
            [
                'name' => 'Campus Surveys & Polls',
                'slug' => 'campus-surveys-and-polls',
                'description' => 'Campus hostel, canteen, library, placement cell, and general student satisfaction polls.',
                'icon' => 'bar-chart-2',
                'sort_order' => 5,
                'status' => 1,
            ],
            [
                'name' => 'Administrative & Requests',
                'slug' => 'administrative-and-requests',
                'description' => 'Leave applications, bonafide requests, NOC forms, and grievance redressals.',
                'icon' => 'clipboard-check',
                'sort_order' => 6,
                'status' => 1,
            ],
        ];

        foreach ($categories as $cat) {
            MasterTemplateCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
