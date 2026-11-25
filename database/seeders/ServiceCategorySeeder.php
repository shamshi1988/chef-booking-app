<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

/**
 * Starter catalogue of services. Safe to re-run: rows are matched by slug and updated.
 * Prices are SAMPLE values - edit them in the admin panel (Admin > Services).
 */
class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->services() as $order => $service) {
            ServiceCategory::updateOrCreate(
                ['slug' => $service['slug']],
                $service + ['sort_order' => $order + 1, 'is_active' => true]
            );
        }
    }

    private function services(): array
    {
        return [
            [
                'name' => 'Electrician',
                'slug' => 'electrician',
                'icon' => 'bolt',
                'tagline' => 'Wiring, switches, fans, lights & MCB repairs',
                'description' => 'Certified electricians for everything from a faulty switch to complete house wiring. Safe, quick and fairly priced.',
                'starting_price' => 149,
                'price_unit' => 'per visit',
                'is_featured' => true,
                'highlights' => ['Verified & trained electricians', 'Switch, socket & MCB repair', 'Fan, light & geyser installation', '30-day service warranty'],
                'form_fields' => [
                    ['key' => 'work_type', 'label' => 'What do you need?', 'type' => 'select', 'required' => true, 'options' => ['Switch / socket repair', 'Fan installation or repair', 'Light / fixture installation', 'MCB / fuse problem', 'Wiring / rewiring', 'Inverter / stabiliser', 'Other']],
                    ['key' => 'points', 'label' => 'Number of points / items', 'type' => 'number', 'required' => false, 'placeholder' => 'e.g. 3'],
                ],
            ],
            [
                'name' => 'Plumber',
                'slug' => 'plumber',
                'icon' => 'wrench',
                'tagline' => 'Leaks, taps, blockages, tanks & bathroom fittings',
                'description' => 'Experienced plumbers who fix leaks, blockages and fittings the first time, with all tools carried.',
                'starting_price' => 149,
                'price_unit' => 'per visit',
                'is_featured' => true,
                'highlights' => ['Leakage & blockage experts', 'Tap, shower & flush repair', 'Water tank & motor fitting', 'Transparent pricing'],
                'form_fields' => [
                    ['key' => 'work_type', 'label' => 'What do you need?', 'type' => 'select', 'required' => true, 'options' => ['Leakage repair', 'Tap / shower / mixer', 'Drain blockage', 'Toilet / flush repair', 'Water tank / motor', 'New bathroom fitting', 'Other']],
                ],
            ],
            [
                'name' => 'AC Repair & Service',
                'slug' => 'ac-repair-service',
                'icon' => 'snowflake',
                'tagline' => 'Service, gas refill, repair & installation',
                'description' => 'Keep cool with professional AC servicing, gas refill, repair, installation and uninstallation for all brands.',
                'starting_price' => 499,
                'price_unit' => 'per AC',
                'is_featured' => true,
                'highlights' => ['All brands, split & window', 'Jet-pump deep cleaning', 'Gas refill with pressure check', 'Installation & uninstallation'],
                'form_fields' => [
                    ['key' => 'service_type', 'label' => 'Service type', 'type' => 'select', 'required' => true, 'options' => ['Service / cleaning', 'Gas refill', 'Repair (not cooling, noise, leak)', 'Installation', 'Uninstallation']],
                    ['key' => 'ac_type', 'label' => 'AC type', 'type' => 'select', 'required' => true, 'options' => ['Split AC', 'Window AC', 'Cassette / central', 'Not sure']],
                    ['key' => 'units', 'label' => 'Number of ACs', 'type' => 'number', 'required' => true, 'placeholder' => 'e.g. 1'],
                ],
            ],
            [
                'name' => 'Physiotherapy',
                'slug' => 'physiotherapy',
                'icon' => 'heart',
                'tagline' => 'Certified physiotherapists at your home',
                'description' => 'Qualified physiotherapists for pain relief, post-surgery rehab, sports injuries and elderly care - in the comfort of your home.',
                'starting_price' => 699,
                'price_unit' => 'per session',
                'is_featured' => true,
                'highlights' => ['Qualified, experienced therapists', 'Personalised treatment plan', 'Home visits - no travel', 'Session packages available'],
                'form_fields' => [
                    ['key' => 'concern', 'label' => 'Main concern', 'type' => 'select', 'required' => true, 'options' => ['Back / neck pain', 'Knee / joint pain', 'Post-surgery rehabilitation', 'Sports injury', 'Stroke / neuro rehab', 'Elderly care & mobility', 'Other']],
                    ['key' => 'patient_age', 'label' => 'Patient age', 'type' => 'number', 'required' => true, 'placeholder' => 'e.g. 45'],
                    ['key' => 'sessions', 'label' => 'Sessions needed', 'type' => 'select', 'required' => false, 'options' => ['1 session (assessment)', '5 sessions', '10 sessions', 'Not sure yet']],
                    ['key' => 'therapist_gender', 'label' => 'Preferred therapist', 'type' => 'select', 'required' => false, 'options' => ['No preference', 'Female', 'Male']],
                ],
            ],
            [
                'name' => 'Private Chef',
                'slug' => 'private-chef',
                'icon' => 'chef',
                'tagline' => 'A professional chef cooks at your home',
                'description' => 'From intimate dinners to family celebrations, a private chef plans the menu, cooks, serves and cleans up.',
                'starting_price' => 1999,
                'price_unit' => 'per event',
                'is_featured' => true,
                'highlights' => ['Custom menu for your event', 'Shopping, cooking & serving', 'Dietary needs handled', 'Kitchen left spotless'],
                'form_fields' => [
                    ['key' => 'guest_count', 'label' => 'Number of guests', 'type' => 'number', 'required' => true, 'placeholder' => 'e.g. 6'],
                    ['key' => 'meal', 'label' => 'Occasion / meal', 'type' => 'select', 'required' => false, 'options' => ['Breakfast', 'Lunch', 'Dinner', 'Party / celebration', 'Daily cook (long term)']],
                    ['key' => 'cuisine', 'label' => 'Cuisine preferences', 'type' => 'multiselect', 'required' => false, 'options' => ['Indian', 'Chinese', 'Italian', 'Continental', 'Mexican', 'Thai']],
                ],
            ],
            [
                'name' => 'Home Cleaning',
                'slug' => 'home-cleaning',
                'icon' => 'sparkles',
                'tagline' => 'Deep cleaning for homes, kitchens & bathrooms',
                'description' => 'Trained cleaning teams with professional equipment for full-home, kitchen, bathroom and sofa deep cleaning.',
                'starting_price' => 399,
                'price_unit' => 'onwards',
                'is_featured' => true,
                'highlights' => ['Trained & background-checked staff', 'Eco-friendly chemicals', 'Machines & supplies included', 'Satisfaction guaranteed'],
                'form_fields' => [
                    ['key' => 'cleaning_type', 'label' => 'Cleaning type', 'type' => 'select', 'required' => true, 'options' => ['Full home deep cleaning', 'Kitchen cleaning', 'Bathroom cleaning', 'Sofa / carpet cleaning', 'Move-in / move-out cleaning']],
                    ['key' => 'home_size', 'label' => 'Home size', 'type' => 'select', 'required' => false, 'options' => ['1 BHK', '2 BHK', '3 BHK', '4 BHK+', 'Villa / independent house']],
                ],
            ],
            [
                'name' => 'Carpenter',
                'slug' => 'carpenter',
                'icon' => 'hammer',
                'tagline' => 'Furniture repair, assembly & fittings',
                'description' => 'Skilled carpenters for furniture repair and assembly, door and window fixes, and custom woodwork.',
                'starting_price' => 199,
                'price_unit' => 'per visit',
                'highlights' => ['Furniture assembly & repair', 'Door, lock & hinge fixes', 'Curtain rods & shelves', 'Custom woodwork on request'],
                'form_fields' => [
                    ['key' => 'work_type', 'label' => 'What do you need?', 'type' => 'select', 'required' => true, 'options' => ['Furniture repair', 'Furniture assembly', 'Door / lock / hinge', 'Shelves / curtain rods', 'Custom woodwork', 'Other']],
                ],
            ],
            [
                'name' => 'Appliance Repair',
                'slug' => 'appliance-repair',
                'icon' => 'appliance',
                'tagline' => 'Washing machine, fridge, RO, microwave & more',
                'description' => 'Repair and servicing for all major home appliances and brands, with genuine spare parts.',
                'starting_price' => 249,
                'price_unit' => 'per visit',
                'highlights' => ['All major brands', 'Genuine spare parts', 'Doorstep diagnosis', 'Repair warranty'],
                'form_fields' => [
                    ['key' => 'appliance', 'label' => 'Appliance', 'type' => 'select', 'required' => true, 'options' => ['Washing machine', 'Refrigerator', 'Water purifier (RO)', 'Microwave', 'Geyser', 'Television', 'Chimney', 'Other']],
                    ['key' => 'brand', 'label' => 'Brand', 'type' => 'text', 'required' => false, 'placeholder' => 'e.g. LG, Samsung'],
                ],
            ],
            [
                'name' => 'Pest Control',
                'slug' => 'pest-control',
                'icon' => 'bug',
                'tagline' => 'Cockroach, termite, bed bug & mosquito control',
                'description' => 'Safe, odourless and certified pest control treatments for homes and offices.',
                'starting_price' => 899,
                'price_unit' => 'onwards',
                'highlights' => ['Government-approved chemicals', 'Safe for kids & pets', 'Odourless treatment', 'Free follow-up visit'],
                'form_fields' => [
                    ['key' => 'pest', 'label' => 'Pest type', 'type' => 'multiselect', 'required' => true, 'options' => ['Cockroaches', 'Termites', 'Bed bugs', 'Mosquitoes', 'Rodents', 'Ants']],
                    ['key' => 'home_size', 'label' => 'Property size', 'type' => 'select', 'required' => false, 'options' => ['1 BHK', '2 BHK', '3 BHK', '4 BHK+', 'Office / commercial']],
                ],
            ],
            [
                'name' => 'Painting',
                'slug' => 'painting',
                'icon' => 'brush',
                'tagline' => 'Interior & exterior painting, waterproofing',
                'description' => 'Professional painters for a fresh look - interior, exterior, texture and waterproofing, with free inspection.',
                'starting_price' => null,
                'price_unit' => null,
                'highlights' => ['Free site inspection & quote', 'Branded paints', 'Furniture covered & cleaned up', 'On-time completion'],
                'form_fields' => [
                    ['key' => 'paint_type', 'label' => 'Painting type', 'type' => 'select', 'required' => true, 'options' => ['Interior', 'Exterior', 'Texture / design wall', 'Waterproofing', 'Wood polish']],
                    ['key' => 'home_size', 'label' => 'Area', 'type' => 'select', 'required' => false, 'options' => ['Single room', '1 BHK', '2 BHK', '3 BHK', '4 BHK+', 'Commercial']],
                ],
            ],
        ];
    }
}
