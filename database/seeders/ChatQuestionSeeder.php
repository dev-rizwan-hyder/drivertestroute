<?php

namespace Database\Seeders;

use App\Models\ChatQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'What are driver test routes?',
                'answer' => 'Driver test routes are pre-planned driving courses designed to help learners and test-takers practice real-world driving scenarios. Our routes are carefully mapped out to cover various road types, traffic conditions, and driving challenges that you\'ll encounter during your actual driving test.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'How do I access a purchased route?',
                'answer' => 'Once you\'ve purchased a route, you can access it from your "My Routes" dashboard. Simply log in to your account, navigate to "My Routes", select the route you want to practice, and click "Start Route". You can view the full route map, turn-by-turn instructions, and track your progress.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'How many times can I practice a route?',
                'answer' => 'The number of times you can practice a route depends on your subscription package. Each package includes a specific number of "starts" or access attempts. You can see your remaining starts in your account dashboard. Once you\'ve used all your starts, you can purchase additional access or upgrade your package.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept multiple payment methods including credit cards (Visa, Mastercard, American Express) and PayPal. During checkout, you\'ll be able to select your preferred payment method. All payments are processed securely through our trusted payment gateway.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Can I get a refund if I\'m not satisfied?',
                'answer' => 'Yes, we offer a satisfaction guarantee. If you\'re not satisfied with your purchased route within 7 days of purchase, you can request a full refund. Simply contact our support team with your order details and we\'ll process your refund promptly.',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Are the routes suitable for both learner and test drivers?',
                'answer' => 'Absolutely! Our routes are designed for all skill levels. Whether you\'re a beginner learner just starting out or an experienced driver preparing for your test, we have routes tailored to your level. Each route is labeled with its difficulty level to help you choose the right one.',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'How do I share my progress with an instructor?',
                'answer' => 'You can easily share your route completion details with your driving instructor. From your route history, click the "Share" button to generate a shareable link. Your instructor can view your practice data, including distance covered, time taken, and any notes you\'ve added about your experience.',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'question' => 'Do you offer routes in my city?',
                'answer' => 'We continuously expand our route library to cover more cities and regions. You can use our city filter on the "Browse Routes" page to see all available routes in your area. If your city isn\'t covered yet, feel free to request it through our contact form and we\'ll prioritize adding it to our platform.',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'question' => 'Can I download routes for offline use?',
                'answer' => 'Yes! Premium routes can be downloaded for offline use on your mobile device. Simply navigate to your purchased route, click the "Download" button, and the route map and instructions will be available offline. This is perfect for when you\'re practicing in areas with limited internet connectivity.',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'question' => 'How do I contact support if I have issues?',
                'answer' => 'We\'re here to help! You can reach our support team through multiple channels: visit our "Contact Us" page to submit a support ticket, email us directly at support@drivertestroute.com, or call our helpline during business hours. Most inquiries are answered within 24 hours.',
                'order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($questions as $question) {
            ChatQuestion::create($question);
        }
    }
}
