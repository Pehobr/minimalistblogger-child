<?php
/**
 * Template Name: Inspirace AI
 * Description: Stránka pro generování osobní inspirace pomocí AI.
 */

get_header();
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <article class="page type-page">
            <header class="entry-header">
                <h1 class="entry-title">Inspirace pro dnešní den</h1>
            </header>
            <div class="entry-content">
                
                <div id="ai-inspiration-container">
                    <div id="daily-scripture-display">
                        <h2>Dnešní Boží slovo</h2>
                        <div class="scripture-content">
                            <p><em>Načítám denní čtení...</em></p>
                            </div>
                    </div>

                    <button id="generate-inspiration-btn">Vygenerovat moji inspiraci</button>
                    
                    <div id="inspiration-result" style="display:none;">
                        <h2>Vaše osobní inspirace</h2>
                        <div class="loader" style="display:none;"></div>
                        <div class="inspiration-text"></div>
                    </div>
                </div>
            </div>
        </article>
    </main>
</div>
<?php
get_footer();
?>