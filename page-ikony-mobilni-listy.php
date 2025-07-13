<?php
/**
 * Template Name: Ikony mobilní lišty
 * Description: Umožňuje uživatelům nastavit si ikony ve spodní mobilní liště.
 *
 * @package minimalistblogger-child
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page">
            <header class="entry-header">
                <h1 class="entry-title">Nastavení ikon mobilní lišty</h1>
            </header>
            <div class="entry-content">
                <div id="mobile-nav-settings">

                    <?php
                    // Pole s definicemi všech dostupných možností
                    $icon_options = [
                        ['value' => 'oblibene', 'icon' => 'fa-star', 'label' => 'Oblíbené texty', 'url' => home_url('/oblibene-texty/')],
                        ['value' => 'archiv', 'icon' => 'fa-folder-open-o', 'label' => 'Archiv citátů', 'url' => home_url('/archiv-citatu/')],
                        ['value' => 'zapisnik', 'icon' => 'fa-pencil', 'label' => 'Můj zápisník', 'url' => home_url('/zapisnik/')],
                        ['value' => 'video', 'icon' => 'fa-video-camera', 'label' => 'Video kapky', 'url' => home_url('/video-kapky/')],
                        ['value' => 'fotogalerie', 'icon' => 'fa-picture-o', 'label' => 'Fotogalerie', 'url' => home_url('/fotogalerie/')],
                        ['value' => 'pisne', 'icon' => 'fa-music', 'label' => 'Postní písně', 'url' => home_url('/postni-pisne/')],
                        ['value' => 'radia', 'icon' => 'fa-headphones', 'label' => 'Internetová rádia', 'url' => home_url('/krestanska-radia/')],
                        ['value' => 'podcast', 'icon' => 'fa-podcast', 'label' => 'Podcast', 'url' => home_url('/podcast/')],
                        ['value' => 'modlitba', 'icon' => 'fa-commenting', 'label' => 'Modlitba', 'url' => home_url('/')],
                        ['value' => 'poboznost', 'icon' => 'fa-book', 'label' => 'Pobožnost', 'url' => home_url('/poboznosti/')],
                    ];

                    // Definice tlačítek (pozic), které chceme zobrazit
                    $positions = [
                        1 => '1. Ikona (vlevo)',
                        2 => '2. Ikona',
                        4 => '4. Ikona',
                        5 => '5. Ikona (vpravo)',
                    ];

                    foreach ($positions as $pos_num => $pos_label) :
                    ?>
                        <button class="accordion-btn"><?php echo $pos_label; ?></button>
                        <div class="accordion-content">
                            <p class="settings-description">Vyberte, co se má zobrazit na <?php echo str_replace(['Ikona', '(vlevo)', '(vpravo)'], ['pozici', '', ''], $pos_label); ?> v liště.</p>
                            <div class="setting-item" data-nav-position="<?php echo $pos_num; ?>">
                                
                                <?php foreach ($icon_options as $option) : ?>
                                <label class="radio-label">
                                    <input type="radio" 
                                           name="nav-icon-<?php echo $pos_num; ?>" 
                                           value="<?php echo esc_attr($option['value']); ?>" 
                                           data-icon="<?php echo esc_attr($option['icon']); ?>" 
                                           data-url="<?php echo esc_url($option['url']); ?>">
                                    <i class="fa <?php echo esc_attr($option['icon']); ?>"></i> <?php echo esc_html($option['label']); ?>
                                </label>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    <?php 
                    endforeach; 
                    ?>

                    <div class="middle-icon-info">
                        <p>
                            <strong><i class="fa fa-home"></i> 3. Ikona (Domů)</strong>
                            Prostřední ikona je pevně nastavena a vždy směřuje na úvodní stránku, abyste se nikdy neztratili.
                        </p>
                    </div>

                </div>
            </div>
        </article>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>