<?php
/**
 * Template Name: Moje životní situace
 * Description: Stránka pro nastavení osobní životní situace uživatele.
 *
 * @package minimalistblogger-child
 */

get_header();

// Definuje všechny kategorie a jejich položky podle vašeho zadání
$categories = [
    '👤 Osobní stav' => [
        'rodinny_stav' => [
            'title' => 'Rodinný stav',
            'options' => [
                'osobni_svobodny' => 'Jsem svobodný/á',
                'osobni_manzelstvi' => 'Žiji v manželství',
                'osobni_zasveceny' => 'Jsem zasvěcená osoba',
                'osobni_vdovec' => 'Jsem vdovec/vdova',
                'osobni_rozvedeny' => 'Jsem rozvedený/á',
            ]
        ],
        'rodicovstvi' => [
            'title' => 'Rodičovství',
            'options' => [
                'rodic_male_deti' => 'Mám malé děti (0-6 let)',
                'rodic_skolni_deti' => 'Mám děti ve školním věku (7-15 let)',
                'rodic_dospivajici' => 'Mám dospívající děti (15-20 let)',
                'rodic_dospele_deti' => 'Mám dospělé děti',
                'rodic_cekame_dite' => 'Čekáme dítě',
                'rodic_bez_deti' => 'Nemám děti',
                'rodic_snazime_se' => 'Snažíme se o dítě',
            ]
        ]
    ],
    '⏳ Věková kategorie / Životní etapa' => [
        'zivotni_etapa' => [
            'options' => [
                'etapa_student' => 'Jsem student/ka',
                'etapa_produktivni' => 'Jsem v produktivním věku',
                'etapa_senior' => 'Jsem v důchodu / senior/ka',
                'etapa_zlom' => 'Procházím životním zlomem',
            ]
        ]
    ],
    '❤️ Vztahy' => [
        'vztahy' => [
            'options' => [
                'vztahy_problemy' => 'Prožívám problémy v manželství/partnerství',
                'vztahy_osamelost' => 'Cítím se osamělý/á',
                'vztahy_pece' => 'Pečuji o své rodiče nebo příbuzné',
                'vztahy_pratelstvi' => 'Prožívám radost z nových přátelství',
                'vztahy_konflikty' => 'Trápí mě konflikty v rodině',
                'vztahy_ztrata' => 'Smířuji se se ztrátou blízkého člověka',
            ]
        ]
    ],
    '💪 Výzvy a potíže' => [
        'zdravi' => [
            'title' => 'Zdraví',
            'options' => [
                'vyzvy_zdrav_problemy' => 'Mám zdravotní problémy',
                'vyzvy_zdrav_pece' => 'Pečuji o nemocnou osobu',
                'vyzvy_zdrav_zavislost' => 'Bojuji se závislostí (nebo někdo blízký)',
                'vyzvy_zdrav_psychika' => 'Prožívám psychickou nepohodu (úzkosti, deprese)',
            ]
        ],
        'prace_finance' => [
            'title' => 'Práce a finance',
            'options' => [
                'vyzvy_prace_hledam' => 'Hledám si práci',
                'vyzvy_prace_nespokojenost' => 'Jsem nespokojený/á v práci',
                'vyzvy_prace_finance' => 'Mám finanční potíže',
                'vyzvy_prace_vyhoreni' => 'Prožívám pracovní vyhoření',
            ]
        ],
        'osobni_krize' => [
            'title' => 'Osobní krize',
            'options' => [
                'vyzvy_krize_vira' => 'Procházím krizí víry',
                'vyzvy_krize_smysl' => 'Hledám smysl života',
                'vyzvy_krize_odpusteni' => 'Potýkám se s neodpuštěním',
                'vyzvy_krize_rozhodnuti' => 'Čelím velkému rozhodnutí',
            ]
        ]
    ]
];
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page type-page">
            <header class="entry-header">
                <h1 class="entry-title">Moje životní situace</h1>
            </header>
            <div class="entry-content">
                <p class="settings-intro">Chcete-li, můžete si nastavit oblasti, které nejlépe vystihují vaši životní situaci, hodnoty nebo to, co právě prožíváte. Jak vidíte, tento výběr je zcela anonymní a ukládá se pouze ve vašem zařízení. Pomůže však lépe přizpůsobit denní inspiraci na úvodní stránce, aby více odpovídala tomu, co právě potřebujete.</p>
                
                <div id="life-situation-settings">
                    <?php foreach ($categories as $category_title => $subcategories) : ?>
                        <button class="accordion-btn"><?php echo esc_html($category_title); ?></button>
                        <div class="accordion-content">
                            <?php foreach ($subcategories as $subcategory) : ?>
                                <?php if (!empty($subcategory['title'])) : ?>
                                    <h3 class="subcategory-title"><?php echo esc_html($subcategory['title']); ?></h3>
                                <?php endif; ?>
                                <?php foreach ($subcategory['options'] as $key => $label) : ?>
                                    <div class="setting-item">
                                        <label for="toggle-<?php echo esc_attr($key); ?>" class="setting-label"><?php echo esc_html($label); ?></label>
                                        <label class="switch">
                                            <input type="checkbox" class="situation-toggle" id="toggle-<?php echo esc_attr($key); ?>" data-situation-key="<?php echo esc_attr($key); ?>">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </article>
    </main>
</div>

<?php
get_footer();
?>