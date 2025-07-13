<?php
/**
 * Template Name: Nastavení pravého menu
 * Description: Umožňuje uživatelům skrýt/zobrazit položky v pravém mobilním menu.
 *
 * @package minimalistblogger-child
 */

get_header();

// Zde definujeme všechny položky, které chceme, aby si uživatel mohl přizpůsobit.
// Klíč (např. 'oblibene-texty') by měl odpovídat části URL dané stránky.
$menu_items = [
    'oblibene-texty'    => 'Oblíbené texty',
    'archiv-citatu'     => 'Archiv citátů',
    'zapisnik'          => 'Můj zápisník',
    'poboznosti'        => 'Pobožnosti',
    'denni-modlitba'    => 'Modlitba', // Doplněno dle požadavku
    'video-kapky'       => 'Video kapky',
    'fotogalerie'       => 'Fotogalerie',
    'postni-pisne'      => 'Postní písně',
    'krestanska-radia'  => 'Internetová rádia',
    'podcast'           => 'Podcast',
];

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Nastavení pravého menu</h1>
            </header>
            <div class="entry-content">
                <p class="settings-intro">Zde si můžete přizpůsobit, které položky se mají zobrazovat v pravém vysouvacím menu (ikona <i class="fa fa-bars"></i> vpravo nahoře). Pokud jste si přidali některou z těchto stránek do spodní lišty, můžete ji zde skrýt, abyste neměli odkazy duplicitně.</p>
                <div id="right-menu-settings">
                    <?php foreach ($menu_items as $slug => $name) : ?>
                        <div class="setting-item">
                            <label for="toggle-<?php echo esc_attr($slug); ?>" class="setting-label"><?php echo esc_html($name); ?></label>
                            <label class="switch">
                                <input type="checkbox" class="visibility-toggle" id="toggle-<?php echo esc_attr($slug); ?>" data-menu-slug="<?php echo esc_attr($slug); ?>">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>