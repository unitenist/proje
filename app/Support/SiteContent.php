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
            'questions' => [
                [
                    'topic' => 'Başlangıç noktası',
                    'text' => 'Operasyonlarınızı bugün hangi teknoloji yapısı yönetiyor?',
                    'hint' => 'Mevcut yapıyı bilmek, size sıfırdan kurulum mu yoksa hızlı entegrasyon mu önereceğimizi belirler.',
                    'options' => [
                        ['label' => 'ERP kullanıyoruz', 'value' => 'Aktif ERP altyapısı var', 'next' => 1],
                        ['label' => 'Birden çok araç kullanıyoruz', 'value' => 'Excel, e-ticaret, CRM veya farklı uygulamalar birlikte kullanılıyor', 'next' => 4],
                        ['label' => 'Henüz merkezi sistem yok', 'value' => 'Merkezi operasyon sistemi henüz yok', 'next' => 7],
                    ],
                ],
                [
                    'topic' => 'ERP olgunluğu',
                    'text' => 'ERP veriniz karar almak için ne kadar erişilebilir durumda?',
                    'hint' => 'İyi bir AI katmanı önce doğru, güncel ve yetkili veriye güvenle erişmelidir.',
                    'options' => [
                        ['label' => 'Raporlar hazır ama geç kalıyor', 'value' => 'Standart raporlar var; karar verene geç ulaşıyor', 'next' => 2],
                        ['label' => 'Veri var ama dağınık', 'value' => 'ERP verisi farklı ekranlar ve dosyalara dağılmış', 'next' => 3],
                        ['label' => 'Veri kalitesi belirsiz', 'value' => 'Eksik, mükerrer veya güvenilmez veri riski var', 'next' => 3],
                    ],
                ],
                [
                    'topic' => 'Hedef çıktı',
                    'text' => 'İlk aşamada en değerli sonuç hangisi olurdu?',
                    'hint' => 'Yönetim raporlama ile operasyon yürütme aynı çözüm değildir; başlangıcı doğru seçmek yatırımın geri dönüşünü hızlandırır.',
                    'options' => [
                        ['label' => 'Doğal dilde anlık rapor', 'value' => 'Yöneticiler veriye soru sorarak hızlı karar almak istiyor', 'next' => 9],
                        ['label' => 'Risk ve fırsat uyarıları', 'value' => 'Stok, tahsilat veya satış sapmalarını önceden görmek istiyor', 'next' => 9],
                        ['label' => 'Tekrarlayan işleri devretmek', 'value' => 'Belge, sipariş veya müşteri süreçlerini AI agent ile yürütmek istiyor', 'next' => 6],
                    ],
                ],
                [
                    'topic' => 'Veri hazırlığı',
                    'text' => 'İlk yatırımın odağı konusunda hangi yaklaşım size daha gerçekçi geliyor?',
                    'hint' => 'Hızlı prototip kısa sürede değer gösterir; veri standardizasyonu ise daha kapsamlı ve kalıcı otomasyonun temelidir.',
                    'options' => [
                        ['label' => 'Önce hızlı bir pilot', 'value' => 'Sınırlı bir süreçte ölçülebilir pilot tercih ediliyor', 'next' => 9],
                        ['label' => 'Önce veriyi düzenleyelim', 'value' => 'Veri kalitesi ve entegrasyon temeli önce güçlendirilmeli', 'next' => 8],
                        ['label' => 'İkisini paralel yürütelim', 'value' => 'Pilot ve veri hazırlığı birlikte planlanmalı', 'next' => 8],
                    ],
                ],
                [
                    'topic' => 'Sistemler arası akış',
                    'text' => 'Ekipleriniz arasında en çok hangi kopukluk zaman kaybettiriyor?',
                    'hint' => 'AI, tek bir araca eklenen sohbet kutusundan çok; sistemler arasındaki işi takip eden bir operasyon katmanı olduğunda güçlüdür.',
                    'options' => [
                        ['label' => 'E-ticaret - stok - sipariş', 'value' => 'Satış kanalları ile stok ve sipariş akışı eşzamanlı değil', 'next' => 5],
                        ['label' => 'CRM - satış - teklif', 'value' => 'Müşteri ve satış verisi ekipler arasında parçalı', 'next' => 5],
                        ['label' => 'Finans - tahsilat - yönetim', 'value' => 'Finansal takip ve yönetim görünürlüğü manuel ilerliyor', 'next' => 5],
                    ],
                ],
                [
                    'topic' => 'Öncelik karşılaştırması',
                    'text' => 'Bu sorunu çözerken hangi kazanım daha kritik?',
                    'hint' => 'Maliyet azaltma, hız ve kontrol farklı mimari kararlar gerektirir; çözümü önceliğe göre tasarlarız.',
                    'options' => [
                        ['label' => 'Daha az manuel iş', 'value' => 'Operasyonel iş yükünü azaltmak öncelikli', 'next' => 6],
                        ['label' => 'Daha hızlı karar', 'value' => 'Canlı görünürlük ve karar hızı öncelikli', 'next' => 9],
                        ['label' => 'Daha az hata ve daha çok kontrol', 'value' => 'Onay, izlenebilirlik ve hata azaltma öncelikli', 'next' => 8],
                    ],
                ],
                [
                    'topic' => 'AI agent kapsamı',
                    'text' => 'Bir AI agent hangi yetki seviyesinde başlamalı?',
                    'hint' => 'Öneri üreten asistan ile işlem yapan agent arasında güvenlik, onay ve denetim tasarımı farklıdır.',
                    'options' => [
                        ['label' => 'Sadece öneri ve taslak', 'value' => 'AI yalnızca analiz, öneri ve taslak üretmeli', 'next' => 8],
                        ['label' => 'İnsan onayıyla işlem', 'value' => 'AI işlem hazırlamalı; insan onayı sonrası çalışmalı', 'next' => 8],
                        ['label' => 'Kurallı işlemleri otomatik yapsın', 'value' => 'Net kurallı düşük riskli işlemler otomatik yürütülebilir', 'next' => 8],
                    ],
                ],
                [
                    'topic' => 'Yeni mimari',
                    'text' => 'Yeni bir sistem kurarken önceliğiniz hangisi olurdu?',
                    'hint' => 'Başlangıçta modüler kurgu, ileride ERP, e-ticaret ve AI agent ihtiyaçları değiştiğinde daha düşük maliyetli büyüme sağlar.',
                    'options' => [
                        ['label' => 'Hızlı devreye alma', 'value' => 'Kısa sürede çalışan çekirdek süreçler hedefleniyor', 'next' => 9],
                        ['label' => 'Ölçeklenebilir altyapı', 'value' => 'Büyümeye ve yeni kanallara hazır mimari hedefleniyor', 'next' => 8],
                        ['label' => 'Önce süreçleri netleştirme', 'value' => 'Teknoloji seçmeden önce süreç tasarımı yapılmalı', 'next' => 9],
                    ],
                ],
                [
                    'topic' => 'Güven ve yönetişim',
                    'text' => 'AI çözümünde hangi kontrol mekanizması vazgeçilmez?',
                    'hint' => 'Rol bazlı erişim, kaynak gösterme, onay akışı ve işlem kayıtları birlikte değerlendirildiğinde AI güvenle ölçeklenir.',
                    'options' => [
                        ['label' => 'Rol bazlı veri erişimi', 'value' => 'Her kullanıcının yalnızca yetkili veriyi görmesi gerekli', 'next' => 9],
                        ['label' => 'Onay ve işlem kayıtları', 'value' => 'AI kararlarının ve işlemlerinin izlenebilir olması gerekli', 'next' => 9],
                        ['label' => 'KVKK ve veri yerleşimi', 'value' => 'Kişisel veri, saklama ve model kullanım sınırları öncelikli', 'next' => 9],
                    ],
                ],
                [
                    'topic' => 'Önerilen sonraki adım',
                    'text' => 'Yanıtlarınıza göre en doğru başlangıç, hedef süreç için kısa bir keşif ve karşılaştırmalı çözüm taslağı hazırlamak. Görüşme planlayalım mı?',
                    'hint' => 'Görüşmede mevcut akışı, entegrasyon seçeneklerini, beklenen faydayı ve ilk pilot kapsamını birlikte netleştiririz.',
                    'options' => [
                        ['label' => 'Evet, görüşelim', 'value' => 'Keşif görüşmesi talep ediyor', 'next' => -1, 'interest' => true],
                        ['label' => 'Önce bir çözüm özeti gönderin', 'value' => 'Karşılaştırmalı çözüm özeti talep ediyor', 'next' => -1, 'interest' => true],
                        ['label' => 'Şimdilik araştırıyorum', 'value' => 'Henüz değerlendirme aşamasında', 'next' => -1],
                    ],
                ],
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
