<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = [
            'Mobile Apps',
            'QA',
            'Full Stack',
            'Backend',
            'Frontend',
            'UI/UX Designer',
        ];

        foreach ($divisions as $division) {
            DB::table('divisions')->insert([
                'id' => Str::uuid(),
                'name' => $division,
            ]);
        }
    }
}
