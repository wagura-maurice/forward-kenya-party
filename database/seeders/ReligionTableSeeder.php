<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ReligionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the religion_types table
        \DB::table('religions')->delete();

        // Get religion types and categories for reference
        $types = \App\Models\ReligionType::pluck('id', 'name');
        $categories = \App\Models\ReligionCategory::pluck('id', 'name');

        // Define religions with their details
        $religions = [
            // Christianity (Monotheistic)
            [
                'type_id' => $types['Monotheistic'],
                'category_id' => $categories['Worship Services'],
                'name' => 'Christianity',
                'slug' => Str::slug('Christianity'),
                'description' => 'An Abrahamic monotheistic religion based on the life and teachings of Jesus Christ.',
                'configuration' => json_encode([
                    'denominations' => ['Catholicism', 'Protestantism', 'Orthodoxy'],
                    'sacred_text' => 'The Bible',
                    'holy_days' => ['Christmas', 'Easter', 'Good Friday'],
                    'practices' => ['Baptism', 'Communion', 'Prayer']
                ]),
            ],
            // Islam (Monotheistic)
            [
                'type_id' => $types['Monotheistic'],
                'category_id' => $categories['Charity & Donations'],
                'name' => 'Islam',
                'slug' => Str::slug('Islam'),
                'description' => 'An Abrahamic monotheistic religion teaching that there is only one God and that Muhammad is the messenger of God.',
                'configuration' => json_encode([
                    'denominations' => ['Sunni', 'Shia', 'Sufism'],
                    'sacred_text' => 'The Quran',
                    'five_pillars' => ['Shahada', 'Salat', 'Zakat', 'Sawm', 'Hajj'],
                    'holy_days' => ['Eid al-Fitr', 'Eid al-Adha', 'Ramadan']
                ]),
            ],
            // Hinduism (Dharmic)
            [
                'type_id' => $types['Dharmic'],
                'category_id' => $categories['Religious Education'],
                'name' => 'Hinduism',
                'slug' => Str::slug('Hinduism'),
                'description' => 'An Indian religion and dharma, or way of life, widely practiced in the Indian subcontinent.',
                'configuration' => json_encode([
                    'deities' => ['Brahma', 'Vishnu', 'Shiva'],
                    'sacred_texts' => ['Vedas', 'Upanishads', 'Bhagavad Gita'],
                    'concepts' => ['Dharma', 'Karma', 'Moksha'],
                    'festivals' => ['Diwali', 'Holi', 'Navaratri']
                ]),
            ],
            // Buddhism (Dharmic)
            [
                'type_id' => $types['Dharmic'],
                'category_id' => $categories['Religious Education'],
                'name' => 'Buddhism',
                'slug' => Str::slug('Buddhism'),
                'description' => 'A widespread Asian religion or philosophical tradition based on a series of original teachings attributed to Gautama Buddha.',
                'configuration' => json_encode([
                    'schools' => ['Theravada', 'Mahayana', 'Vajrayana'],
                    'noble_truths' => true,
                    'eightfold_path' => true,
                    'practices' => ['Meditation', 'Mindfulness', 'Puja']
                ]),
            ],
            // Judaism (Monotheistic)
            [
                'type_id' => $types['Monotheistic'],
                'category_id' => $categories['Worship Services'],
                'name' => 'Judaism',
                'slug' => Str::slug('Judaism'),
                'description' => 'An ancient monotheistic Abrahamic religion with the Torah as its foundational text.',
                'configuration' => json_encode([
                    'denominations' => ['Orthodox', 'Conservative', 'Reform', 'Reconstructionist'],
                    'sacred_texts' => ['Tanakh', 'Talmud'],
                    'holy_days' => ['Yom Kippur', 'Passover', 'Hanukkah'],
                    'practices' => ['Shabbat', 'Kashrut', 'Circumcision']
                ]),
            ],
            // Sikhism (Monotheistic)
            [
                'type_id' => $types['Monotheistic'],
                'category_id' => $categories['Community Service'],
                'name' => 'Sikhism',
                'slug' => Str::slug('Sikhism'),
                'description' => 'A monotheistic religion that originated in the Punjab region of the Indian subcontinent.',
                'configuration' => json_encode([
                    'sacred_text' => 'Guru Granth Sahib',
                    'five_ks' => ['Kesh', 'Kangha', 'Kara', 'Kachera', 'Kirpan'],
                    'gurdwara' => true,
                    'langar' => true
                ]),
            ],
            // Jainism (Dharmic)
            [
                'type_id' => $types['Dharmic'],
                'category_id' => $categories['Religious Education'],
                'name' => 'Jainism',
                'slug' => Str::slug('Jainism'),
                'description' => 'An ancient Indian religion that prescribes a path of non-violence towards all living beings.',
                'configuration' => json_encode([
                    'sects' => ['Digambara', 'Svetambara'],
                    'principles' => ['Ahimsa', 'Anekantavada', 'Aparigraha'],
                    'practices' => ['Vegetarianism', 'Fasting', 'Meditation']
                ]),
            ],
            // Shinto (East Asian)
            [
                'type_id' => $types['East Asian'],
                'category_id' => $categories['Religious Events'],
                'name' => 'Shinto',
                'slug' => Str::slug('Shinto'),
                'description' => 'A Japanese ethnic religion focusing on ritual practices to be carried out diligently to establish a connection between present-day Japan and its ancient past.',
                'configuration' => json_encode([
                    'kami' => true,
                    'shrines' => true,
                    'festivals' => ['Matsuri'],
                    'practices' => ['Omikuji', 'Ema', 'Kagura']
                ]),
            ],
            // Taoism (East Asian)
            [
                'type_id' => $types['East Asian'],
                'category_id' => $categories['Religious Education'],
                'name' => 'Taoism',
                'slug' => Str::slug('Taoism'),
                'description' => 'A Chinese philosophy and religion emphasizing living in harmony with the Tao (the Way).',
                'configuration' => json_encode([
                    'sacred_texts' => ['Tao Te Ching', 'Zhuangzi'],
                    'concepts' => ['Wu wei', 'Yin and yang', 'Qi'],
                    'practices' => ['Meditation', 'Feng shui', 'Tai chi']
                ]),
            ],
            // Zoroastrianism (Monotheistic)
            [
                'type_id' => $types['Monotheistic'],
                'category_id' => $categories['Worship Services'],
                'name' => 'Zoroastrianism',
                'slug' => Str::slug('Zoroastrianism'),
                'description' => 'One of the world\'s oldest continuously practiced religions, with a focus on the dualistic cosmology of good and evil.',
                'configuration' => json_encode([
                    'sacred_text' => 'Avesta',
                    'principles' => ['Good Thoughts', 'Good Words', 'Good Deeds'],
                    'practices' => ['Yasna', 'Navjote', 'Tower of Silence']
                ]),
            ]
        ];

        // Insert the religion types into the database using Eloquent
        foreach ($religions as $key => $religion) {
            Religion::create(array_merge($religion, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
