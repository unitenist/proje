<?php

namespace App\Support;

use App\Models\Setting;

class SiteContent
{
    /**
     * Section registry consumed by the admin panel: key => label.
     */
    public static function sections(): array
    {
        return [
            'site' => 'Site Ayarları (Logo & Profil Fotoğrafı)',
            'assistant' => 'AI Robot Asistan',
            'home' => 'Ana Sayfa (Hero)',
            'home_skills' => 'Teknolojiler / Yetkinlikler',
            'home_testimonials' => 'Referanslar',
            'about' => 'Hakkımızda',
            'approach' => 'Yaklaşımımız',
            'services' => 'Hizmetler',
            'lab' => 'AI Lab / Projeler',
            'contact' => 'İletişim',
        ];
    }

    public static function siteDefaults(): array
    {
        return [
            'logo' => null,
            'profile_photo' => null,
            'profile_name' => 'UNITENIST',
            'profile_title' => 'AI · ERP · Otomasyon Danışmanlığı',
        ];
    }

    public static function assistantDefaults(): array
    {
        return [
            'enabled' => true,
            'greeting' => 'Hoşgeldiniz! AI teknolojilerini birlikte keşfedelim 👋',
            // Branching question tree: each question's answer (Evet/Hayır) decides the NEXT question via
            // yes_next/no_next (0-based index into this same list). Use -1 to end the flow at that answer.
            'questions' => [
                ['text' => 'Şu anda bir ERP veya operasyon sisteminiz var mı?', 'yes_next' => 1, 'no_next' => 2],
                ['text' => 'Mevcut ERP sisteminizi yapay zekâ ile entegre etmek ister misiniz?', 'yes_next' => 3, 'no_next' => 5],
                ['text' => 'Yakın zamanda yeni bir ERP veya otomasyon sistemi kurmayı düşünüyor musunuz?', 'yes_next' => 4, 'no_next' => 5],
                ['text' => 'Öncelikli olarak stok ve tahsilat süreçlerinde otomasyon ister misiniz?', 'yes_next' => 6, 'no_next' => 6],
                ['text' => 'Kurulum sürecinde baştan AI destekli bir mimari tercih eder misiniz?', 'yes_next' => 6, 'no_next' => 6],
                ['text' => 'Verilerinizle doğal dilde konuşup anlık rapor almak ister misiniz?', 'yes_next' => 6, 'no_next' => 6],
                ['text' => 'Bir AI Agent projesi için ekibimizle görüşmek ister misiniz?', 'yes_next' => -1, 'no_next' => -1],
            ],
        ];
    }

    public static function homeDefaults(): array
    {
        return [
            'seo_title' => 'UNITENIST | AI, ERP ve İş Süreçleri Otomasyonu',
            'seo_description' => 'UNITENIST, ERP sistemlerini yapay zekâ ile buluşturan AI entegrasyon, AI agent ve iş süreçleri otomasyonu çözümleri geliştirir.',
            'kicker' => 'AI · ERP · AUTOMATION',
            'title' => 'İşletmenizi yapay zekâ ile konuşturun.',
            'title_highlight' => 'yapay zekâ ile',
            'subtitle' => 'UNITENIST; mevcut ERP, e-ticaret ve operasyon sistemlerini yapay zekâ ile senkronize eder. Verinizi sadece raporlayan değil, iş süreçlerinizde çalışan AI sistemleri tasarlar.',
            'image' => null,
            'cta_primary_text' => 'AI dönüşümünü konuşalım →',
            'cta_primary_href' => '/iletisim',
            'cta_secondary_text' => 'Nasıl çalışıyoruz?',
            'cta_secondary_href' => '/yaklasimimiz',
        ];
    }

    public static function homeSkillsDefaults(): array
    {
        return [
            'seo_title' => 'Teknolojiler ve Yetkinlikler | UNITENIST',
            'seo_description' => 'UNITENIST’in ERP entegrasyonu, AI agent geliştirme ve otomasyon alanlarındaki teknik yetkinlikleri.',
            'kicker' => 'Teknolojiler',
            'title' => 'Kullandığımız teknolojiler ve yetkinlik seviyemiz.',
            'items' => [
                ['logo' => null, 'name' => 'ERP Entegrasyonu', 'percentage' => 92],
                ['logo' => null, 'name' => 'AI Agent Geliştirme', 'percentage' => 88],
                ['logo' => null, 'name' => 'API & Sistem Entegrasyonu', 'percentage' => 90],
                ['logo' => null, 'name' => 'Business Automation', 'percentage' => 85],
                ['logo' => null, 'name' => 'Veri & Raporlama', 'percentage' => 80],
                ['logo' => null, 'name' => 'Güvenli AI Mimarisi', 'percentage' => 83],
            ],
        ];
    }

