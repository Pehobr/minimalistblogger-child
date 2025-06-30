<?php
/**
 * Šablona pro HTML e-mail odesílaný přes Ecomail.
 * Používá proměnné, které jsou jí předány z odesílací funkce.
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo esc_html($subject); ?></title>
</head>
<body style="margin:0; padding:0; font-family: sans-serif; background-color: #f4f4f4;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff;">
        <tr>
            <td align="center" style="padding: 20px 0 30px 0;">
                <img src="URL_VASEHO_LOGA_NEBO_BANNERU" alt="Postní kapky" width="300" style="display: block;" />
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 30px;">
                <h1 style="font-size: 24px; margin: 0;"><?php echo esc_html($subject); ?></h1>
                <hr style="margin: 20px 0;">

                <?php if (!empty($image_url)): ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="" style="width: 100%; max-width: 100%; height: auto; margin-bottom: 20px;">
                <?php endif; ?>

                <h2 style="font-size: 20px;">Papež Jan Pavel II.</h2>
                <p style="font-size: 16px; line-height: 1.5;"><?php echo nl2br(esc_html($jpii_quote)); ?></p>

                <h2 style="font-size: 20px;">Papež Benedikt XVI.</h2>
                <p style="font-size: 16px; line-height: 1.5;"><?php echo nl2br(esc_html($benedict_quote)); ?></p>

                <h2 style="font-size: 20px;">Papež František</h2>
                <p style="font-size: 16px; line-height: 1.5;"><?php echo nl2br(esc_html($francis_quote)); ?></p>

                <h2 style="font-size: 20px;">Papež Lev XIII.</h2>
                <p style="font-size: 16px; line-height: 1.5;"><?php echo nl2br(esc_html($leo_quote)); ?></p>

                <h2 style="font-size: 20px;">Sv. Augustin</h2>
                <p style="font-size: 16px; line-height: 1.5;"><?php echo nl2br(esc_html($augustine_quote)); ?></p>

                <h2 style="font-size: 20px;">Modlitba</h2>
                <p style="font-size: 16px; line-height: 1.5;"><?php echo nl2br(esc_html($prayer)); ?></p>
            </td>
        </tr>
        <tr style="background-color: #e6e0f3;">
    <td style="padding: 30px 30px;">
        <p style="margin: 0; text-align: center;">
            Nedaří se vám e-mail správně zobrazit? <a href="<?php echo esc_url($web_view_url); ?>">Zobrazte si jej v prohlížeči.</a>
        </p>
        <p style="margin: 20px 0 0 0; text-align: center;">
            <a href="<?php echo home_url('/'); ?>">Přejít na web aplikace</a>
        </p>
        <p style="font-size: 12px; color: #555555; text-align: center; margin-top: 20px;">
            Tento e-mail jste obdrželi, protože jste se přihlásili k odběru.
            <br>
            Přejete si odběr zrušit? <a href="*|UNSUB|*" style="color: #555555;">Odhlásit zde.</a>
        </p>
    </td>
</tr>
    </table>
</body>
</html>