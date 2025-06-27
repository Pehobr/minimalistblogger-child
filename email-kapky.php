<?php

/**
 * Funkce pro vygenerování HTML obsahu denní kapky.
 *
 * @param string $target_date Datum ve formátu 'Y-m-d' (např. '2025-06-27').
 * @return string|null Vrátí kompletní HTML e-mailu, nebo null, pokud kapka pro daný den nebyla nalezena.
 */
function generate_daily_email_content( $target_date ) {

    // Najdeme příspěvek typu 'email_kapky', jehož titulek přesně odpovídá cílovému datu.
    $args = array(
        'post_type'      => 'email_kapky',
        'posts_per_page' => 1,
        'title'          => $target_date,
        'post_status'    => 'publish',
    );

    $query = new WP_Query( $args );

    // Pokud jsme nenašli žádnou kapku pro daný den, funkci ukončíme.
    if ( ! $query->have_posts() ) {
        return null;
    }

    $query->the_post(); // Nastaví globální $post objekt

    // 1. ZÍSKÁNÍ DAT Z WORDPRESSU
    // =================================
    $post_id = get_the_ID();
    $modlitba = get_the_content(); // Obsah z hlavního editoru
    $modlitba = apply_filters('the_content', $modlitba); // Aplikuje formátování (např. odstavce)

    $citat_1 = get_post_meta( $post_id, '_citat_1', true );
    $autor_1 = get_post_meta( $post_id, '_autor_1', true );
    $citat_2 = get_post_meta( $post_id, '_citat_2', true );
    $autor_2 = get_post_meta( $post_id, '_autor_2', true );

    wp_reset_postdata(); // Obnovíme původní data globálního dotazu

    // 2. VYTVOŘENÍ HTML ŠABLONY
    // =================================
    // Zde je vložena HTML šablona e-mailu. Data se vkládají pomocí proměnných.
    $html_template = <<<HTML
<!DOCTYPE html>
<html lang="cs" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
  <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <style>
      td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
  <![endif]-->
  <title>Denní duchovní impuls</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" media="screen">
  <style>
    .text-2xl { font-size: 24px; line-height: 32px; }
    .text-xl { font-size: 20px; line-height: 28px; }
    .text-base { font-size: 16px; line-height: 24px; }
    .text-sm { font-size: 14px; line-height: 20px; }
    .font-bold { font-weight: 700; }
    .p-6 { padding: 24px; }
    .px-6 { padding-left: 24px; padding-right: 24px; }
    .mb-4 { margin-bottom: 16px; }
    .text-center { text-align: center; }
    .hover-underline:hover { text-decoration: underline !important; }
    @media (max-width: 600px) {
      .sm-w-full { width: 100% !important; }
      .sm-px-6 { padding-left: 24px !important; padding-right: 24px !important; }
      .sm-py-8 { padding-top: 32px !important; padding-bottom: 32px !important; }
    }
  </style>
</head>
<body style="margin: 0; padding: 0; width: 100%; word-break: break-word; -webkit-font-smoothing: antialiased; background-color: #F2EADF;">
  <div role="article" aria-roledescription="email" aria-label="Denní duchovní impuls" lang="cs">
    <table style="width: 100%; font-family: Inter, sans-serif;" cellpadding="0" cellspacing="0" role="presentation">
      <tr>
        <td align="center" class="sm-py-8" style="padding-top: 48px; padding-bottom: 48px; background-color: #F2EADF;">
          <table class="sm-w-full" style="width: 600px;" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td class="sm-px-6" style="border-radius: 12px; background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);">
                <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td class="p-6" style="text-align: center;">
                       <h1 class="text-2xl font-bold" style="margin: 0; color: #40302D;">Duchovní Impulsy</h1>
                       <p style="margin: 4px 0 0; color: #F27457;">{$target_date}</p>
                    </td>
                  </tr>
                  <tr><td class="px-6"><div style="height: 1px; background-color: #E0DACE;"></div></td></tr>
                  <tr>
                    <td class="p-6">
                      <h2 class="text-xl font-bold mb-4" style="margin: 0; color: #00A6A6;">🙏 Denní modlitba</h2>
                      <div class="text-base" style="color: #40302D;">{$modlitba}</div>
                    </td>
                  </tr>
                  <tr><td class="px-6"><div style="height: 1px; background-color: #E0DACE;"></div></td></tr>
                  <tr>
                    <td class="p-6">
                      <h2 class="text-xl font-bold mb-4" style="margin: 0; color: #F27457;">💬 Citáty papežů</h2>
                      <div style="margin-bottom: 24px;">
                        <p class="text-base" style="margin: 0; color: #40302D; font-style: italic;">"{$citat_1}"</p>
                        <p class="text-sm" style="margin: 8px 0 0; color: #40302D; font-weight: 700; text-align: right;">- {$autor_1}</p>
                      </div>
                      <div>
                        <p class="text-base" style="margin: 0; color: #40302D; font-style: italic;">"{$citat_2}"</p>
                        <p class="text-sm" style="margin: 8px 0 0; color: #40302D; font-weight: 700; text-align: right;">- {$autor_2}</p>
                      </div>
                    </td>
                  </tr>
                  <tr><td class="px-6"><div style="height: 1px; background-color: #E0DACE;"></div></td></tr>
                  <tr>
                    <td class="p-6 text-center text-sm" style="color: #6B7280;">
                      <p style="margin: 0 0 16px;">
                        Navštivte náš web pro více inspirace:<br>
                        <a href="https://example.com" class="hover-underline" style="color: #00A6A6; text-decoration: none;">www.vasweb.cz</a>
                      </p>
                      <p style="margin: 0;">
                        Tento e-mail jste obdrželi, protože jste se přihlásili k odběru.<br>
                        Pokud si ho nepřejete nadále dostávat, můžete se <a href="#" class="hover-underline" style="color: #6B7280;">odhlásit zde</a>.
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
HTML;

    return $html_template;
}