    public static function homeTestimonialsDefaults(): array
    {
        return [
            'seo_title' => 'Referanslar ve Müşteri Yorumları | UNITENIST',
            'seo_description' => 'UNITENIST ile çalışan işletmelerin AI ve ERP entegrasyonu deneyimleri.',
            'kicker' => 'Referanslar',
            'title' => 'Bizimle çalışanlar ne diyor?',
            'items' => [
                ['quote' => "UNITENIST ERP'mizi değiştirmeden AI'ı sürece dahil etti. Süreçlerimiz artık kendi kendine raporlanıyor.", 'author' => 'Operasyon Direktörü', 'role' => 'Üretim Sektörü'],
                ['quote' => 'Kurulan AI agent sayesinde stok ve tahsilat takibi neredeyse otomatik hale geldi.', 'author' => 'Finans Yöneticisi', 'role' => 'Perakende'],
                ['quote' => 'Karmaşık entegrasyon süreçlerini net adımlarla, güvenli biçimde yönettiler.', 'author' => 'CTO', 'role' => 'E-ticaret'],
            ],
        ];
    }

    public static function aboutDefaults(): array
    {
        return [
            'seo_title' => 'Hakkımızda | UNITENIST',
            'seo_description' => 'UNITENIST, ERP ve operasyon sistemlerini yapay zekâ ile yeniden yorumlayan bağımsız bir teknoloji markasıdır. Kurucu: Sermet Sandıkçı.',
            'kicker' => 'UNITENIST',
            'title' => "İstanbul'dan dünyaya teknoloji.",
            'paragraphs' => [
                ['text' => 'UNITENIST, yazılım ve işletme sistemleri deneyimini yapay zekâ ile yeniden yorumlayan bağımsız bir teknoloji markasıdır.'],
                ['text' => "Kurucusu Sermet Sandıkçı'nın odağı; işletmelerin kullandığı ERP ve operasyon sistemlerini anlayan, güvenli biçimde bu sistemlere bağlanan ve gerçek iş üreten AI çözümleri geliştirmektir."],
            ],
            'quote' => 'Hedefimiz daha fazla yazılım üretmek değil; yazılımın işletme adına daha fazla iş yapmasını sağlamak.',
            'cta_text' => 'Bizimle çalışın →',
            'cta_href' => '/iletisim',
        ];
    }

    public static function approachDefaults(): array
    {
        return [
            'seo_title' => 'Yaklaşımımız | UNITENIST',
            'seo_description' => "UNITENIST'in AI dönüşüm yaklaşımı: analiz, tasarım, entegrasyon ve ölçüm adımlarıyla ERP'nizi yapay zekâ ile buluşturuyoruz.",
            'kicker' => 'Yaklaşımımız',
            'title' => "Önce sistemi anlarız. Sonra AI'ı çalıştırırız.",
            'subtitle' => 'AI projesini bir chatbot kurulumu olarak değil, işletmenin mevcut teknoloji mimarisinin dönüşümü olarak ele alıyoruz.',
            'steps' => [
                ['label' => 'ANALİZ', 'title' => 'Mevcut sistemi keşfet', 'description' => 'ERP, API, veri kaynakları ve kritik iş süreçlerini haritalandırıyoruz. Hangi verinin nerede, hangi formatta yaşadığını çıkarıyoruz.'],
                ['label' => 'TASARIM', 'title' => 'AI fırsatlarını belirle', 'description' => 'Hangi süreçlerin AI ile hızlanacağını, hangi kararların otomatikleştirilebileceğini ve hangi verinin nerede kullanılacağını çıkarıyoruz.'],
                ['label' => 'ENTEGRASYON', 'title' => "AI'ı sisteme bağla", 'description' => 'Tool calling, API, RAG ve kontrollü agent mimarileriyle AI katmanını mevcut sisteminize güvenli şekilde entegre ediyoruz.'],
                ['label' => 'ÖLÇ', 'title' => 'Sonucu takip et', 'description' => 'Zaman, maliyet, işlem hacmi ve hata oranı gibi metriklerle dönüşümü ölçüyoruz; sürekli iyileştiriyoruz.'],
            ],
            'cta_text' => 'Projenizi konuşalım →',
            'cta_href' => '/iletisim',
        ];
    }

