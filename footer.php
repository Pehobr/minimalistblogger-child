<?php
/**
 * The template for displaying the footer
 * takže další pokus a co teď?
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

<div id="settings-overlay" class="settings-overlay"></div>
<div id="settings-panel" class="settings-panel">
    <div class="settings-header">
        <h2>Nastavení</h2>
        <button id="settings-close-btn" class="settings-close-btn" aria-label="Zavřít nastavení">&times;</button>
    </div>
    <div class="settings-content">
        <div class="setting-item">
            <label for="toggle-player">Zobrazit hlavní přehrávač</label>
            <label class="switch">
                <input type="checkbox" id="toggle-player">
                <span class="slider round"></span>
            </label>
        </div>
        </div>
</div>

<nav class="bottom-nav-bar">
    <ul>
        <li>
            <a href="/oblibene" aria-label="Oblíbené">
                <i class="fa fa-star" aria-hidden="true"></i>
                <span>Oblíbené</span>
            </a>
        </li>
        <li>
            <a href="/plaminek" aria-label="Zamyšlení">
                <i class="fa fa-fire" aria-hidden="true"></i>
                <span>Zamyšlení</span>
            </a>
        </li>
        <li>
            <a href="/" class="active" aria-label="Domů">
                <i class="fa fa-home" aria-hidden="true"></i>
                <span>Domů</span>
            </a>
        </li>
        <li>
            <a href="/liturgicke-cteni/" aria-label="Biblické texty">
                <i class="fa fa-book" aria-hidden="true"></i>
                <span>Text</span>
            </a>
        </li>
        <li>
            <a href="/informace" aria-label="Informace">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <span>Info</span>
            </a>
        </li>
    </ul>
</nav>

</body>
</html>