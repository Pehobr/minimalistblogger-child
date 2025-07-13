<?php
/**
 * The template for displaying the footer
 * Zkouška oddělení
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package minimalistblogger
 */

?>
</div>
</div><footer id="colophon" class="site-footer clearfix">

    <div class="content-wrap">
        <?php if ( is_active_sidebar( 'footerwidget-1' ) ) : ?>
        <div class="footer-column-wrapper">
            <div class="footer-column-three footer-column-left">
                <?php dynamic_sidebar( 'footerwidget-1' ); ?>
            </div>
        <?php endif; ?>

        <?php if ( is_active_sidebar( 'footerwidget-2' ) ) : ?>
        <div class="footer-column-three footer-column-middle">
            <?php dynamic_sidebar( 'footerwidget-2' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( is_active_sidebar( 'footerwidget-3' ) ) : ?>
    <div class="footer-column-three footer-column-right">
        <?php dynamic_sidebar( 'footerwidget-3' ); ?>
    </div>
<?php endif; ?>

</div>

<div class="site-info">
    <?php echo esc_html('&copy;', 'minimalistblogger') ?> <?php echo esc_html(date('Y')); ?> <?php bloginfo( 'name' ); ?>
    <span class="footer-info-right">
        <?php echo esc_html_e(' | Powered by', 'minimalistblogger') ?> <a href="<?php echo esc_url('https://superbthemes.com/minimalistblogger/'); ?>" rel="nofollow noopener"><?php echo esc_html_e('Minimalist Blog', 'minimalistblogger') ?></a> <?php echo esc_html_e('WordPress Theme', 'minimalistblogger') ?>
    </span>
    </div></div>



</footer>
</div>
<div id="smobile-menu" class="mobile-only"></div>
<div id="mobile-menu-overlay"></div>

<?php wp_footer(); ?>

<?php
// Zobrazení panelů pouze tam, kde jsou potřeba
if ( is_page_template(array('page-liturgicke-cteni.php', 'page-poboznosti.php', 'page-home.php', 'page-radio.php')) ) :
?>
    <div id="settings-overlay" class="settings-overlay"></div>
    <div id="settings-panel" class="settings-panel">
        <div class="settings-header">
            <h2>Nastavení</h2>
            <button id="settings-close-btn" class="settings-close-btn" aria-label="Zavřít nastavení">&times;</button>
        </div>
        <div class="settings-content">
            <?php // Zde se přidává obecné nastavení, které může být na více stránkách ?>
            <div class="setting-item">
                <label for="toggle-player">Skrýt hlavní přehrávač</label>
                <label class="switch">
                    <input type="checkbox" id="toggle-player">
                    <span class="slider round"></span>
                </label>
            </div>

            <?php // Specifická nastavení pro Pobožnosti se vloží dynamicky pomocí JS ?>
        </div>
    </div>

    <?php if ( is_page_template('page-radio.php') ) : ?>
        <div id="add-radio-overlay" class="settings-overlay"></div>
        <div id="add-radio-panel" class="settings-panel">
            <div class="settings-header">
                <h2>Přidat vlastní rádio</h2>
                <button id="add-radio-close-btn" class="settings-close-btn" aria-label="Zavřít">&times;</button>
            </div>
            <div class="settings-content">
                <div class="add-radio-form">
                    <div class="form-group">
                        <label for="custom-radio-name">Název rádia:</label>
                        <input type="text" id="custom-radio-name" placeholder="Např. Rádio 7">
                    </div>
                    <div class="form-group">
                        <label for="custom-radio-stream">URL adresa streamu:</label>
                        <input type="url" id="custom-radio-stream" placeholder="https://icecast.proglas.cz/radio7-128.mp3">
                    </div>
                    <button id="save-custom-radio-btn" class="save-btn">Uložit rádio</button>
                </div>
                <div id="custom-radio-list-container">
                        <h3>Moje rádia</h3>
                        <ul id="custom-radio-list">
                            <?php /* Seznam uživatelských rádií se sem vloží pomocí JavaScriptu */ ?>
                        </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>


<nav class="bottom-nav-bar">
    <ul>
        <li>
            <a href="<?php echo esc_url( home_url('/oblibene-texty/') ); ?>" aria-label="Oblíbené">
                <i class="fa fa-star" aria-hidden="true"></i>
                <span>Oblíbené</span>
            </a>
        </li>

        <li>
            <a href="<?php echo esc_url( home_url('/archiv-citatu/') ); ?>" aria-label="Archiv">
                <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                <span>Archiv</span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="active" aria-label="Domů">
                <i class="fa fa-home" aria-hidden="true"></i>
                <span>Domů</span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( home_url('/fotogalerie/') ); ?>" aria-label="Podcast">
                <i class="fa fa-picture-o" aria-hidden="true"></i>
                <span>Podcast</span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( home_url('/zapisnik/') ); ?>" aria-label="Zápisník">
                <i class="fa fa-pencil" aria-hidden="true"></i>
                <span>Zápisník</span>
            </a>
        </li>
    </ul>
</nav>

<div id="left-mobile-menu-overlay" class="mobile-menu-overlay"></div>
<div id="left-mobile-menu-panel" class="mobile-menu-panel">
    <div class="mobile-menu-header">
        <h2>Menu</h2>
        <button id="left-mobile-menu-close-btn" class="mobile-menu-close-btn">&times;</button>
    </div>
    <div class="mobile-menu-content">
        <?php
        if ( has_nav_menu( 'left_mobile_menu' ) ) {
            wp_nav_menu(
                array(
                    'theme_location' => 'left_mobile_menu',
                    'container'      => false,
                    'menu_class'     => 'mobile-menu-ul',
                )
            );
        } else {
            echo '<p>Vytvořte a přiřaďte menu k pozici "Levé mobilní menu".</p>';
        }
        ?>
    </div>
</div>

<div id="mobile-menu-main-overlay" class="mobile-menu-overlay"></div>
<div id="mobile-menu-panel" class="mobile-menu-panel">
    <div class="mobile-menu-header">
        <h2>Menu aplikace</h2>
        <button id="mobile-menu-close-btn" class="mobile-menu-close-btn">&times;</button>
    </div>
    <div class="mobile-menu-content">
        <?php
        if ( has_nav_menu( 'mobile_extra_menu' ) ) {
            wp_nav_menu(
                array(
                    'theme_location' => 'mobile_extra_menu',
                    'container'      => false,
                    'menu_class'     => 'mobile-menu-ul',
                )
            );
        } else {
            echo '<p>Vytvořte a přiřaďte menu k pozici "Pravé mobilní menu".</p>';
        }
        ?>
    </div>
</div>

<script type="text/javascript" id="apply-mobile-nav-settings">
document.addEventListener('DOMContentLoaded', function() {
    const navSettingsKey = 'pehobr_mobile_nav_settings';
    const navBar = document.querySelector('.bottom-nav-bar');

    if (navBar) {
        try {
            const savedSettings = JSON.parse(localStorage.getItem(navSettingsKey)) || {};
            for (const [position, settings] of Object.entries(savedSettings)) {
                // Najdeme odkaz a ikonu podle pozice (začínáme od 1)
                const linkElement = navBar.querySelector(`li:nth-child(${position}) a`);
                const iconElement = linkElement ? linkElement.querySelector('i') : null;

                if (linkElement && iconElement) {
                    // Nastavíme novou URL adresu a třídu ikony
                    linkElement.setAttribute('href', settings.url);
                    iconElement.className = 'fa ' + settings.icon;
                }
            }
        } catch (e) {
            console.error('Chyba při parsování nastavení navigace:', e);
        }
    }
});
</script>

</body>
</html>