    public static function servicesDefaults(): array
    {
        return [
            'seo_title' => 'Hizmetler | UNITENIST',
            'seo_description' => 'AI ERP entegrasyonu, AI agent geliştirme, sistem entegrasyonu ve iş süreçleri otomasyonu hizmetlerimizi keşfedin.',
            'kicker' => 'Hizmetler',
            'title' => "AI'ı işletmenizin gerçek verisiyle buluşturuyoruz.",
            'subtitle' => 'Yeni bir ERP dayatmak yerine mevcut sistemlerinizi anlayıp, onların üzerine güvenli ve ölçülebilir yapay zekâ katmanları kuruyoruz.',
            'items' => [
                ['icon' => 'AI ERP INTEGRATION', 'title' => 'AI ERP Integration', 'description' => 'ERP verilerinizi doğal dil, analiz ve kontrollü AI aksiyonlarıyla erişilebilir hale getiriyoruz. SAP, Netsis, Logo, Odoo gibi sistemlerinize doğrudan bağlanan bir AI katmanı kuruyoruz.'],
                ['icon' => 'AI AGENTS', 'title' => 'AI Agents', 'description' => 'Satış, tahsilat, stok, raporlama ve operasyon süreçlerinde görev yapan, tool calling ile gerçek işlem yapabilen yapay zekâ ajanları geliştiriyoruz.'],
                ['icon' => 'SİSTEM ENTEGRASYONU', 'title' => 'Sistem Entegrasyonu', 'description' => 'ERP, CRM, e-ticaret ve özel yazılımlar arasında güvenilir API ve veri akışları kuruyoruz; sistemleriniz birbiriyle gerçek zamanlı konuşur hale gelir.'],
                ['icon' => 'BUSINESS AUTOMATION', 'title' => 'Business Automation', 'description' => 'Tekrarlayan manuel işleri otomatikleştirerek ekiplerin zamanını daha değerli işlere taşıyoruz; onay akışları, raporlama ve veri girişi gibi süreçleri sadeleştiriyoruz.'],
                ['icon' => 'VERİ & RAPORLAMA', 'title' => 'Veri & Raporlama', 'description' => 'Dağınık verilerinizi tek bir doğru kaynağa taşıyıp, yöneticilerin doğal dille soru sorup anında yanıt aldığı raporlama katmanları kuruyoruz.'],
                ['icon' => 'GÜVENLİ AI MİMARİSİ', 'title' => 'Güvenli AI Mimarisi', 'description' => 'Yetkilendirme, denetim izleri ve kontrollü aksiyon sınırlarıyla; AI’nin sisteminizde ne yapıp yapamayacağını net kurallarla tanımlıyoruz.'],
            ],
        ];
    }

    public static function labDefaults(): array
    {
        return [
            'seo_title' => 'AI Lab / Projeler | UNITENIST',
            'seo_description' => 'UNITENIST AI Lab: gerçek işletme problemlerini AI agent, veri erişimi, otomasyon ve güvenli entegrasyon mimarileriyle çözüyoruz.',
            'kicker' => 'UNITENIST AI LAB',
            'title' => "ERP'nin geleceğini bugün deniyoruz.",
            'subtitle' => 'Gerçek işletme problemlerini AI agent, veri erişimi, otomasyon ve güvenli entegrasyon mimarileriyle çözüyoruz.',
            'quote' => "ERP'nizi değiştirmek zorunda değilsiniz. Onu yapay zekâ çağına taşıyabilirsiniz.",
            'cta_text' => 'Kendi senaryonuzu konuşalım →',
            'cta_href' => '/iletisim',
            'projects' => [
                ['tag' => 'ERP AGENT', 'title' => 'Stok & Tahsilat Ajanı', 'description' => 'ERP üzerindeki stok ve tahsilat verilerini doğal dille sorgulayıp aksiyon alabilen bir AI agent prototipi.'],
                ['tag' => 'RAPORLAMA', 'title' => 'Doğal Dil Raporlama', 'description' => 'Yöneticilerin serbest metinle soru sorup anlık, doğru rapor aldığı bir veri katmanı.'],
                ['tag' => 'OTOMASYON', 'title' => 'Onay Akışı Otomasyonu', 'description' => 'Manuel onay ve veri girişi süreçlerini uçtan uca otomatikleştiren bir iş akışı denemesi.'],
            ],
        ];
    }

