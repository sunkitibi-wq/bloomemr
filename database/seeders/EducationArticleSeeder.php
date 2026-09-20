<?php

namespace Database\Seeders;

use App\Models\EducationArticle;
use Illuminate\Database\Seeder;

class EducationArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Understanding Hypertension',
                'category' => 'diagnosis',
                'content' => "High blood pressure (hypertension) is a common condition in which the long-term force of the blood against your artery walls is high enough that it may eventually cause health problems, such as heart disease.\n\n### Symptoms\nMost people with high blood pressure have no signs or symptoms, even if blood pressure readings reach dangerously high levels.\n\n### Prevention\n- Eat a healthy diet\n- Maintain a healthy weight\n- Be physically active\n- Limit alcohol and avoid tobacco",
                'video_url' => 'https://www.youtube.com/embed/5U7zB1q2e9U',
                'is_published' => true,
            ],
            [
                'title' => 'Managing Type 2 Diabetes',
                'category' => 'diagnosis',
                'content' => "Type 2 diabetes is a chronic condition that affects the way your body metabolizes sugar (glucose) — an important source of fuel for your body.\n\n### Key Management Steps\n1. Monitor your blood sugar regularly\n2. Adopt a balanced diet rich in fiber and low in refined carbs\n3. Engage in regular physical activity\n4. Take medications exactly as prescribed by your physician.",
                'video_url' => null,
                'is_published' => true,
            ],
            [
                'title' => 'How to Take Your Medications Properly',
                'category' => 'medication',
                'content' => "Taking your medication exactly as prescribed is critical for your health. Here are some tips to ensure you are doing it safely:\n\n- **Create a Routine:** Take your medication at the same time each day.\n- **Use a Pillbox:** This helps prevent missing doses.\n- **Never Share:** Do not take medications prescribed for someone else.\n- **Ask Questions:** If you experience side effects, contact your care provider immediately.",
                'video_url' => null,
                'is_published' => true,
            ],
            [
                'title' => 'General Nutrition and Diet',
                'category' => 'general',
                'content' => "A balanced diet is the cornerstone of good health. It provides your body with the nutrients it needs to function effectively.\n\n### What to Eat\n- Fruits and vegetables (make half your plate veggies!)\n- Whole grains\n- Lean proteins\n- Healthy fats\n\n### What to Avoid\n- Excess sodium (salt)\n- Added sugars\n- Saturated and trans fats",
                'video_url' => null,
                'is_published' => true,
            ],
            [
                'title' => 'Coping with Anxiety and Stress',
                'category' => 'diagnosis',
                'content' => "Anxiety and stress are natural responses to challenging situations, but chronic stress can negatively impact your health.\n\n### Coping Strategies\n- **Deep Breathing:** Practice 4-7-8 breathing exercises.\n- **Mindfulness:** Meditation and yoga can help ground your thoughts.\n- **Exercise:** Physical activity releases endorphins, natural stress relievers.\n- **Talk to Someone:** Don't hesitate to reach out to a therapist or counselor.",
                'video_url' => null,
                'is_published' => true,
            ],
        ];

        foreach ($articles as $articleData) {
            // Give all articles to practice_id 1 (the default seeded practice)
            $articleData['practice_id'] = 1;
            
            EducationArticle::firstOrCreate(
                ['title' => $articleData['title']],
                $articleData
            );
        }
    }
}