    public static function contactDefaults(): array
    {
        return [
            'seo_title' => 'İletişim | UNITENIST',
            'seo_description' => "ERP'nizde AI için nereden başlamanız gerektiğini birlikte bulalım. UNITENIST ile iletişime geçin.",
            'kicker' => 'İlk adım',
            'title' => "ERP'nizde AI için nereden başlamanız gerektiğini birlikte bulalım.",
            'subtitle' => 'Mevcut sisteminizi, veri akışlarınızı ve otomasyona uygun süreçlerinizi değerlendirelim. Önceliği teknolojiye değil, işletmenizde oluşacak gerçek faydaya verelim.',
            'email' => 'info@unitenist.com',
            'phone' => '',
            'address' => '',
            'linkedin' => 'https://www.linkedin.com/',
        ];
    }

    /**
     * Maps admin section key => Setting storage key.
     */
    protected static function settingKey(string $section): string
    {
        return match ($section) {
            'home' => 'home_content',
            'contact' => 'contact_content',
            default => $section,
        };
    }

    public static function home(): array
    {
        return array_merge(self::homeDefaults(), Setting::get(self::settingKey('home'), []));
    }

    public static function site(): array
    {
        return array_merge(self::siteDefaults(), Setting::get(self::settingKey('site'), []));
    }

    public static function assistant(): array
    {
        return array_merge(self::assistantDefaults(), Setting::get(self::settingKey('assistant'), []));
    }

    public static function homeSkills(): array
    {
        return array_merge(self::homeSkillsDefaults(), Setting::get(self::settingKey('home_skills'), []));
    }

    public static function homeTestimonials(): array
    {
        return array_merge(self::homeTestimonialsDefaults(), Setting::get(self::settingKey('home_testimonials'), []));
    }

    public static function about(): array
    {
        return array_merge(self::aboutDefaults(), Setting::get(self::settingKey('about'), []));
    }

    public static function approach(): array
    {
        return array_merge(self::approachDefaults(), Setting::get(self::settingKey('approach'), []));
    }

    public static function services(): array
    {
        return array_merge(self::servicesDefaults(), Setting::get(self::settingKey('services'), []));
    }

    public static function lab(): array
    {
        return array_merge(self::labDefaults(), Setting::get(self::settingKey('lab'), []));
    }

    public static function contact(): array
    {
        return array_merge(self::contactDefaults(), Setting::get(self::settingKey('contact'), []));
    }

    /**
     * Get merged (defaults + stored) content for an admin-managed section.
     */
    public static function get(string $section): array
    {
        return match ($section) {
            'site' => self::site(),
            'assistant' => self::assistant(),
            'home' => self::home(),
            'home_skills' => self::homeSkills(),
            'home_testimonials' => self::homeTestimonials(),
            'about' => self::about(),
            'approach' => self::approach(),
            'services' => self::services(),
            'lab' => self::lab(),
            'contact' => self::contact(),
            default => [],
        };
    }

    /**
     * Get the raw defaults (no stored overrides) for an admin-managed section.
     */
    public static function defaults(string $section): array
    {
        return match ($section) {
            'site' => self::siteDefaults(),
            'assistant' => self::assistantDefaults(),
            'home' => self::homeDefaults(),
            'home_skills' => self::homeSkillsDefaults(),
            'home_testimonials' => self::homeTestimonialsDefaults(),
            'about' => self::aboutDefaults(),
            'approach' => self::approachDefaults(),
            'services' => self::servicesDefaults(),
            'lab' => self::labDefaults(),
            'contact' => self::contactDefaults(),
            default => [],
        };
    }

    public static function set(string $section, array $value): void
    {
        Setting::set(self::settingKey($section), $value);
    }
}
